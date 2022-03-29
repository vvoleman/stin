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
        $property->setValue($processor, [PrivateConstructor::class]);

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
            $str = (string)$e;
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
            "Empty variable, no leading space"=>["Kolik je hodin v?", false],
            "Empty variable, leading space"=>["Kolik je hodin v ?", false],
            "Empty variable, no question mark"=>["Kolik je hodin v ", false],
            "Unknown question"=>["Jak se máš?", false],
            "Empty string"=>["", false]
        ];
    }
}
