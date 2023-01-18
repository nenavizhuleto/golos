<?php

namespace App\Controllers;

class Porches extends BaseController {
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
  	$building_id = $this->request->getGet('building_id');

  	if (null !== $id)
  		$id = (int)$id;

  	if (null !== $building_id)
  		$building_id = (int)$building_id;

  	// wheres
  	$wheres = [];
  	if (null !== $id)
  		$wheres['porch.id'] = $id;

  	if (null !== $building_id)
  		$wheres['building_id'] = $building_id;

  	// orderings
  	$orderings = [
  		'street_title' => 'asc',
  		'building_num' => 'asc',
  		'num' => 'asc'
  	];

		//
    $porchModel = model('App\Models\PorchModel');
    // TODO: add extra=1 param for getWithStreetAndBuilding instead of get
    $result = $porchModel->getExtra($wheres, $orderings, null, null);

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
  	$building_id = $this->request->getPost('building_id');
  	$num = trim($this->request->getPost('num'));

    if (null !== $id)
    	$id = (int)$id;

   	$building_id = (int)$building_id;
   	$num = (int)$num;

  	if (!$building_id || !$num)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    //
  	$data = [
			'building_id' => $building_id,
			'num' => $num
		];

  	$result = [
  		'result' => false
  	];

    $porchModel = model('App\Models\PorchModel');
  	if (null !== $id) {
  		// updating
  		try {
	  		$porchModel->update($id, $data);
		  	$result['result'] = true;
	  		$result['message'] = 'Обновлен подъезд #' . $id . '.';
			} catch (\Exception $e) {
		  	$result['message'] = "Ошибка: " . $e->getMessage() . '.';
	  	}

	    return json_encode($result);
		}
  		
		// inserting
		try {
  		$porchModel->insert($data);
		} catch (\Exception $e) {
	  	$result['message'] = "Ошибка: " . $e->getMessage() . '.';
			return json_encode($result);
  	}

  	$result['result'] = true;
		$result['message'] = 'Создан подъезд #' . $porchModel->getInsertID() . '.';

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
    $porchModel = model('App\Models\PorchModel');
  	$result = [
  		'result' => false
  	];

  	try {
  		$porchModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: некоторые подъезды содержат квартиры.';
		}

    return json_encode($result);
  }
}
