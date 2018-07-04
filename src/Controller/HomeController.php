<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;


class HomeController extends BaseController
{

    public function home(Request $request, Response $response, $args)
    {
        // Sample log message
        $this->logger->info("Index '/' route");

        // Render index view
        return $this->view->render($response, 'index.twig');
    }
}
