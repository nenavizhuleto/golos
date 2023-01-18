<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Buildings extends BaseController {
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
  	$street_id = $this->request->getGet('street_id');
  	$limit = $this->request->getGet('limit');
  	$page = $this->request->getGet('page');

  	if (null !== $id)
  		$id = (int)$id;

  	if (null !== $street_id)
  		$street_id = (int)$street_id;

  	$limit = (int)$limit;
  	$page = (int)$page;

  	// wheres
  	$wheres = [];
  	if (null !== $id)
  		$wheres['building.id'] = $id;

  	if (null !== $street_id)
  		$wheres['building.street_id'] = $street_id;

  	// orderings
  	$orderings = [
  		'street_title' => 'asc',
  		'code' => 'asc'
  	];

    $buildingModel = model('App\Models\BuildingModel');
    // TODO: add extra=1 param for getWithStreet instead of get
    $result = $buildingModel->getWithStreet($wheres, $orderings, $limit, $page);

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
  	$street_id = $this->request->getPost('street_id');
	$block_id = $this->request->getPost('block_id');
  	$num = $this->request->getPost('num');

    if (null !== $id)
    	$id = (int)$id;

   	$street_id = (int)$street_id;
	$block_id = (int)$block_id;
   	$num = (int)$num;

  	if (!$street_id || !$num)
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    $code = "$num";
    while (strlen($code) < 3)
    	$code = "0$code";

    if (strlen($code) < 4)
    	$code = "{$code}0";

    //
  	$data = [
			'street_id' => $street_id,
			'code' => $code,
			'num' => $num
		];

    $buildingModel = model('App\Models\BuildingModel');
	$blockBuildingModel = model('App\Models\BlockBuildingModel');  	
	$result = [
  		'result' => false
  	];

  	if (null === $id) {
  		// inserting
  		try {
	  		$buildingModel->insert($data);
			$buildingInsertID = $buildingModel->insertID();
			$blockBuildingModel->insert(['block_id' => $block_id, 'building_id' => $buildingInsertID ]);
		  	$result['result'] = true;
	  		$result['message'] = 'Создан дом #' . $buildingInsertID . '.';
			} catch (\Exception $e) {
		  	$result['message'] = "Ошибка: " . $block_id . $e->getMessage() . '.';
	  	}
  	} else {
  		// updating
  		try {
	  		$buildingModel->update($id, $data);
			$blockBuildingModel->where('building_id', $id)->set(['block_id', $block_id])->update(); 
		  	$result['result'] = true;
	  		$result['message'] = 'Обновлен дом #' . $id . '.';
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
    $buildingModel = model('App\Models\BuildingModel');
  	$result = [
  		'result' => false
  	];

  	try {
  		$buildingModel->delete($ids);
			$result['result'] = true;
			$result['message'] = 'Удалено.';
		} catch (\Exception $e) {
			$result['message'] = 'Ошибка: некоторые дома содержат подъезды.';
		}

    return json_encode($result);
  }

  function getExport() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    // input validation & typecasting
  	$format = strtolower($this->request->getGet('format'));
  	if (!in_array($format, [ 'csv', 'excel' ]))
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

  	$ids = $this->request->getGet('ids');
  	if (!is_array($ids))
      return json_encode([
      	'result' => false,
      	'message' => 'Неверный запрос.'
      ]);

    foreach ($ids as &$id)
    	$id = (int)$id;

    // exporting
    $buildingModel = model('App\Models\BuildingModel');
    $data = $buildingModel->getWithRoomsAndMobiles($ids);

		header('Cache-Control: max-age=0');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

    switch ($format) {
    	case 'csv':
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename="mobile_devices.csv"');

    		echo "Улица;Дом;Квартира;Логин;Пароль\n";
    		foreach ($data as $row) {
    			echo "{$row->street_title};";
    			echo "{$row->building_num};";
    			echo "{$row->room_num};";
    			echo "{$row->username};";
    			echo "{$row->password}\n";
    		}

    		break;

    	case 'excel':
    		$spreadsheet = new Spreadsheet();

    		$sheet = $spreadsheet->setActiveSheetIndex(0);
    		$sheet->setTitle('mobile_devices')
    			->setCellValue('A1', 'Улица')
			    ->setCellValue('B1', 'Дом')
			    ->setCellValue('C1', 'Квартира')
			    ->setCellValue('D1', 'Логин')
			    ->setCellValue('E1', 'Пароль');

    		foreach ($data as $i => $row) {
    			$row_index = $i + 2;
	    		$sheet
	    			->setCellValue("A$row_index", $row->street_title)
	    			->setCellValue("B$row_index", $row->building_num)
	    			->setCellValue("C$row_index", $row->room_num)
	    			->setCellValue("D$row_index", $row->username)
	    			->setCellValue("E$row_index", $row->password);
    		}

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="mobile_devices.xlsx"');

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save('php://output');

    		break;
    }

  	die();
  }
}
