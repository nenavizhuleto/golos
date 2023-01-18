<?php

namespace App\Models;

use CodeIgniter\Model;

class PsAorModel extends Model {
	protected $DBGroup = 'asterisk';

  protected $table = 'ps_aors';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = false;

  protected $allowedFields = [
		'id',
		'contact',
		'default_expiration',
		'mailboxes',
		'max_contacts',
		'minimum_expiration',
		'remove_existing',
		'qualify_frequency',
		'authenticate_qualify',
		'maximum_expiration',
		'outbound_proxy',
		'support_path',
		'qualify_timeout',
		'voicemail_extension'
	];
}
