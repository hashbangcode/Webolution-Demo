<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Individual\ImageIndividual;
use Hashbangcode\Webolution\Evolution\Population\Decorators\PopulationDecoratorFactory;
use Hashbangcode\Webolution\Evolution\Population\ImagePopulation;
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
class ImageDatabaseController extends BaseController
{

  public function imageEvolution($request, $response, $args)
  {
    $title = 'Image Evolution Database';

    $styles = "img{border:1px solid black;}";

    $evolutionId = 98;

    $evolutionMapper = new \Hashbangcode\WebolutionDemo\Model\Evolution($this->container->database);
    $populationMapper = new \Hashbangcode\WebolutionDemo\Model\Population($this->container->database);
    $individualMapper = new \Hashbangcode\WebolutionDemo\Model\Individual($this->container->database);
    $statisticMapper = new \Hashbangcode\WebolutionDemo\Model\Statistics($this->container->database);

    $evolution = $evolutionMapper->load($evolutionId);

    $individuals = 50;

    $evolution->setIndividualsPerGeneration($individuals);
    $evolution->setGlobalMutationAmount(5);
    $evolution->setReplicationType(Evolution::REPLICATION_TYPE_CLONE);

    $generation = $evolution->getGeneration();

    $population = new ImagePopulation();
    $population->setDefaultRenderType('html');

    if ($generation == 0) {
      for ($i = 0; $i < $individuals; $i++) {
        $image = ImageIndividual::generateFromImageSize(25, 25);
        $image->getObject()->setPixel(24, 12, 1);
        $population->addIndividual($image);
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
      20,
      50,
      100,
      ///500,
    ];
    $output .= '<ul>';
    foreach ($steps as $step) {
      $output .= '<li><a href="/image_evolution_database/' . $step . '">' . $step . '</a></li>';
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
