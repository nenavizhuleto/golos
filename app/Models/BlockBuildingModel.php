<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockBuildingModel extends Model {
  protected $table = 'block_building';
  //protected $primaryKey = 'id';

  protected $allowedFields = ['block_id', 'building_id'];

  function get($wheres = [], $orderings = [], $limit = null, $page = null) {
  	if (is_array($wheres))
  		$this->where($wheres);

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir);

  	return (object)[
  		'rows' => $this->findAll()
  	];
  }

  function getExtra($wheres = [], $orderings = [], $limit = null, $page = null) {
  	$this->select('
  		block_building.*,
  		street.code AS street_code,
  		street.title AS street_title,
  		building.id AS building_id,
  		building.code AS building_code,
  		building.num AS building_num
		');
  	$this->join('building', 'building.id = block_building.building_id', 'inner');
  	$this->join('street', 'street.id = building.street_id', 'inner');

  	if (is_array($wheres))
  		$this->where($wheres);

  	if (is_array($orderings))
  		foreach ($orderings as $field => $dir)
	  		$this->orderBy($field, $dir);

	  /*
		SELECT
		block.*,
		street.title AS street_title,
		building.num AS building_num
		FROM block
		LEFT JOIN block_building ON block_building.block_id = block.id
		INNER JOIN building ON building.id = block_building.building_id
		INNER JOIN street ON street.id = building.street_id;
		*/

  	return (object)[
  		'rows' => $this->findAll()
  	];
  }
}
