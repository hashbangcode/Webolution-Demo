<?php

namespace Hashbangcode\WevolutionDemo\Controller;

use Hashbangcode\Wevolution\Evolution\Population\Decorators\PopulationDecoratorFactory;
use Hashbangcode\WevolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Type\Color\Color;
use Hashbangcode\Wevolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Wevolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Wevolution\Evolution\EvolutionManager;

/**
 * Class ColorController.
 *
 * @package Hashbangcode\WevolutionDemo\Controller
 */
class ColorDatabaseController extends BaseController
{

  public function colorEvolution($request, $response, $args)
  {
    $title = 'Colour Evolution Database';

    $styles = 'span {width:30px;height:30px;display:inline-block;padding:0px;margin:-2px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}
img {padding:0px;margin:0px;}';

    $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
    $evolution = new EvolutionStorage();

    $evolution->setEvolutionId(1);
    $evolution->setupDatabase('sqlite:' . $database);

    $evolution->setIndividualsPerGeneration(100);
    $evolution->setGlobalMutationFactor(1);
    $evolution->setGlobalMutationAmount(10);

    $generation = $evolution->getGeneration();

    $population = new ColorPopulation();
    $population->setDefaultRenderType('html');

    if ($generation == 0) {
      for ($i = 0; $i < 30; $i++) {
        $population->addIndividual(ColorIndividual::generateFromRgb(255, 255, 255));
      }

      $evolution->setPopulation($population);
    } else {
      $evolution->setPopulation($population);
      $evolution->loadPopulation();
    }

    if (isset($args['step']) && is_numeric($args['step'])) {
      // Run as many generations as is requested.
      for ($i = 0; $i < $args['step']; ++$i) {
        $evolution->runGeneration();
      }
    }
    else {
      // Just run one generation.
      $evolution->runGeneration();
    }

    $output = '';

    $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

    $populationDecorator = PopulationDecoratorFactory::getPopulationDecorator($evolution->getCurrentPopulation(), 'html');
    $output .= $populationDecorator->render() . PHP_EOL . '<br>';

    $steps = [
      1,
      20,
      50,
      100,
      500,
    ];
    $output .= '<ul>';
    foreach ($steps as $step) {
      $output .= '<li><a href="/color_evolution_database/' . $step . '">' . $step . '</a></li>';
    }
    $output .= '</ul>';

    return $this->view->render($response, 'demos.twig', [
      'title' => $title,
      'output' => $output,
      'styles' => $styles
    ]);
  }

}
