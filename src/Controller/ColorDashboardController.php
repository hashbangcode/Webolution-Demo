<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\Evolution;
use Hashbangcode\Webolution\Evolution\EvolutionStorage;
use Hashbangcode\Webolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Webolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Webolution\Evolution\EvolutionManager;

/**
 * Class ColorDashboardController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class ColorDashboardController extends BaseController
{

  public function colorDashboardEvolution(Request $request, Response $response, $args)
  {

    $title = 'Color Dashboard';

    $currentGeneration = '';
    $statistics = '';
    $graphs = '';

    $pastGenerations = '';

    return $this->view->render($response, 'dashboard.twig', [
      'title' => $title,
      'current_generation' => $currentGeneration,
      'statistics' => $statistics,
      'graphs' => $graphs,
      'past_generations' => $pastGenerations,
    ]);
  }
}
