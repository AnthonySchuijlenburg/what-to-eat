<?php

namespace Feature\Services;

use App\Services\BrowserService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\BrowserKit\Response;
use Tests\TestCase;

class BrowserServiceTest extends TestCase
{
    private MockObject $browser;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->browser = $this->createMock(BrowserService::class);
    }

    public function test_it_should_return_a_response(): void
    {
        // Arrange
        $content = 'content';
        $statusCode = 200;

        $this->browser
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        // Act
        $result = $this
            ->browser
            ->makeRequest('GET', 'https://www.google.com');

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($content, $result->getContent());
        $this->assertSame($statusCode, $result->getStatusCode());
    }

    public function test_it_should_return_an_error_response(): void
    {
        // Arrange
        $content = '';
        $statusCode = 404;

        $this->browser
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        // Act
        $result = $this
            ->browser
            ->makeRequest('GET', 'https://www.google.com');

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($content, $result->getContent());
        $this->assertSame($statusCode, $result->getStatusCode());
    }
}
