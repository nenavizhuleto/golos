<?php

namespace App\Controllers;

class Rooms extends BaseController {
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
  	$porch_id = $this->request->getGet('porch_id');
  	$limit = $this->request->getGet('limit');
  	$page = $this->request->getGet('page');

  	if (null !== $id)
  		$id = (int)$id;

  	if (null !== $porch_id)
  		$porch_id = (int)$porch_id;

  	$limit = (int)$limit;
  	$page = (int)$page;

  	// wheres
  	$wheres = [];
  	if ($id)
  		$wheres['room.id'] = $id;

  	if ($porch_id)
  		$wheres['room.porch_id'] = $porch_id;

    $roomModel = model('App\Models\RoomModel');
    // TODO: add extra=1 param for getWithStreetBuildingAndPorch instead of get
    $result = $roomModel->getExtra($wheres, [], $limit, $page);

		return json_encode([
			'result' => true
		] + (array)$result);
  }

  public function postGenerate() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$porch_id = $this->request->getPost('porch_id');
  	$rooms_from = $this->request->getPost('rooms_from');
  	$rooms_to = $this->request->getPost('rooms_to');
  	$mobile_device = $this->request->getPost('mobile_device');

  	if (
  		(null === $porch_id) ||
  		(null === $rooms_from) ||
  		(null === $rooms_to)
		)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    $porch_id = (int)$porch_id;
    $rooms_from = (int)$rooms_from;
    $rooms_to = (int)$rooms_to;
    $mobile_device = 'true' == $mobile_device;

    if (
    	($porch_id <= 0) ||
    	($rooms_from <= 0) ||
    	($rooms_to <= 0) ||
    	($rooms_from > $rooms_to)
  	)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    // generation
    $roomModel = model('App\Models\RoomModel');
    for ($room = $rooms_from; $room <= $rooms_to; $room++)
		  $roomModel->insert([
		  	'porch_id' => $porch_id,
		  	'num' => $room,
		  	'mobile_device' => $mobile_device
	  	]);

    return json_encode([
    	'result' => true
    ]);
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
  	$porch_id = $this->request->getPost('porch_id');
  	$num = $this->request->getPost('num');
  	$mobile_device = $this->request->getPost('mobile_device');
  	$mobile_device_password = $this->request->getPost('mobile_device_password');

  	if (
  		(null === $porch_id) ||
  		(null === $num)
  	)
      return json_encode([
      	'result' => false
      ]);

  	$porch_id = (int)$porch_id;
  	$num = (int)$num;
  	$mobile_device = 'true' == $mobile_device;
  	$mobile_device_password = trim($mobile_device_password);

  	$data = [
			'porch_id' => $porch_id,
			'num' => $num,
			'mobile_device' => $mobile_device,
			'mobile_device_password' => $mobile_device_password
		];

    $roomModel = model('App\Models\RoomModel');
  	if (null === $id) {
  		// inserting
  		$id = $roomModel->insert($data);

	    return json_encode([
	    	'result' => true,
	    	'message' => "Создана квартира #$id.",
	    	'id' => $id
	    ]);
  	} else {
  		// updating
			$id = (int)$id;
  		$roomModel->update($id, $data);

	    return json_encode([
	    	'result' => true,
	    	'message' => "Квартира #$id обновлена."
	    ]);
  	}
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

  	if (null === $ids)
      return json_encode([
      	'result' => false
      ]);

    if (!is_array($ids))
      return json_encode([
      	'result' => false
      ]);

    foreach ($ids as &$id)
    	$id = (int)$id;

   	// deleting
    $roomModel = model('App\Models\RoomModel');

  	$result = [
  		'result' => false
  	];

  	try {
  		$roomModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: некоторые квартиры содержат устройства. ' . $e->getMessage();
		}

    return json_encode($result);
  }
}
