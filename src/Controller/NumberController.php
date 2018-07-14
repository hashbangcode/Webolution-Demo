<?php

namespace Hashbangcode\WevolutionDemo\Controller;

use Hashbangcode\WevolutionDemo\Controller\BaseController;
use Hashbangcode\Wevolution\Evolution\EvolutionManager;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Population\NumberPopulation;
use Hashbangcode\Wevolution\Evolution\Individual\NumberIndividual;

/**
 * Class NumberController.
 *
 * @package Hashbangcode\WevolutionDemo\Controller
 */
class NumberController extends BaseController
{

  public function numberEvolution(Request $request, Response $response, $args)
  {
    $this->logger->info("Number Evolution '/number_evolution' route");

    $title = 'Number Evolution';

    // Setup the population.
    $population = new NumberPopulation();
    $population->setDefaultRenderType('html');

    // Add individuals to the population.
    for ($i = 0; $i < 30; $i++) {
      $population->addIndividual(NumberIndividual::generateFromNumber(1));
    }

    // Create the EvolutionManager object and add the population to it.
    $evolution = new EvolutionManager();
    $evolution->getEvolutionObject()->setPopulation($population);
    $evolution->getEvolutionObject()->setMaxGenerations(200);
    $evolution->getEvolutionObject()->setReplicationType('crossover');
    $evolution->runEvolution();

    $output = '';
    $output .= $evolution->getEvolutionObject()->renderGenerations(TRUE);

    return $this->view->render($response, 'demos.twig', [
      'title' => $title,
      'output' => $output,
    ]);
  }
}
