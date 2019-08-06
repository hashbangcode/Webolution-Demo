<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Individual\Decorators\IndividualDecoratorFactory;
use Hashbangcode\Webolution\Evolution\Individual\Individual;
use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Hashbangcode\WebolutionDemo\DashboardManager;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\Evolution;
use Hashbangcode\Webolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Webolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Webolution\Evolution\EvolutionManager;
use Hashbangcode\Webolution\Evolution\Population\Decorators\PopulationDecoratorFactory;
use Hashbangcode\Webolution\Evolution\Statistics\Decorators\StatisticsDecoratorHtml;
use Hashbangcode\WebolutionDemo\Model\Evolution as EvolutionModel;

/**
 * Class BaseDashboardController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class BaseDashboardController extends BaseController
{

  const NUMBER_OF_INDIVIDUALS = 300;

  const DASHBOARD_TYPE = 'Color';

  const DASHBOARD_TITLE = 'Color Dashboard';

  const DASHBOARD_PATH = '/color_dashboard_evolution';

  const DASHBOARD_EVOLUTION_ID = 100;

  const DASHBOARD_ROUTE_NAME = 'color_dashboard_evolution';

  const DASHBOARD_RENDER_TYPE = 'html';

  public function dashboardEvolution(Request $request, Response $response, $args)
  {
    $action = false;

    // Grab the parameters sent to the page.
    if (isset($args['params'])) {
      list($action) = explode('/', $args['params']);
    }

    if ($action == 'clear_database') {
      $evolutionMapper = new EvolutionModel($this->container->database);
      $evolutionMapper->clearDatabase();

      return $response->withStatus(302)->withHeader('Location', static::DASHBOARD_PATH);
    }

    if ($action == 'reset') {
      $evolutionMapper = new EvolutionModel($this->container->database);
      $evolutionMapper->delete(static::DASHBOARD_EVOLUTION_ID);

      return $response->withStatus(302)->withHeader('Location', static::DASHBOARD_PATH);
    }

    $statistics = '';
    $graphs = '';
    $pastGenerations = [];

    $dashboardManager = new DashboardManager($this->container->database);
    $evolution = $dashboardManager->loadEvolution(static::DASHBOARD_EVOLUTION_ID, static::NUMBER_OF_INDIVIDUALS);

    if ($evolution->getGeneration() == 0) {
      $individuals = $this->generateIndividuals();
      $dashboardManager->setupEvolution(static::DASHBOARD_EVOLUTION_ID, $evolution, $individuals, static::DASHBOARD_TYPE);
    } else {
      $dashboardManager->loadPopulation(static::DASHBOARD_EVOLUTION_ID, $evolution);
    }

    if ($action == 'run') {
      for ($i = 0; $i < 100; ++$i) {
        $evolution->runGeneration(false);
        $dashboardManager->saveEvolution(static::DASHBOARD_EVOLUTION_ID, $evolution);
      }

      return $response->withStatus(302)->withHeader('Location', static::DASHBOARD_PATH);
    }

    $title = static::DASHBOARD_TITLE;

    if ($request->isPost()) {

      $data = $request->getParsedBody();

      $arrayKeys = array_keys($data['individual']);
      $luckyIndividualId = array_pop($arrayKeys);
      unset($arrayKeys);

      $newPopulation = $evolution->getCurrentPopulation()->getIndividuals();
      if (key_exists($luckyIndividualId, $newPopulation)) {
        $dashboardManager->selectForIndividual($luckyIndividualId, $evolution);
        $evolution->runGeneration(false);
        $dashboardManager->saveEvolution(static::DASHBOARD_EVOLUTION_ID, $evolution);
      }
    }

    $generation = $evolution->getGeneration();

    $populationFormItems = [];

    $currentPopulation = $evolution->getCurrentPopulation();

    $currentPopulation->sort();

    foreach ($currentPopulation->getIndividuals() as $id => $individual) {
      /* @var $individual Individual */
      $decorator = IndividualDecoratorFactory::getIndividualDecorator($individual, static::DASHBOARD_RENDER_TYPE);
      $populationFormItems[] = [
        'render' => $decorator->render(),
        'button' => '<input class="individual-pick" type="submit" value="Pick" name="individual[' . $id . ']" />'
      ];
    }

    $statisticsDecorator = new StatisticsDecoratorHtml($evolution->getCurrentPopulation()->getStatistics());
    $statistics .= $statisticsDecorator->render();

    for ($i = 1; $i <= $generation; ++$i) {
      $pastGenerations[] = $i;
    }

    return $this->view->render($response, 'dashboard.twig', [
      'route_name' => static::DASHBOARD_ROUTE_NAME,
      'title' => $title,
      'current_generation' => $generation,
      'population_form_items' => $populationFormItems,
      'statistics' => $statistics,
      'graphs' => $graphs,
      'past_generations' => $pastGenerations,
    ]);
  }
}
