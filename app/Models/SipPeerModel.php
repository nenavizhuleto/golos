<?php

namespace App\Models;

use CodeIgniter\Model;

class SipPeerModel extends Model {
	protected $DBGroup = 'asterisk';

  protected $table = 'sippeers';
  protected $primaryKey = 'id';

  protected $allowedFields = [
  	'name',
  	'context',
  	'secret'
  ];

  protected $afterFind = [ 'onAfterFind' ];

	protected function onAfterFindRow(&$row) {
		$row['id'] = (int)$row['id'];	
	}

	protected function onAfterFind($data) {
		if ('findAll' == $data['method'])
			foreach ($data['data'] as &$row)
				$this->onAfterFindRow($row);

		if ('find' == $data['method'])
  		$this->onAfterFindRow($data['data']);

		return $data;
	}
}
