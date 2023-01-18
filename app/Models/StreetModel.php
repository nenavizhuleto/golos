<?php

namespace App\Models;

use CodeIgniter\Model;

class StreetModel extends Model {
  protected $table = 'street';
  protected $primaryKey = 'id';

  protected $allowedFields = ['code', 'title'];

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
}
