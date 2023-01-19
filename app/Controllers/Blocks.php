<?php

namespace App\Controllers;

class Blocks extends BaseController {
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
  		'num' => 'asc'
  	];

  	//
    $blockModel = model('App\Models\BlockModel');
    // TODO: add extra=1 param for getExtra instead of get
    $result = $blockModel->getExtra($wheres, $orderings, null, null);

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
  	$num = trim($this->request->getPost('num'));
	$name = trim($this->request->getPost('name'));

    if (null !== $id)
    	$id = (int)$id;

  	if (!$num)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

  	$data = [
			'num' => $num,
			'name' => $name
		];

    $blockModel = model('App\Models\BlockModel');
  	$result = [
  		'result' => false
  	];

  	if (null === $id) {
  		// inserting
  		try {
	  		$blockModel->insert($data);
		  	$result['result'] = true;
	  		$result['message'] = 'Создан двор #' . $blockModel->insertID() . '.';
			} catch (\Exception $e) {
		  	$result['message'] = "Ошибка: " . $e->getMessage() . '.';
	  	}
  	} else {
  		// updating
  		try {
	  		$blockModel->update($id, $data);
		  	$result['result'] = true;
	  		$result['message'] = 'Обновлена двор #' . $id . '.';
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
    $blockModel = model('App\Models\BlockModel');
  	$result = [
  		'result' => false
  	];

  	try {
  		$blockModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: некоторые дворы содержат дома.';
		}

    return json_encode($result);
  }
}
