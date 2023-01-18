<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model {
  //protected $table = 'street';
  //protected $primaryKey = 'id';

  //protected $allowedFields = ['code', 'title'];

  function getPeers() {
  	return file_get_contents('/etc/asterisk/sip_custom.conf');
  }

  function getExtensions() {
  	return file_get_contents('/etc/asterisk/extensions_custom.conf');
  }

  function insert($data = null, bool $returnID = true) {
		file_put_contents("/etc/asterisk/sip_custom.conf", $data['peers']);
		file_put_contents("/etc/asterisk/extensions_custom.conf", $data['extensions']);

		$data_suffix = sprintf('%04d%02d%02d%02d%02d%02d',
			date('Y'),
			date('m'),
			date('d'),
			date('H'),
			date('i'),
			date('s'));

		file_put_contents("/etc/asterisk/backups/sip_custom-{$data_suffix}.conf", $data['peers']);
		file_put_contents("/etc/asterisk/backups/extensions_custom-{$data_suffix}.conf", $data['extensions']);

		// config
		$config = (object)[
			'ami_host' => 'localhost',
			'ami_scheme' => 'tcp://',
			'ami_port' => 5038,
			'ami_user' => 'golos',
			'ami_password' => 'golos',
			'ami_connect_timeout' => 10000,
			'ami_read_timeout' => 10000
		];

		// checking AMI
		try {
			$ami = new \PAMI\Client\Impl\ClientImpl([
				'host' => $config->ami_host,
				'scheme' => $config->ami_scheme,
				'port' => $config->ami_port,
				'username' => $config->ami_user,
				'secret' => $config->ami_password,
				'connect_timeout' => $config->ami_connect_timeout,
				'read_timeout' => $config->ami_read_timeout
			]);
		} catch (\Exception $e) {
			// BUG: PAMI implode parameters deprecated order workaround
		}

		try {
			$ami->open();
		} catch (\Exception $e) {
			// BUG: PAMI implode parameters deprecated order workaround
		}

		try {
			// sending command
			$commandMsg = new \PAMI\Message\Action\CommandAction("sip reload");
			$ami->send($commandMsg);
		} catch (\Exception $e) {
			// BUG: PAMI implode parameters deprecated order workaround
		}

		try {
			// sending command
			$commandMsg = new \PAMI\Message\Action\CommandAction("dialplan reload");
			$ami->send($commandMsg);
		} catch (\Exception $e) {
			// BUG: PAMI implode parameters deprecated order workaround
		}

		// releasing AMI
		$ami->close();

  	// $id = parent::insert($data, true);
  	$id = null;

    return $id;
  }
}
