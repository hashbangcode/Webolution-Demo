<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\Evolution;
use Hashbangcode\Webolution\Evolution\EvolutionStorage;
use Hashbangcode\Webolution\Type\Color\Color;
use Hashbangcode\Webolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Webolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Webolution\Evolution\EvolutionManager;

/**
 * Class ColorController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class ColorController extends BaseController
{

  public function colorEvolution($request, $response, $args)
  {
    $styles = 'span {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}';

    $this->logger->info("Number Evolution '/color_evolution' route");

    $title = 'Color Evolution';

    // Setup the population.
    $population = new ColorPopulation();
    $population->setDefaultRenderType('html');
    $population->setPopulationFitnessType('hue');

    // Add individuals to the population.
    for ($i = 0; $i < 30; $i++) {
      $population->addIndividual(ColorIndividual::generateFromRgb(255, 255, 255));
    }

    // Create the EvolutionManager object and add the population to it.
    $evolution = new EvolutionManager();
    $evolution->getEvolutionObject()->setPopulation($population);
    $evolution->getEvolutionObject()->setMaxGenerations(200);
    $evolution->getEvolutionObject()->setIndividualsPerGeneration(40);
    $evolution->getEvolutionObject()->setGlobalMutationAmount(5);
    $evolution->getEvolutionObject()->setReplicationType('crossover');
    $evolution->runEvolution();

    $output = '';
    $output .= $evolution->getEvolutionObject()->renderGenerations(TRUE);

    return $this->view->render($response, 'demos.twig', [
      'title' => $title,
      'output' => $output,
      'styles' => $styles,
    ]);
  }

}
