<?php

namespace App\Models;

use CodeIgniter\Model;

class PsAuthModel extends Model {
	protected $DBGroup = 'asterisk';

  protected $table = 'ps_auths';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = false;

  protected $allowedFields = [
  	'id',
  	'auth_type',
  	'nonce_lifetime',
  	'md5_cred',
  	'password',
  	'realm',
  	'username'
	];
}
