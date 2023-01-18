<?php

namespace App\Models;

use CodeIgniter\Model;

class BuildingModel extends Model {
  protected $table = 'building';
  protected $primaryKey = 'id';

  protected $allowedFields = ['street_id', 'code', 'num'];

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

  function getWithStreet($wheres = [], $orderings = [], $limit = null, $page = null) {
  	$this->select('building.*, street.title AS street_title, block_building.block_id as block_id');
  	$this->join('street', 'street.id = building.street_id', 'left');
	$this->join('block_building', 'block_building.building_id = building.id', 'left');

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

  function getWithRoomsAndMobiles($ids) {
		$ids_string = implode(', ', $ids);

  	$result = $this->query("
  		SELECT
  			`street`.`title` AS `street_title`,
  			`building`.`num` AS `building_num`,
  			`room`.`num` AS `room_num`,
  			`asterisk`.`ps_auths`.`username` AS `username`,
  			`asterisk`.`ps_auths`.`password` AS `password`
  		FROM `room`
  		INNER JOIN `porch` ON `porch`.`id` = `room`.`porch_id`
  		INNER JOIN `building` ON `building`.`id` = `porch`.`building_id`
  		INNER JOIN `street` ON `street`.`id` = `building`.`street_id`
  		INNER JOIN `device` ON `device`.`id` = `room`.`mobile_device_id`
  		LEFT JOIN `asterisk`.`ps_endpoints` ON `asterisk`.`ps_endpoints`.`id` = `device`.`ps_endpoint_id`
  		LEFT JOIN `asterisk`.`ps_auths` ON `asterisk`.`ps_auths`.`id` = `asterisk`.`ps_endpoints`.`auth`
  		WHERE `building`.`id` IN ($ids_string)
  		ORDER BY
  			`street`.`title`,
  			`building`.`num`,
  			`room`.`num`
  	");

  	return $result->getResult();
  }
}
