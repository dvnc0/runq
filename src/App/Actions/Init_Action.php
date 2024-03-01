<?php
namespace Runq\Actions;

use Runq\Actions\Runq_Action;
use Clyde\Request\Request;
use Clyde\Request\Request_Response;
use Symfony\Component\Yaml\Yaml;

class Init_Action extends Runq_Action {
	public function execute(Request $Request): Request_Response {
		if (file_exists(ROOT . '/runq.yaml')) {
			$this->Printer->error('A runq.yaml file already exists in this directory.');
			$this->exitNow(1);
		}
		$query_file_path = $this->Input->get('What is the relative path to the directory where your query files are located?');
		$query_file_prefix = $this->Input->get('What is the prefix for your query files?');
		$database_name = $this->Input->get('What is the name of the database you will be running queries against?');
		$database_host = $this->Input->get('What is the host of the database you will be running queries against?');
		$database_user = $this->Input->get('What is the user name?');
		$database_password = $this->Input->get('What is the user password?');

		$this->Printer->alert('You gave the following settings:');
		$this->Printer->message('Query file path: ' . $query_file_path);
		$this->Printer->message('Query file prefix: ' . $query_file_prefix);
		$this->Printer->message('Database name: ' . $database_name);
		$this->Printer->message('Database host: ' . $database_host);
		$this->Printer->message('Database user: ' . $database_user);
		$this->Printer->message('Database password: ' . $database_password);

		$affirm = $this->Input->affirm('Is this correct? (y/n)');

		if (!$affirm) {
			$this->Printer->error('Aborted');
			$this->exitNow(1);
		}

		$config = [
			'query_file_path' => $query_file_path,
			'query_file_prefix' => $query_file_prefix,
			'database' => [
				'name' => $database_name,
				'host' => $database_host,
				'user' => $database_user,
				'password' => $database_password
			]
		];

		$log = [
			'processed' => [],
			'last_run' => date('YmdHis'),
		];

		$yaml = Yaml::dump($config);

		file_put_contents(ROOT . '/runq.yaml', $yaml);
		$this->Printer->success('runq.yaml file created');
		file_put_contents(ROOT . '/runq.log.yaml', Yaml::dump($log));
		$this->Printer->success('runq.log.yaml file created');

		return new Request_Response(true);
	}
}