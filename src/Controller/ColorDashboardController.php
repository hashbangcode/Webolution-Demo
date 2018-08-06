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
use Hashbangcode\Webolution\Evolution\Population\Decorators\PopulationDecoratorFactory;

/**
 * Class ColorDashboardController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class ColorDashboardController extends BaseController
{

  public function colorDashboardEvolution(Request $request, Response $response, $args)
  {
    // Grab the parameters sent to the page.
    $params = explode('/', $args['params']);

    if ($params[0] == 'clear_database') {
      $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
      $evolution = new EvolutionStorage();
      $evolution->setupDatabase('sqlite:' . $database);
      $evolution->clearDatabase();

      return $response->withStatus(302)->withHeader('Location', '/color_dashboard_evolution');
    }

    if ($params[0] == 'step') {
      $step = $params[1];
    }
    else {
      $step = NULL;
    }

    $title = 'Color Dashboard';

    $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
    $evolution = new EvolutionStorage();

    $individuals = 30;

    $evolution->setEvolutionId(2);
    $evolution->setupDatabase('sqlite:' . $database);

    $evolution->setIndividualsPerGeneration($individuals);
    $evolution->setGlobalMutationAmount(5);
    $evolution->setReplicationType('crossover');

    $generation = $evolution->getGeneration();

    $population = new ColorPopulation();
    $population->setDefaultRenderType('html');

    if ($generation == 0) {
      for ($i = 0; $i < $individuals; $i++) {
        $population->addIndividual(ColorIndividual::generateFromRgb(255, 255, 255));
      }

      $evolution->setPopulation($population);
    } else {
      $evolution->setPopulation($population);
      $evolution->loadPopulation();
    }

    if (!empty($step) && is_numeric($step)) {
      // Run as many generations as is requested.
      for ($i = 0; $i < $step; ++$i) {
        $evolution->runGeneration();
      }
    }

    $populationDecorator = PopulationDecoratorFactory::getPopulationDecorator($evolution->getCurrentPopulation(), 'html');
    $currentGeneration = $populationDecorator->render() . PHP_EOL . '<br>';

    $statistics = '<p>Generation: ' . $evolution->getGeneration() . '</p>';

    $graphs = '';

    $pastGenerations = '';


    $steps = [
      1,
      2,
      5,
      20,
      50,
      100,
      500,
    ];
    $operations['steps'] = $steps;

    return $this->view->render($response, 'dashboard.twig', [
      'title' => $title,
      'current_generation' => $currentGeneration,
      'statistics' => $statistics,
      'graphs' => $graphs,
      'past_generations' => $pastGenerations,
      'operations' => $operations,
    ]);
  }
}
