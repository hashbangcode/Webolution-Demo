<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Individual\Decorators\IndividualDecoratorFactory;
use Hashbangcode\Webolution\Evolution\Individual\Individual;
use Hashbangcode\Webolution\Type\Color\Color;
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
 * Class ColorDashboardController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class ColorDashboardController extends BaseDashboardController
{

  const DASHBOARD_TYPE = 'Color';

  const DASHBOARD_TITLE = 'Color Dashboard';

  const DASHBOARD_PATH = '/color_dashboard_evolution';

  const DASHBOARD_EVOLUTION_ID = 101;

  const DASHBOARD_ROUTE_NAME = 'color_dashboard_evolution';

  const DASHBOARD_RENDER_TYPE = 'html';

  public function generateIndividuals() {
    $individuals = [];
    for ($i = 0; $i < static::NUMBER_OF_INDIVIDUALS; $i++) {
      $color = ColorIndividual::generateRandomColor();
      $individuals[] = $color;
    }
    return $individuals;
  }
}
