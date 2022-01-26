<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\IndividualDecoratorFactory;
use Hashbangcode\Webolution\Individual;
use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Hashbangcode\WebolutionDemo\DashboardManager;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution;
use Hashbangcode\Webolution\Type\Color\ColorPopulation;
use Hashbangcode\Webolution\Type\Color\ColorIndividual;
use Hashbangcode\Webolution\EvolutionManager;
use Hashbangcode\Webolution\PopulationDecoratorFactory;
use Hashbangcode\Webolution\Statistics\Decorator\StatisticsDecoratorHtml;
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
      for ($i = 0; $i < 5; ++$i) {
        $evolution->runGeneration(false);
        $dashboardManager->saveEvolution(static::DASHBOARD_EVOLUTION_ID, $evolution);
      }

      return $response->withStatus(302)->withHeader('Location', static::DASHBOARD_PATH);
    }

    $title = static::DASHBOARD_TITLE;

    if ($request->isPost()) {

      $data = $request->getParsedBody();

      if (isset($data['individual-download'])) {
        $arrayKeys = array_keys($data['individual-download']);
        $downloadId = array_pop($arrayKeys);
        $individual = $dashboardManager->loadIndividual($downloadId);

        $decorator = IndividualDecoratorFactory::getIndividualDecorator($individual, 'html');
        $html = $decorator->render();

        $filename = "individual" . $downloadId . ".html";

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $stream = fopen('data://text/html;base64,' . base64_encode($html),'r');
        echo stream_get_contents($stream);

        return '';
      }
      else {

        $pickedIndividuals = array_keys($data['individual-pick']);

        $newPopulation = [];
        $oldPopulation = $evolution->getCurrentPopulation()->getIndividuals();
        $oldPopulationKeys = array_keys($oldPopulation);

        foreach ($pickedIndividuals as $individual) {
          if (in_array($individual, $oldPopulationKeys)) {
            $newPopulation[$individual] = $oldPopulation[$individual];
          }
        }

        $evolution->getCurrentPopulation()->setIndividuals($newPopulation);
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
        'download_button' => '<input class="individual-download" type="submit" value="Download" name="individual-download[' . $id . ']" />',
        'pick_checkbox' => '<label class="pick-label" for="individual-pick[' . $id . ']"><input class="individual-pick" type="checkbox" name="individual-pick[' . $id . ']" id="individual-pick[' . $id . ']" />Chosen</label>',
        'id' => $id,
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
      //'past_generations' => $pastGenerations,
    ]);
  }
}
