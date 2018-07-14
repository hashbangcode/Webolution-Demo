<?php

namespace Hashbangcode\WevolutionDemo\Controller;

use Hashbangcode\WevolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController.
 *
 * @package Hashbangcode\WevolutionDemo\Controller
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
