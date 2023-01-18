<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model {
  protected $table = 'room';
  protected $primaryKey = 'id';

  protected $allowedFields = [
  	'porch_id',
  	'num',
  	'mobile_device_id'
	];

  // TODO: add onAfterFind typecasts everywhere
	protected $afterFind = [ 'onAfterFind' ];
	//protected $beforeUpdate = [ 'onBeforeUpdate' ];
	protected $afterInsert = [ 'onAfterInsert' ];
	protected $beforeDelete = [ 'onBeforeDelete' ];

	// events
	protected function onAfterFindRow(&$row) {
		if (!$row)
			return;

		$row['id'] = (int)$row['id'];	
		$row['porch_id'] = (int)$row['porch_id'];	
		$row['num'] = (int)$row['num'];	

		if (null !== $row['mobile_device_id'])
			$row['mobile_device_id'] = (int)$row['mobile_device_id'];	

		$row['code'] = sprintf('%03d', $row['num']);
	}

	protected function onAfterFind($data) {
		if ('findAll' == $data['method'])
			foreach ($data['data'] as &$row)
				$this->onAfterFindRow($row);

		if ('find' == $data['method'])
  		$this->onAfterFindRow($data['data']);

		return $data;
	}

	protected function onAfterInsert($data) {
		if (!$data['result'])
			return;

    $streetModel = model('App\Models\StreetModel');
    $buildingModel = model('App\Models\BuildingModel');
    $porchModel = model('App\Models\PorchModel');
    $deviceModel = model('App\Models\DeviceModel');

    $porch = $porchModel->find($data['data']['porch_id']);
    $building = $buildingModel->find($porch['building_id']);
    $street = $streetModel->find($building['street_id']);

    // inserting new customer device
		$deviceModel->insert([
			'room_id' => $data['id'],
			'type' => 6,
			'num' => 1,
			'comment' => "абонентская трубка №1, {$street['title']} {$building['num']}, кв. {$data['data']['num']}"
		]);
	}

	function onBeforeDelete($data) {
		if (!count($data['id']))
			return $data;

    $deviceModel = model('App\Models\DeviceModel');
    foreach ($data['id'] as $room_id) {
	    $devices = $deviceModel
	    	->where('room_id', $room_id)
	    	->findAll();

			foreach ($devices as $device)
				$deviceModel->delete($device['id']);
  	}

		return $data;
	}

	// custom methods
  function get($wheres = [], $orderings = [], $limit = null, $page = null) {
  	if (is_array($wheres))
  		$this->where($wheres);

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir);

  	if ($limit) {
  		if (!$page)
  			$page = 1;

	  	return (object)[
	  		'rows' => $this->paginate($limit, 'rows', $page),
	  		'total' => $this->pager->getTotal('rows'),
	  		'limit' => $limit,
	  		'page' => $page,
	  		'pageCount' => $this->pager->getPageCount('rows')
	  	];
  	}

  	return (object)[
  		'rows' => $this->findAll()
  	];
  }

  function getExtra($wheres = [], $orderings = [], $limit = null, $page = null) {
  	$this->select('
  		room.*,
  		street.id AS street_id,
  		street.title AS street_title,
  		building.id AS building_id,
  		building.num AS building_num,
  		porch.num AS porch_num,
  		device.sipusername AS device_sipusername,
  		device.sippassword AS device_sippassword
  	');
  	$this->join('porch', 'porch.id = room.porch_id', 'left');
  	$this->join('building', 'building.id = porch.building_id', 'left');
  	$this->join('street', 'street.id = building.street_id', 'left');
  	$this->join('device', 'device.id = room.mobile_device_id', 'left');

  	if (is_array($wheres))
  		$this->where($wheres);

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir);
		
  	if ($limit) {
  		if (!$page)
  			$page = 1;

	  	return (object)[
	  		'rows' => $this->paginate($limit, 'rows', $page),
	  		'total' => $this->pager->getTotal('rows'),
	  		'limit' => $limit,
	  		'page' => $page,
	  		'pageCount' => $this->pager->getPageCount('rows')
	  	];
  	}

  	return (object)[
  		'rows' => $this->findAll()
  	];
  }

	public function update($id = NULL, $data = NULL) : bool {
		$deviceModel = model('App\Models\DeviceModel');

		// messing around SIP-application
		if (array_key_exists('mobile_device', $data)) {
			$mobile_device = $data['mobile_device'];
			unset($data['mobile_device']);

			$mobile_device_password = $data['mobile_device_password'];
			unset($data['mobile_device_password']);
			$data['sippassword'] = $mobile_device_password;

			// reading existing room
			$room = $this->find($id);
			if (null === $room)
				return false;

			if (null !== $room['mobile_device_id']) {
				// there was old mobile_device_id
				if ($mobile_device) {
					// and now we shound update it's password
					$deviceModel->update($room['mobile_device_id'], [
						'sippassword' => $mobile_device_password
					]);
				} else {
					// and now we shound delete it
					$deviceModel->delete($room['mobile_device_id']);
					$data['mobile_device_id'] = NULL;
				}
			} else {
				// there wasn't mobile_device_id
				if ($mobile_device) {
					// and now we shound create it
					$deviceModel->insert([
						'room_id' => $id,
						'type' => 9,
						'num' => 1
					]);

					$data['mobile_device_id'] = $deviceModel->getInsertID();
				}
			}
		}

		return parent::update($id, $data);
  }

  public function insert($data = null, bool $returnID = true) {
		$deviceModel = model('App\Models\DeviceModel');

  	$id = parent::insert($data, true);

		if ($data['mobile_device']) {
			// inserting new mobile device
			$device_id = $deviceModel->insert([
				'room_id' => $id,
				'type' => 9,
				'num' => 1
			]);

	    $this->update($id, [
	    	'mobile_device_id' => $device_id
	    ]);
    }

    return $id;
  }
}
