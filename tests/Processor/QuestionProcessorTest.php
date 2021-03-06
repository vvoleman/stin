<?php

namespace App\Tests\Processor;

use App\Exception\InitializationException;
use App\Exception\UnknownCommandException;
use App\Processor\QuestionProcessor;
use App\Tests\Mock\Command\PrivateConstructor;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class QuestionProcessorTest extends TestCase
{

	/**
	 * Command with private constructor should always throw exception
	 * @depends testRun
	 * @throws UnknownCommandException
	 * @covers \App\Processor\QuestionProcessor
	 * @covers \App\Command\Command
	 */
	public function testCommandReflection(): void
	{
		$obj = new \ReflectionClass(QuestionProcessor::class);
		$processor = new QuestionProcessor();
		$property = $obj->getProperty("commands");
		$property->setAccessible(true);
		$property->setValue($processor, [PrivateConstructor::class]);

		$this->expectException(InitializationException::class);
		$processor->run("Tohle je privátní příkaz");
		$test = $obj->getProperty('innerCommands');
		$test->setAccessible(true);
		dd($test->getValue($processor));
	}

    /**
     * @dataProvider questionsProvider
	 * @covers \App\Processor\QuestionProcessor
	 * @covers \App\Command\Command
	 * @covers \App\Command\CurrencyCommand
	 * @covers \App\Command\CurrencyHistoryCommand
	 * @covers \App\Command\HelpCommand
	 * @covers \App\Command\NameCommand
	 * @covers \App\Command\TimeCommand
	 * @covers \App\Command\TimeZoneCommand
	 * @covers \App\Response\Command\SimpleResponse
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\CurrencyContainerFactory
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 * @covers \App\Service\Currency\Retriever\Client
	 * @covers \App\Service\Currency\Retriever\DataSource\CnbSource
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Command\RecommendCurrencyCommand
     */
    public function testRun(string $question, bool $pass): void
    {
        $processor = new QuestionProcessor();

        $isOK = true;
        try {
            $response = $processor->run($question);
			if($response->getCode() !== $response::HTTP_SUCCESS){
				$isOK = false;
			}
        } catch (UnknownCommandException $e) {
            $isOK = false;
        }
        $this->assertEquals($pass, $isOK);
    }

    /**
     * @return array
     */
    public function questionsProvider(): array
    {
        return [
            "Question: Kolik je hodin?" => ["Kolik je hodin?", true],
            "Question: What time is it?" => ["What time is it?", true],
            "Question: Kolik je hodin v PST?" => ["Kolik je hodin v PST?", true],
			"Question: Jaký je kurz EUR?" => ["Jaký je kurz EUR?", true],
            "Empty variable, no leading space, passable"=>["Kolik je hodin v?", true],
            "Empty variable, leading space, passable"=>["Kolik je hodin v ?", true],
            "Empty variable, no question mar, passable"=>["Kolik je hodin v ", true],
            "Invalid timezone"=>["Kolik je hodin v Liberci", false],
            "Unknown question"=>["Jak se máš?", false],
            "Empty string"=>["", false]
        ];
    }
}
