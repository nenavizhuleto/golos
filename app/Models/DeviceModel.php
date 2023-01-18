<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceModel extends Model {
  protected $table = 'device';
  protected $primaryKey = 'id';

  protected $allowedFields = [
  	'sippeer_id',
  	'ps_endpoint_id',
  	'phone',
  	'sipusername',
  	'sippassword',
  	'block_id',
  	'porch_id',
  	'room_id',
  	'type',
  	'num',
  	'comment'
	];

	protected $afterFind = [ 'onAfterFind' ];
	protected $beforeInsert = [ 'onBeforeInsert' ];
	protected $afterInsert = [ 'onAfterInsert' ];
	protected $afterUpdate = [ 'onAfterUpdate' ];
	protected $beforeDelete = [ 'onBeforeDelete' ];
	
	// events
	protected function onAfterFindRow(&$row) {
		$row['id'] = (int)$row['id'];	

		if (null !== $row['sippeer_id'])
			$row['sippeer_id'] = (int)$row['sippeer_id'];	

		// ps_endpoint_id is string, skipping

		if (null !== $row['block_id'])
			$row['block_id'] = (int)$row['block_id'];	

		if (null !== $row['porch_id'])
			$row['porch_id'] = (int)$row['porch_id'];	

		if (null !== $row['room_id'])
			$row['room_id'] = (int)$row['room_id'];	
	}

	protected function onAfterFind($data) {
		if ('findAll' == $data['method'])
			foreach ($data['data'] as &$row)
				$this->onAfterFindRow($row);

		if ('find' == $data['method'])
  		$this->onAfterFindRow($data['data']);

		return $data;
	}

	protected function onBeforeInsert($data) {
		// TODO: what 'bout many phone numbers of concierges at one porch?
		// TODO: what 'bout deleting device which is concirrge linked to porch?

		// looking up for free NUM (not guaranteed)
		$data['data']['num'] = null;

		$num = 0;
		while (++$num <= 99) {
			if (isset($data['data']['block_id']))
				if (null === $row = $this
					->where([
						'block_id' => $data['data']['block_id'],
						'num' => $num
					])
					->first()) {
						$data['data']['num'] = $num;
						break;
					}

			if (isset($data['data']['porch_id']))
				if (null === $row = $this
					->where([
						'porch_id' => $data['data']['porch_id'],
						'num' => $num
					])
					->first()) {
						$data['data']['num'] = $num;
						break;
					}

			if (isset($data['data']['room_id']))
				if (null === $row = $this
					->where([
						'room_id' => $data['data']['room_id'],
						'num' => $num
					])
					->first()) {
						$data['data']['num'] = $num;
						break;
					}
		}

		if (null === $data['data']['num'])
			throw new \Exception('NO_FREE_NUM');

		// generating phone number
		$data['data']['phone'] = $this->generatePhone($data['data']);

		return $data;
	}

	protected function onAfterInsert($data) {
		if (!$data['result'])
			return;

		if (!array_key_exists('block_id', $data['data']))
			$data['data']['block_id'] = null;

		if (!array_key_exists('porch_id', $data['data']))
			$data['data']['porch_id'] = null;

		if (!array_key_exists('room_id', $data['data']))
			$data['data']['room_id'] = null;

		// generating peer data
		if (9 == $data['data']['type']) {
			// PJSIP-client
			$sipusername = $data['data']['phone'];
		} else {
			// regular SIP-client
			$sipusername = sprintf('%06d', $data['id']);
		}

		$sippassword = substr(md5($sipusername . md5('Ccydvbb3qkc@')), 0, 8);

		$context = $this->generateSipContext(
			$data['data']['block_id'],
			$data['data']['porch_id'],
			$data['data']['room_id']
		);

		// creating peer
		if (9 == $data['data']['type']) {
			// PJSIP mobile client
	    $psAorModel = model('App\Models\PsAorModel');
	    $psAuthModel = model('App\Models\PsAuthModel');
	    $psEndpointModel = model('App\Models\PsEndpointModel');

	    // AOR
	    $psAorModel->insert([
	    	'id' => $sipusername,
	    	'max_contacts' => 10
	    ]);

	    // Auth
	    $psAuthModel->insert([
	    	'id' => $sipusername,
	    	'auth_type' => 'userpass',
	    	'password' => $sippassword,
	    	'username' => $sipusername
	    ]);

	    // Endpoint
	    $psEndpointModel->insert([
	    	'id' => $sipusername,
        'aors' => $sipusername,
        'auth' => $sipusername,
        'context' => $context
	    ]);

		 	// updating device data
			$this->update($data['id'], [
				'ps_endpoint_id' => $sipusername,
				'sipusername' => $sipusername,
				'sippassword' => $sippassword
			]);
		} else {
			// regular SIP client
	    $sipPeerModel = model('App\Models\SipPeerModel');

			$sippeer_id = $sipPeerModel->insert([
				'name' => $sipusername,
				'context' => $context,
				'secret' => $sippassword
			]);

		 	// updating device data
			$this->update($data['id'], [
				'sippeer_id' => $sippeer_id,
				'sipusername' => $sipusername,
				'sippassword' => $sippassword
			]);
    }
	}

	protected function onAfterUpdate($data) {
		$sipPeerModel = model('App\Models\SipPeerModel');
    $psEndpointModel = model('App\Models\PsEndpointModel');
    $psAuthModel = model('App\Models\PsAuthModel');

		// regenerating context
		if (!isset($data['data']['block_id']))
			$data['data']['block_id'] = null;

		if (!isset($data['data']['porch_id']))
			$data['data']['porch_id'] = null;

		if (!isset($data['data']['room_id']))
			$data['data']['room_id'] = null;

		$context = $this->generateSipContext(
			$data['data']['block_id'],
			$data['data']['porch_id'],
			$data['data']['room_id']
		);

		// updating each device
		foreach ($data['id'] as $id) {
			$device = $this->find($id);
			if (!$device)
				continue;

			if (null !== $device['sippeer_id']) {
				// regular SIP client
	    	$fields = [];

	    	if (null !== $context)
	    		$fields['context'] = $context;

				if (array_key_exists('sippassword', $data['data']))
					$fields['secret'] = $data['data']['sippassword'];

		    $sipPeerModel->update($device['sippeer_id'], $fields);
    	}

			if (null !== $device['ps_endpoint_id']) {
				// PJSIP mobile client
				if (array_key_exists('sippassword', $data['data'])) {
					$endpoint = $psEndpointModel->find($device['ps_endpoint_id']);

					if ($endpoint) {
						$psAuthModel->update($endpoint['auth'], [
							'password' => $data['data']['sippassword']
						]);
					}
				}

				if (null != $context)
					$psEndpointModel->update($device['ps_endpoint_id'], [
						'context' => $context
					]);
			}
		}
  }

	protected function onBeforeDelete($data) {
    $sipPeerModel = model('App\Models\SipPeerModel');
    $psAorModel = model('App\Models\PsAorModel');
    $psAuthModel = model('App\Models\PsAuthModel');
    $psEndpointModel = model('App\Models\PsEndpointModel');

    foreach ($data['id'] as $id) {
    	$device = $this->find($id);

			if (null !== $device['sippeer_id'])
				$sipPeerModel->delete($device['sippeer_id']);

			if (null !== $device['ps_endpoint_id']) {
		    $psEndpointModel->delete($device['ps_endpoint_id']);
		    $psAorModel->delete($device['ps_endpoint_id']);
		    $psAuthModel->delete($device['ps_endpoint_id']);
			}
    }
	}

	// helpers
	function generateSipContext($block_id = null, $porch_id = null, $room_id = null) {
		if (null !== $block_id) {
	    $blockModel = model('App\Models\BlockModel');

	    $block = $blockModel->find($block_id);

			return "block-out-{$block['code']}";
		}

		if (null !== $porch_id) {
	    $porchModel = model('App\Models\PorchModel');
	    $buildingModel = model('App\Models\BuildingModel');
	    $streetModel = model('App\Models\StreetModel');

	    $porch = $porchModel->find($porch_id);
	    $building = $buildingModel->find($porch['building_id']);
	    $street = $streetModel->find($building['street_id']);

	    return "room-out-{$street['code']}{$building['code']}{$porch['code']}";
		}

		if (null !== $room_id) {
	    $roomModel = model('App\Models\RoomModel');
	    $porchModel = model('App\Models\PorchModel');
	    $buildingModel = model('App\Models\BuildingModel');
	    $streetModel = model('App\Models\StreetModel');

	    $room = $roomModel->find($room_id);
	    $porch = $porchModel->find($room['porch_id']);
	    $building = $buildingModel->find($porch['building_id']);
	    $street = $streetModel->find($building['street_id']);

	    return "room-out-{$street['code']}{$building['code']}{$porch['code']}";
		}

		return null;
	}

	function generatePhone($row) {
		$roomModel = model('App\Models\RoomModel');
		$porchModel = model('App\Models\PorchModel');
		$buildingModel = model('App\Models\BuildingModel');
		$streetModel = model('App\Models\StreetModel');
		$blockModel = model('App\Models\BlockModel');

		$result = '';
		if (isset($row['block_id'])) {
		/*
			$block = $blockModel->find($row['block_id']);
//			$porch = $porchModel->find($room['porch_id']);
//			$building = $buildingModel->find($porch['building_id']);
//			$street = $streetModel->find($building['street_id']);

			$result = '';

			// block code
			$result .= $block['code'];

			return $result;
			*/
		}

		if (isset($row['porch_id'])) {
			$porch = $porchModel->find($row['porch_id']);
			$building = $buildingModel->find($porch['building_id']);
			$street = $streetModel->find($building['street_id']);

			// street code
			$result .= $street['code'];

			// building code
			$result .= $building['code'];

			// porch code
			$result .= $porch['code'];

			// type
			$result .= substr((string)$row['type'], -1);

			// num
			$result .= substr((string)$row['num'], -1);

			return $result;
		}

		if (isset($row['room_id'])) {
			$room = $roomModel->find($row['room_id']);
			$porch = $porchModel->find($room['porch_id']);
			$building = $buildingModel->find($porch['building_id']);
			$street = $streetModel->find($building['street_id']);

			// street code
			$result .= $street['code'];

			// building code
			$result .= $building['code'];

			// room code
			$result .= $room['code'];

			// type
			$result .= substr((string)$row['type'], -1);

			// num
			$result .= substr((string)$row['num'], -1);

			return $result;
		}

		return '';
	}

	// custom methods
  function get($wheres = [], $orderings = [], $limit = null, $page = null) {
  	if (is_array($wheres))
  		foreach ($wheres as $cond => $value) {
				if ('device.comment' == $cond) {
					$this->like($cond, $value);
					continue;
				}

	  		$this->where($cond, $value);
	  	}

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir);

  	if ($limit) {
  		if (!$page)
  			$page = 1;

  		$rows = $this->paginate($limit, 'rows', $page);

	  	return (object)[
	  		'rows' => $rows,
	  		'total' => $this->pager->getTotal('rows'),
	  		'limit' => $limit,
	  		'page' => $page,
	  		'pageCount' => $this->pager->getPageCount('rows')
	  	];
  	}

  	$rows = $this->findAll();

  	return (object)[
  		'rows' => $rows
  	];
  }

  function getExtra($wheres = [], $orderings = [], $limit = null, $page = null) {
  	$this->select('
  		device.*,
  		porch_street.id AS porch_street_id,
  		porch_building.id AS porch_building_id,
  		room_street.id AS room_street_id,
  		room_building.id AS room_building_id,
  		room_porch.id AS room_porch_id
  	');
  	$this->join('porch', 'porch.id = device.porch_id', 'left');
  	$this->join('building AS porch_building', 'porch_building.id = porch.building_id', 'left');
  	$this->join('street AS porch_street', 'porch_street.id = porch_building.street_id', 'left');
  	$this->join('room', 'room.id = device.room_id', 'left');
  	$this->join('porch AS room_porch', 'room_porch.id = room.porch_id', 'left');
  	$this->join('building AS room_building', 'room_building.id = room_porch.building_id', 'left');
  	$this->join('street AS room_street', 'room_street.id = room_building.street_id', 'left');

  	if (is_array($wheres))
  		foreach ($wheres as $cond => $value) {
				if ('device.comment' == $cond) {
					$this->like($cond, $value);
					continue;
				}

	  		$this->where($cond, $value);
	  	}

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir, false);

  	if ($limit) {
  		if (!$page)
  			$page = 1;

  		$rows = $this->paginate($limit, 'rows', $page);

	  	return (object)[
	  		'rows' => $rows,
	  		'total' => $this->pager->getTotal('rows'),
	  		'limit' => $limit,
	  		'page' => $page,
	  		'pageCount' => $this->pager->getPageCount('rows')
	  	];
  	}

  	$rows = $this->findAll();

  	return (object)[
  		'rows' => $rows
  	];
  }
}
