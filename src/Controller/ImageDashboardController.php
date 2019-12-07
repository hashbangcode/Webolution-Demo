<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Type\Image\ImageIndividual;

class ImageDashboardController extends BaseDashboardController
{
  const NUMBER_OF_INDIVIDUALS = 100;

  const DASHBOARD_TYPE = 'Image';

  const DASHBOARD_TITLE = 'Image Dashboard';

  const DASHBOARD_PATH = '/image_dashboard_evolution';

  const DASHBOARD_EVOLUTION_ID = 102;

  const DASHBOARD_ROUTE_NAME = 'image_dashboard_evolution';

  const DASHBOARD_RENDER_TYPE = 'html';

  public function generateIndividuals() {
    $individuals = [];
    for ($i = 0; $i < static::NUMBER_OF_INDIVIDUALS; $i++) {
      $image = ImageIndividual::generateFromImageSize(25, 25);
      $image->getObject()->setPixel(24, 12, 1);
      $individuals[] = $image;
    }
    return $individuals;
  }

}
