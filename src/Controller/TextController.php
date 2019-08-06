<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Individual\TextIndividual;
use Hashbangcode\Webolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Webolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Webolution\Evolution\EvolutionManager;
use Hashbangcode\Webolution\Evolution\Population\TextPopulation;

/**
 * Class TextController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class TextController extends BaseController
{

  public function textEvolution($request, $response, $args)
  {
    $styles = 'span {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}';

    $this->logger->info("Number Evolution '/text_evolution' route");

    $title = 'Text Evolution';

    // Setup the population.
    $population = new TextPopulation();
    $population->setDefaultRenderType('html');
    //$population->setPopulationFitnessType('hue');

    // Add individuals to the population.
    for ($i = 0; $i < 30; $i++) {
      $population->addIndividual(TextIndividual::generateRandomTextIndividual(20));
    }

    // Create the EvolutionManager object and add the population to it.
    $evolution = new EvolutionManager();
    $evolution->getEvolutionObject()->setPopulation($population);
    $evolution->getEvolutionObject()->setMaxGenerations(1000);
    $evolution->getEvolutionObject()->setIndividualsPerGeneration(40);
    $evolution->getEvolutionObject()->setGlobalMutationAmount(5);
    //$evolution->getEvolutionObject()->setReplicationType('crossover');
    $evolution->getEvolutionObject()->setGlobalFitnessGoal('Monkey say monkey do');
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
