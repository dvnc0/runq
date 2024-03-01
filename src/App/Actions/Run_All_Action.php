<?php
namespace Runq\Actions;

use Runq\Actions\Runq_Action;
use Clyde\Request\Request;
use Clyde\Request\Request_Response;
use Symfony\Component\Yaml\Yaml;
use PDO;
use PDOException;

class Run_All_Action extends Runq_Action {
	public function execute(Request $Request): Request_Response {

		if (!file_exists(ROOT . '/runq.yaml')) {
			$this->Printer->error('Please init your project with runq init');
			$this->exitNow(1);
		}

		$config = Yaml::parseFile(ROOT . '/runq.yaml');

		$files = array_diff(scandir($config['query_file_path']), array('..', '.'));
		$processed = Yaml::parseFile(ROOT . '/runq.log.yaml')['processed'];

		$files = array_diff($files, $processed);

		foreach ($files as $file) {
			$file_path = $config['query_file_path'] . '/' . $file;

			if (!file_exists($file_path)) {
				$this->Printer->error('File not found: ' . $file_path);
				$this->exitNow(1);
			}

			$yaml = Yaml::parseFile($file_path);

			$connection = $yaml['connection'];
			$query = $yaml['query'];

			$database = new PDO($connection['dsn'], $connection['user'], $connection['password']);

			try {
				$database->beginTransaction();
				$database->exec($query['create']);

				$error = $database->errorInfo();

				if ($error[0] !== '00000') {
					$database->rollBack();
					$this->Printer->error('Error running query: ' . $error[2]);
					$this->exitNow(1);
				}

				if ($database->inTransaction()) {
					$database->commit();
				}
			} catch (PDOException $e) {
				$database->rollBack();
				$this->Printer->error('Error running query: ' . $e->getMessage());
				$this->exitNow(1);
			}

			$log = Yaml::parseFile(ROOT . '/runq.log.yaml');
			$log['processed'][] = $file;
			$log['last_run'] = date('YmdHis');
			file_put_contents(ROOT . '/runq.log.yaml', Yaml::dump($log));

			$this->Printer->success('Query file run: ' . $file_path);
		}

		$this->Printer->success('All query files run');

		return new Request_Response(true);
	}
}