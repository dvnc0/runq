<?php
namespace Runq\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request;
use Clyde\Request\Request_Response;
use Clyde\Application;
use Clyde\Core\Event_Dispatcher;
use Clyde\Tools\Input;

abstract class Runq_Action extends Action_Base {
	/**
	 * Input
	 *
	 * @var Input
	 */
	protected Input $Input;

	/**
	 * construct
	 *
	 * @param Application      $Application      Application
	 * @param Event_Dispatcher $Event_Dispatcher Event Dispatcher
	 */
	public function __construct(Application $Application, Event_Dispatcher $Event_Dispatcher) {
		parent::__construct($Application, $Event_Dispatcher);
		$this->Input = $this->getInputInstance();
	}
	/**
	 * Return an instance of Input
	 *
	 * @return Input
	 * @codeCoverageIgnore
	 */
	protected function getInputInstance(): Input {
		return new Input($this->Printer);
	}

	/**
	 * Exit now
	 *
	 * @param int<0,1> $code exit code
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function exitNow(int $code) {
		exit($code);
	}
}