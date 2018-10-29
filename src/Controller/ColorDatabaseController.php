<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Population\Decorators\PopulationDecoratorFactory;
use Hashbangcode\Webolution\Evolution\Statistics\Decorators\StatisticsDecoratorHtml;
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
class ColorDatabaseController extends BaseController
{

  public function colorEvolution($request, $response, $args)
  {
    $title = 'Colour Evolution Database';

    $styles = 'span {width:30px;height:30px;display:inline-block;padding:0px;margin:-2px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}
img {padding:0px;margin:0px;}';

    $evolutionId = 99;

    $evolutionMapper = new \Hashbangcode\WebolutionDemo\Model\Evolution($this->container->database);
    $populationMapper = new \Hashbangcode\WebolutionDemo\Model\Population($this->container->database);
    $individualMapper = new \Hashbangcode\WebolutionDemo\Model\Individual($this->container->database);
    $statisticMapper = new \Hashbangcode\WebolutionDemo\Model\Statistics($this->container->database);

    ///$evolutionMapper->createDatabase();

    $evolution = $evolutionMapper->load($evolutionId);

    $individuals = 1000;

    $evolution->setIndividualsPerGeneration($individuals);
    $evolution->setGlobalMutationAmount(5);
    $evolution->setReplicationType(Evolution::REPLICATION_TYPE_CROSSOVER);

    $generation = $evolution->getGeneration();

    $population = new ColorPopulation();
    $population->setDefaultRenderType('html');

    if ($generation == 0) {
      for ($i = 0; $i < $individuals; $i++) {
        $population->addIndividual(ColorIndividual::generateFromRgb(255, 255, 255));
      }

      $evolution->setPopulation($population);
      $evolutionMapper->insert($evolutionId, $evolution);
    } else {
      $individuals = $individualMapper->loadGeneration($evolutionId, $evolution);
      $population = $populationMapper->load($evolution->getGeneration(), $evolutionId);
      $population->setIndividuals($individuals);
      $evolution->setPopulation($population);
    }

    if (isset($args['step']) && is_numeric($args['step'])) {
      // Run as many generations as is requested.
      for ($i = 0; $i < $args['step']; ++$i) {
        $evolution->runGeneration();
        $evolutionMapper->update($evolutionId, $evolution);
        $populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
        $individualMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
        $statisticMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation()->getStatistics());
      }
    }
    else {
      // Just run one generation.
      $evolution->runGeneration();
      $evolutionMapper->update($evolutionId, $evolution);
      $populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
      $individualMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
      $statisticMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation()->getStatistics());
    }

    $output = '';

    $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

    $populationDecorator = PopulationDecoratorFactory::getPopulationDecorator($evolution->getCurrentPopulation(), 'html');
    $output .= $populationDecorator->render() . PHP_EOL . '<br>';

    $statisticsDecorator = new StatisticsDecoratorHtml($evolution->getCurrentPopulation()->getStatistics());
    $output .= $statisticsDecorator->render();

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

    $output .= '<p><a href="/clear_database">Clear Database</a></p>';

    return $this->view->render($response, 'demos.twig', [
      'title' => $title,
      'output' => $output,
      'styles' => $styles
    ]);
  }

}
