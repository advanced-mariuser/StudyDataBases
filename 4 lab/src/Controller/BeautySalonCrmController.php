<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BeautySalonCrmController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->isGet($request)) {
            return $this->badRequest($response);
        }

        $body = $this->twig->render('index.twig');
        return $this->success($response, $body);
    }
}