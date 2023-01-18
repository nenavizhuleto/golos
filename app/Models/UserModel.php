<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
  protected $table = 'user';
  protected $primaryKey = 'id';

	function login($login, $password) {
		$user = $this->where([
			'login' => $login,
			'password' => $password
		])->first();

		if (!$user)
			return false;

		$user = (object)$user;

		$session = \Config\Services::session();
    $session->set('user', $user);

    return true;
	}

	function logout() {
		$session = \Config\Services::session();
    $session->remove('user');

    return true;
	}

	function getCurrent() {
		$session = \Config\Services::session();
    $user = $session->get('user');
    if (!$user) {
    	return false;
    }

		return $user;
	}
}
