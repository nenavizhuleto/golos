<?php

namespace App\Models;

use CodeIgniter\Model;

class Block extends Model {
  protected $table = 'block';
  protected $primaryKey = 'id';

  protected $allowedFields = ['code', 'num', 'name'];

	protected $afterFind = [ 'onAfterFind' ];

	// events
	protected function onAfterFindRow(&$row) {
		$row['id'] = (int)$row['id'];	
		$row['code'] = sprintf('%02d', $row['id']);
	}

	// TODO: best practice, must propogate to all models
	protected function onAfterFind($data) {
		if ($data['singleton']) {
			$this->onAfterFindRow($data['data']);

			return $data;
		}

		foreach ($data['data'] as &$row) {
			$this->onAfterFindRow($row);
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

  	return (object)[
  		'rows' => $this->findAll()
  	];
  }

  function getExtra($wheres = [], $orderings = [], $limit = null, $page = null) {
  	$result = $this->get($wheres, $orderings, $limit, $page);

    $blockBuildingModel = model('App\Models\BlockBuildingModel');
  	foreach ($result->rows as &$row) {
			$row['buildings'] = $blockBuildingModel->getExtra(
				[
					'block_id' => $row['id']
				], [
					'street_title' => 'asc',
					'building_num' => 'asc',
				]
			)->rows;

    	$buildings = [];
    	foreach ($row['buildings'] as $building) {
    		$buildings[] = "{$building['street_title']}, {$building['building_num']}";
    	}

    	$row['buildings_string'] = implode('; ', $buildings);
  	}
	  
  	return $result;
  }
}
