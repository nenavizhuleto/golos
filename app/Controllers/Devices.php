<?php

namespace App\Controllers;

class Devices extends BaseController {
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
  	$limit = $this->request->getGet('limit');
  	$page = $this->request->getGet('page');
  	$filters = $this->request->getGet('filters');

  	if (null !== $id)
  		$id = (int)$id;

  	$limit = (int)$limit;
  	$page = (int)$page;

  	// wheres
  	$wheres = [];

  	if ($id)
  		$wheres['device.id'] = $id;

  	if (is_array($filters)) {
  		if (array_key_exists('comment', $filters))
  			if (trim($filters['comment']))
		  		$wheres['device.comment'] = trim($filters['comment']);
		}

		$wheres['device.type !='] = 9;


  	// orderings
  	$orderings = [
  		'COALESCE(porch_street.title, room_street.title)' => 'asc',
  		'COALESCE(porch_building.num, room_building.num)' => 'asc',
  		'COALESCE(porch.num, room_porch.num)' => 'asc',
  		'room.num' => 'asc',
  		'type = 5' => 'asc',
  		'type = 1' => 'asc',
  		'num' => 'asc'
  	];

    $deviceModel = model('App\Models\DeviceModel');
    // TODO: bla-bla-bla about extra=1
    $result = $deviceModel->getExtra($wheres, $orderings, $limit, $page);
    foreach ($result->rows as &$row) {
    	switch ($row['type']) {
    		case 1:
    			$row['type_name'] = 'подъезд, многоабонентская панель';
    			break;

    		case 2:
    			$row['type_name'] = 'калитка, многоабонентская панель';
    			break;

    		case 3:
    			$row['type_name'] = 'ворота, одноабонентская панель';
    			break;

    		case 4:
    			$row['type_name'] = 'зарезирвировано 4';
    			break;

    		case 5:
    			$row['type_name'] = 'абонент, консьерж';
    			break;

    		case 6:
    			$row['type_name'] = 'абонент, трубка';
    			break;

    		case 7:
    			$row['type_name'] = 'абонент, видеомонитор';
    			break;

    		case 8:
    			$row['type_name'] = 'абонент, вызывная панель';
    			break;

    		case 9:
    			$row['type_name'] = 'SIP-клиент приложения';
    			break;

    		default:
    			$row['type_name'] = '';
    	}

    	if ($row['block_id'])
    		$row['objtype'] = 'Двор';

    	if ($row['porch_id'])
    		$row['objtype'] = 'Подъезд';

    	if ($row['room_id'])
    		$row['objtype'] = 'Квартира';
    }

		return json_encode([
			'result' => true
		] + (array)$result);
  }

  function postSave() {
  	// TODO: handle PHONE
  	// TODO: handle COMMENT

  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$id = $this->request->getPost('id');
  	$block_id = $this->request->getPost('block_id');
  	$porch_id = $this->request->getPost('porch_id');
  	$room_id = $this->request->getPost('room_id');
  	$type = $this->request->getPost('type');
  	$sippassword = $this->request->getPost('sippassword');
  	$comment = $this->request->getPost('comment');

  	if (null !== $id)
  		$id = (int)$id;

  	if (null !== $block_id)
  		$block_id = (int)$block_id;

  	if (null !== $porch_id)
  		$porch_id = (int)$porch_id;

  	if (null !== $room_id)
  		$room_id = (int)$room_id;

  	if (1 != (null !== $block_id) + (null !== $porch_id) + (null !== $room_id))
	    return json_encode([
	    	'result' => false,
      	'message' => 'Неверный запрос.'
	    ]);

		if (null === $type)
	    return json_encode([
	    	'result' => false,
      	'message' => 'Неверный запрос.'
	    ]);

		$type = (int)$type;

		if (null !== $sippassword)
			$sippassword = trim($sippassword);

		if (null !== $comment)
			$comment = trim($comment);

		// building data
		// TODO: produce method's comment-based splicing to every model
  	$data = [
  		'block_id' => $block_id,
  		'porch_id' => $porch_id,
  		'room_id' => $room_id,
  		'type' => $type
  	];

  	if (null !== $id)
  		$data['sippassword'] = $sippassword;

  	if (null !== $comment)
  		$data['comment'] = $comment;

  	// saving
		$deviceModel = model('App\Models\DeviceModel');
  	if (null !== $id) {
  		// updating
  		try {
	  		$deviceModel->update($id, $data);
			} catch (\Exception $e) {
				// TODO: produce this short message everywhere
		    return json_encode([
		    	'result' => false,
	      	'message' => "Ошибка обновления устройства #$id."
		    ]);
			}

			// TODO: produce this short message everywhere
	    return json_encode([
	    	'result' => true,
      	'message' => "Устройство #$id обновлено."
	    ]);
  	}

  	// inserting
  	// TODO: every INSERT should return ID
  	try {
	  	$id = $deviceModel->insert($data, true); // false or insertID
	 	} catch (\Exception $e) {
	 		switch ($e->getMessage()) {
	 			case 'NO_FREE_NUM':
	 				$message = 'Не удалось найти свободный порядковый номер устройства в пределах его объекта.';
		 			break;

		 		default:
		    	$message = 'Ошибка создания устройства.';
			 		break;
	 		}

	    return json_encode([
	    	'result' => false,
	    	'message' => $message
	    ]);
	 	}

    return json_encode([
    	'result' => true,
    	'message' => "Создано устройство #$id.",
    	'id' => $id
    ]);
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
    $deviceModel = model('App\Models\DeviceModel');
  	$result = [
  		'result' => false
  	];

  	try {
  		$deviceModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: невозможно удалить некоторые устройства.';
		}

    return json_encode($result);
  }
}
