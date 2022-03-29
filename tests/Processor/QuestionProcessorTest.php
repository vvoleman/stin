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
     * @throws UnknownCommandException
     */
    public function testCommandReflection(): void
    {
        $obj = new \ReflectionClass(QuestionProcessor::class);
        $processor = new QuestionProcessor();
        $property = $obj->getProperty("commands");
        $property->setAccessible(true);
        $property->setValue($processor,[PrivateConstructor::class]);

        $this->expectException(InitializationException::class);
        $processor->run("Tohle je privátní příkaz");
    }
    
    /**
     * @dataProvider questionsProvider
     */
    public function testRun(string $question, bool $pass): void
    {
        $processor = new QuestionProcessor();

        $isOK = true;
        try {
            $processor->run($question);
        } catch (UnknownCommandException $e) {
            $str = (string) $e;
            $isOK = false;
        }

        $this->assertEquals($pass, $isOK);
    }

    /**
     * @return array<array<string,bool>>
     */
    public function questionsProvider(): array
    {
        return [
            ["Kolik je hodin?", true],
            ["What time is it?", true],
            ["Kolik je hodin v PST?", true],
            ["Kolik je hodin v?", false],
            ["Kolik je hodin v ?", false],
            ["Kolik je hodin v ", false],
            ["Jak se máš?", false],
            [" ", false]
        ];
    }
}
