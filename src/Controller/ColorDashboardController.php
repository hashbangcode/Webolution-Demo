<?php

namespace Hashbangcode\WevolutionDemo\Controller;

use Hashbangcode\WevolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Wevolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Wevolution\Evolution\EvolutionManager;

/**
 * Class ColorDashboardController.
 *
 * @package Hashbangcode\WevolutionDemo\Controller
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
