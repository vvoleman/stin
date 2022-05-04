<?php
declare(strict_types=1);


namespace App\Tests\Mock\Command;

class EngineMock extends \Latte\Engine
{

	public function renderToString(string $name, $params = [], ?string $block = null): string
	{
		return "TEMPLATE";
	}

}