<?php declare(strict_types = 1);


namespace App\Tests\Mock\HTTP;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{

	private string $body;

	public function __construct(string $body) {
		$this->body = $body;
	}

	/**
	 * @inheritDoc
	 */
	public function getProtocolVersion(): string
	{
		return "";
	}

	/**
	 * @inheritDoc
	 */
	public function withProtocolVersion($version): static
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders(): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function hasHeader($name): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getHeader($name): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaderLine($name): string
	{
		return "";
	}

	/**
	 * @inheritDoc
	 */
	public function withHeader($name, $value): static
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function withAddedHeader($name, $value): MessageInterface|string
	{
		return "";
	}

	/**
	 * @inheritDoc
	 */
	public function withoutHeader($name): static
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */

	public function getBody()
	{
		/** @phpstan-ignore-next-line */
		return $this->body;
	}

	/**
	 * @inheritDoc
	 */
	public function withBody(StreamInterface $body): static
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getStatusCode(): int
	{
		return 0;
	}

	/**
	 * @inheritDoc
	 */
	public function withStatus($code, $reasonPhrase = ''): static
	{
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getReasonPhrase(): string
	{
		return "";
	}
}