<?php

namespace App\Controller;

use App\Exception\EnvironmentException;
use App\Exception\InitializationException;
use App\Exception\UnknownCommandException;
use App\Processor\QuestionProcessor;
use App\Service\API\TokenVerifier;
use App\Util\TimetrackerTrait;

class HomeController
{

	use TimetrackerTrait;

	public function index(): void
	{
		$token = $_GET['token'] ?? '';
		$question = $_GET['question'] ?? '';
		$processor = new QuestionProcessor();

		try {
			if (TokenVerifier::verify($token)) {
				$response = $processor->run($question);
				$this->send([
					'content' => $response->getContent()
				], 200, 'success');
			} else {
				$this->send([
					'message' => 'Invalid token'
				], 403, 'error');
			}
			exit;
		} catch (UnknownCommandException) {
			$this->send([
				'content' => 'Na tuhle otázku neznám odpověď',
			], 200, 'success');
			exit;
		} catch (EnvironmentException | InitializationException $e) {
			$status = 500;
			$message = $e->getMessage();

		}

		$this->send([
			'message' => $message
		], $status, 'error');
	}

	public function validateToken()
	{
		$token = $_GET['token'] ?? '';

		try {
			if(TokenVerifier::verify($token)){
				$this->send([], 200, "success");
				exit;
			}else{
				$this->send([
					"message"=>"Invalid token"
				], 403, 'error');
			}
		} catch (EnvironmentException) {
			$this->send([
				"message" => 'Server error'
			],500,'error');
		}
	}
	
	public function send(array $data, int $code, string $status)
	{
		header('Content-type: application/json');
		http_response_code($code);
		echo json_encode([
			'status' => $status,
			'data' => $data
		]);
	}

}