<?php
declare(strict_types=1);

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController
{
    protected const HTTP_STATUS_OK = 200;
    protected const HTTP_STATUS_SEE_OTHER = 302;
    protected const HTTP_STATUS_BAD_REQUEST = 400;

    protected Environment $twig;

    public function __construct()
    {
        $isProduction = getenv('APP_ENV') === 'prod';
        $path = $isProduction ? '..\templates' : '..\..\templates';
        $this->twig = new Environment(new FilesystemLoader($path));
    }

    protected function isGet(ServerRequestInterface $request): bool
    {
        return $request->getMethod() === 'GET';
    }

    protected function isPost(ServerRequestInterface $request): bool
    {
        return $request->getMethod() === 'POST';
    }

    protected function success(ResponseInterface $response, string $body): ResponseInterface
    {
        try {
            $response->getBody()->write($body);
            return $response->withStatus(self::HTTP_STATUS_OK);
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    protected function redirect(ResponseInterface $response, string $body): ResponseInterface
    {
        try {
            return $response->withHeader('Location', $body)
                ->withStatus(self::HTTP_STATUS_SEE_OTHER);
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    protected function badRequest(ResponseInterface $response): ResponseInterface
    {
        try {
            return $response->withStatus(self::HTTP_STATUS_BAD_REQUEST);
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}