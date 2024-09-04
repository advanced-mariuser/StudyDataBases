<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Controller\CrmAppFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;

abstract class AbstractFunctionalTestCase extends AbstractDatabaseTestCase
{
    private App $slimApp;
    private UriFactory $uriFactory;
    private ServerRequestFactory $serverRequestFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->slimApp = CrmAppFactory::createApp();
        $this->uriFactory = new UriFactory();
        $this->serverRequestFactory = new ServerRequestFactory();
    }

    /**
     * Отправляет GET запрос, передавая параметры через URL Query.
     *
     * @param string $urlPath
     * @param array $queryParams
     * @return ResponseInterface
     */
    protected function sendGetRequest(string $urlPath, array $queryParams): ResponseInterface
    {
        $urlString = $urlPath . '?' . http_build_query($queryParams);
        return $this->doRequest('GET', $urlString);
    }

    /**
     * Отправляет POST запрос, передавая параметры в теле запроса в формате "application/x-www-form-urlencoded"
     *
     * @param string $urlPath
     * @param array $requestParams
     * @return ResponseInterface
     */
    protected function sendPostRequest(string $urlPath, array $requestParams): ResponseInterface
    {
        return $this->doRequest('POST', $urlPath, $requestParams);
    }

    /**
     * Отправляет DELETE запрос, передавая параметры через URL Query.
     *
     * @param string $urlPath
     * @param array $queryParams
     * @return ResponseInterface
     */
    protected function sendDeleteRequest(string $urlPath, array $queryParams): ResponseInterface
    {
        $urlString = $urlPath . '?' . http_build_query($queryParams);
        return $this->doRequest('DELETE', $urlString);
    }

    protected function parseResponseBodyAsHtml(ResponseInterface $response): string
    {
        $response->getBody()->seek(0);

        try
        {
            return $response->getBody()->getContents();;
        }
        catch (\JsonException $e)
        {
            throw new \RuntimeException("Invalid response body: {$e->getMessage()}", 0, $e);
        }
    }

    private function doRequest(string $method, string $url, array $body = []): ResponseInterface
    {
        $uri = $this->uriFactory->createUri($url);

        $request = $this->serverRequestFactory
            ->createServerRequest($method, $uri)
            ->withParsedBody($body);

        return $this->slimApp->handle($request);
    }
}