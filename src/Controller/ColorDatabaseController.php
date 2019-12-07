<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\PopulationDecoratorFactory;
use Hashbangcode\Webolution\Statistics\Decorator\StatisticsDecoratorHtml;
use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution;
use Hashbangcode\Webolution\Type\Color\Color;
use Hashbangcode\Webolution\Type\Color\ColorPopulation;
use Hashbangcode\Webolution\Type\Color\ColorIndividual;
use Hashbangcode\Webolution\EvolutionManager;
use Hashbangcode\WebolutionDemo\Model\Evolution as EvolutionModel;
use Hashbangcode\WebolutionDemo\Model\Population as PopulationModel;
use Hashbangcode\WebolutionDemo\Model\Individual as IndividualModel;
use Hashbangcode\WebolutionDemo\Model\Statistics as StatisticsModel;

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

    $evolutionMapper = new EvolutionModel($this->container->database);
    $populationMapper = new PopulationModel($this->container->database);
    $individualMapper = new IndividualModel($this->container->database);
    $statisticMapper = new StatisticsModel($this->container->database);

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
      $population = $populationMapper->load($evolution->getGeneration(), $evolutionId);
      $evolution->setPopulation($population);
      $individualMapper->loadGeneration($evolutionId, $evolution);
      $population->setStatistics($statisticMapper->load($evolutionId, $evolution->getGeneration()));
    }

    if (isset($args['step']) && is_numeric($args['step'])) {
      // Run as many generations as is requested.
      for ($i = 0; $i < $args['step']; ++$i) {
        $evolution->runGeneration();
        $evolutionMapper->update($evolutionId, $evolution);
        $populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
        $individualMapper->insertFromPopulation($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
        $statisticMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation()->getStatistics());
      }
    }
    else {
      // Just run one generation.
      $evolution->runGeneration();
      $evolutionMapper->update($evolutionId, $evolution);
      $populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
      $individualMapper->insertFromPopulation($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
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
      5,
      20,
      50,
      100,
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
