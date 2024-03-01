<?php
namespace Runq\Actions;

use Runq\Actions\Runq_Action;
use Clyde\Request\Request;
use Clyde\Request\Request_Response;
use Symfony\Component\Yaml\Yaml;

class Create_Action extends Runq_Action {
	public function execute(Request $Request): Request_Response {
		// Check if runq.yaml already exists
		if (!file_exists(ROOT . '/runq.yaml')) {
			$this->Printer->error('Please init your project with runq init');
			$this->exitNow(1);
		}

		$config = Yaml::parseFile(ROOT . '/runq.yaml');
		$prefix = $config['query_file_prefix'];
		$save_path = $config['query_file_path'];
		$connection_dsn = "mysql:host=" . $config['database']['host'] . ";dbname=" . $config['database']['name'];
		$connection_user = $config['database']['user'];
		$connection_password = $config['database']['password'];

		$date = date("YmdHis");
		$filename = $prefix . "_" . $date . ".yml";

		$file_data = [
			'connection' => [
				'dsn' => $connection_dsn,
				'user' => $connection_user,
				'password' => $connection_password
			],
			'query' => [
				"create" => '',
				"revert" => '',
			]
		];

		$yaml = Yaml::dump($file_data);
		file_put_contents($save_path . "/" . $filename, $yaml);
		$this->Printer->success('Query file created: ' . $save_path . "/" . $filename);

		return new Request_Response(true);
	}
}