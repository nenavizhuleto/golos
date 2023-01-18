<?php

namespace App\Controllers;

class Users extends BaseController {
  public function postLogin() {
  	$login = $this->request->getPost('login');
  	$password = $this->request->getPost('password');

    $userModel = model('App\Models\UserModel');
    $result = $userModel->login($login, $password);

		return json_encode([
			'result' => $result
		]);
  }

  public function postLogout() {  	
    $userModel = model('App\Models\UserModel');
    $userModel->logout();

		return json_encode([
			'result' => true
		]);
  }
}
