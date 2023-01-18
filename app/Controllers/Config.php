<?php

namespace App\Controllers;

class Config extends BaseController {
  public function getIndex() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    //
    $configModel = model('App\Models\ConfigModel');
    $peers = $configModel->getPeers();
    $extensions = $configModel->getExtensions();

		return json_encode([
			'result' => true,
			'peers' => $peers,
			'extensions' => $extensions
		]);
  }

  public function postSave() {
  	// ACL
    $userModel = model('App\Models\UserModel');
    if (!$user = $userModel->getCurrent())
      return json_encode([
      	'result' => false,
      	'message' => 'Ошибка авторизации.'
      ]);

    //
  	$peers = $this->request->getPost('peers');
  	$extensions = $this->request->getPost('extensions');

		if (null === $peers)
	    return json_encode([
	    	'result' => false,
      	'message' => 'Неверный запрос.'
	    ]);

		if (null === $extensions)
	    return json_encode([
	    	'result' => false,
      	'message' => 'Неверный запрос.'
	    ]);

		// building data
  	$data = [
  		'peers' => $peers,
  		'extensions' => $extensions
  	];

  	// saving
    $configModel = model('App\Models\ConfigModel');
  	try {
	  	$configModel->insert($data, true);
	 	} catch (\Exception $e) {
	    return json_encode([
	    	'result' => false,
	    	'message' => 'Ошибка сохранения: ' . $e->getMessage()
	    ]);
	 	}

    return json_encode([
    	'result' => true,
    	'message' => "Сохранено."
    ]);
  }
}
