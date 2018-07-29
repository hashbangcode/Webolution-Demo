<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
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
