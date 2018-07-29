<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\Evolution;
use Hashbangcode\Webolution\Evolution\EvolutionStorage;
use Hashbangcode\Webolution\Evolution\Population\ImagePopulation;
use Hashbangcode\Webolution\Evolution\Individual\ImageIndividual;
use Hashbangcode\Webolution\Evolution\EvolutionManager;

/**
 * Class ImageController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class ImageController extends BaseController
{

  public function imageEvolution(Request $request, Response $response, $args)
  {
    $styles = "img{border:1px solid black;}";

    $this->logger->info("Image Eovolution '/image_evolution' route");

    $title = 'Image Evolution';

    // Setup the population.
    $population = new ImagePopulation();
    $population->setPopulationFitnessType('height');
    $population->setDefaultRenderType('html');
    $population->setPopulationFitnessType('hue');

    // Add individuals to the population.
    for ($i = 0; $i < 10; $i++) {
      $image = ImageIndividual::generateFromImageSize(25, 25);
      $image->getObject()->setPixel(24, 12, 1);
      $population->addIndividual($image);
    }

    // Create the EvolutionManager object and add the population to it.
    $evolution = new EvolutionManager();
    $evolution->getEvolutionObject()->setPopulation($population);
    $evolution->getEvolutionObject()->setMaxGenerations(50);
    $evolution->getEvolutionObject()->setIndividualsPerGeneration(10);
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
