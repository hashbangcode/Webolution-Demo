<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Type\Color\ColorIndividual;

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
