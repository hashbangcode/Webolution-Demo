<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\IndividualDecoratorFactory;
use Hashbangcode\Webolution\Individual;
use Hashbangcode\Webolution\Type\Page\PageIndividual;
use Hashbangcode\Webolution\Type\Color\Color;
use Hashbangcode\Webolution\Type\Element\Element;
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
 * Class ColorDashboardController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class PageDashboardController extends BaseDashboardController
{

  const NUMBER_OF_INDIVIDUALS = 50;

  const DASHBOARD_TYPE = 'Page';

  const DASHBOARD_TITLE = 'Page Dashboard';

  const DASHBOARD_PATH = '/page_dashboard_evolution';

  const DASHBOARD_EVOLUTION_ID = 103;

  const DASHBOARD_ROUTE_NAME = 'page_dashboard_evolution';

  const DASHBOARD_RENDER_TYPE = 'iframe';

  public function generateIndividuals() {
    $individuals = [];
    for ($i = 0; $i < static::NUMBER_OF_INDIVIDUALS; $i++) {
      $page = PageIndividual::generateBlankPage();

      $div = new Element('div');

      $p = new Element('p');
      $ul = new Element('ul');
      $li = new Element('li');

      $div->addChild($p);
      $p->addChild($ul);
      $ul->addChild($li);

      $page->getObject()->setBody($div);

      $page->getObject()->generateStylesFromBody();

      $individuals[] = $page;
    }
    return $individuals;
  }
}
