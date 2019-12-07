<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Type\Page\PageIndividual;
use Hashbangcode\Webolution\Type\Page\PagePopulation;
use Hashbangcode\Webolution\Type\Element\Element;
use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution;
use Hashbangcode\Webolution\Type\Image\ImagePopulation;
use Hashbangcode\Webolution\Type\Image\ImageIndividual;
use Hashbangcode\Webolution\EvolutionManager;

/**
 * Class PageController.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
class PageController extends BaseController
{

  public function pageEvolution(Request $request, Response $response, $args)
  {
    $styles = "img{border:1px solid black;}";

    $this->logger->info("Page Evolution '/page_evolution' route");

    $title = 'Page Evolution';

    // Setup the population.
    $population = new PagePopulation();
    ///$population->setPopulationFitnessType('height');
    $population->setDefaultRenderType('iframe');
    //$population->setPopulationFitnessType('hue');

    // Add individuals to the population.
    for ($i = 0; $i < 10; $i++) {
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

      $population->addIndividual($page);
    }

    // Create the EvolutionManager object and add the population to it.
    $evolution = new EvolutionManager();
    $evolution->getEvolutionObject()->setPopulation($population);
    $evolution->getEvolutionObject()->setMaxGenerations(100);
    $evolution->getEvolutionObject()->setIndividualsPerGeneration(10);
    $evolution->getEvolutionObject()->setGlobalMutationAmount(5);
    //$evolution->getEvolutionObject()->setReplicationType('crossover');
    $evolution->runEvolution();

    $output = '';
    $output .= $evolution->getEvolutionObject()->renderGenerations(TRUE, 'iframe');

    return $this->view->render($response, 'demos.twig', [
      'title' => $title,
      'output' => $output,
      'styles' => $styles,
    ]);
  }
}
