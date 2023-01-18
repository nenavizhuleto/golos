<?php

namespace App\Controllers;

class Streets extends BaseController {
  public function getIndex() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$id = $this->request->getGet('id');

  	if (null !== $id)
  		$id = (int)$id;

  	// wheres
  	$wheres = [];
  	if (null !== $id)
  		$wheres['id'] = $id;

  	// orderings
  	$orderings = [
  		'code' => 'asc',
  		'title' => 'asc'
  	];

  	//
    $streetModel = model('App\Models\StreetModel');
    $result = $streetModel->get($wheres, $orderings, null, null);

		return json_encode([
			'result' => true
		] + (array)$result);
  }

  function postSave() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$id = $this->request->getPost('id');
  	$code = trim($this->request->getPost('code'));
  	$title = trim($this->request->getPost('title'));

    if (null !== $id)
    	$id = (int)$id;

  	if (!$title)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    while (strlen($code) < 3)
    	$code = "0$code";

    //
  	$data = [
			'code' => $code,
			'title' => $title
		];

    $streetModel = model('App\Models\StreetModel');
  	$result = [
  		'result' => false
  	];

  	if (null === $id) {
  		// inserting
  		try {
	  		$streetModel->insert($data);
		  	$result['result'] = true;
	  		$result['message'] = 'Создана улица #' . $streetModel->insertID() . '.';
			} catch (\Exception $e) {
		  	$result['message'] = "Ошибка: " . $e->getMessage() . '.';
	  	}
  	} else {
  		// updating
  		try {
	  		$streetModel->update($id, $data);
		  	$result['result'] = true;
	  		$result['message'] = 'Обновлена улица #' . $id . '.';
			} catch (\Exception $e) {
		  	$result['message'] = "Ошибка: " . $e->getMessage() . '.';
	  	}
  	}

    return json_encode($result);
  }

  function postDelete() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$ids = $this->request->getPost('ids');

  	if (!is_array($ids))
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    foreach ($ids as &$id)
    	$id = (int)$id;

   	//
    $streetModel = model('App\Models\StreetModel');
  	$result = [
  		'result' => false
  	];

  	try {
  		$streetModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: некоторые улицы содержат дома.';
		}

    return json_encode($result);
  }
}
