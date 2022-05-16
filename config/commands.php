<?php

use App\Command\CurrencyCommand;
use App\Command\CurrencyHistoryCommand;
use App\Command\HelpCommand;
use App\Command\NameCommand;
use App\Command\RecommendCurrencyCommand;
use App\Command\TimeCommand;
use App\Command\TimeZoneCommand;

return [
	TimeZoneCommand::class,
	TimeCommand::class,
	CurrencyCommand::class,
	NameCommand::class,
	CurrencyHistoryCommand::class,
	HelpCommand::class,
	RecommendCurrencyCommand::class,
];