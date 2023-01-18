<?php

namespace App\Models;

use CodeIgniter\Model;

class PorchModel extends Model {
  protected $table = 'porch';
  protected $primaryKey = 'id';

  protected $allowedFields = ['building_id', 'num', 'concierge_device_id'];

	protected $afterFind = [ 'onAfterFind' ];
	protected $afterInsert = [ 'onAfterInsert' ];
	protected $beforeDelete = [ 'onBeforeDelete' ];

	protected function onAfterFindRow(&$row) {
		$row['id'] = (int)$row['id'];	

		if (null !== $row['building_id'])
			$row['building_id'] = (int)$row['building_id'];	

		$row['num'] = (int)$row['num'];	

		if (null !== $row['concierge_device_id'])
			$row['concierge_device_id'] = (int)$row['concierge_device_id'];	

		// derivatives
		$row['code'] = sprintf('%02d', $row['num']);
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
    $deviceModel = model('App\Models\DeviceModel');

    $building = $buildingModel->find($data['data']['building_id']);
    $street = $streetModel->find($building['street_id']);

    // inserting new intercom device
		$deviceModel->insert([
			'porch_id' => $data['id'],
			'type' => 1,
			'num' => 1,
			'comment' => "домофон, {$street['title']} {$building['num']}, под. {$data['data']['num']}"
		]);

    // inserting new concierge device
		$deviceModel->insert([
			'porch_id' => $data['id'],
			'type' => 5,
			'num' => 1,
			'comment' => "консьерж, {$street['title']} {$building['num']}, под. {$data['data']['num']}"
		]);
	}

	function onBeforeDelete($data) {
		if (!count($data['id']))
			return $data;

    $deviceModel = model('App\Models\DeviceModel');
    foreach ($data['id'] as $porch_id) {
	    $devices = $deviceModel
	    	->where('porch_id', $porch_id)
	    	->findAll();

			foreach ($devices as $device)
				$deviceModel->delete($device['id']);
  	}

		return $data;
	}


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
  		porch.*,
  		street.id AS street_id,
  		street.title AS street_title,
  		building.num AS building_num');
  	$this->join('building', 'building.id = porch.building_id', 'left');
  	$this->join('street', 'street.id = building.street_id', 'left');

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
}
