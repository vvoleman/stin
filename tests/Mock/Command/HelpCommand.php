<?php
declare(strict_types=1);


namespace App\Tests\Mock\Command;


class HelpCommand extends \App\Command\HelpCommand
{

	public function __construct() {
		$this->engine = new EngineMock();
	}

}