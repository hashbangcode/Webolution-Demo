<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\Webolution\Evolution\Individual\Decorators\IndividualDecoratorFactory;
use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\EvolutionStorage;
use Hashbangcode\WebolutionDemo\Model\Individual as IndividualModel;

class IndividualController extends BaseController
{


  public function viewIndividual(Request $request, Response $response, $args)
  {
    $title = 'Individual';

    $individualId = $args['individual'];

    $individualMapper = new IndividualModel($this->container->database);

    $individual = $individualMapper->load($individualId);
    $decorator = IndividualDecoratorFactory::getIndividualDecorator($individual, 'iframe');
    $decorator->setHeight(1000);
    $decorator->setWidth(1000);

    return $this->view->render($response, 'individual.twig', [
      'title' => $title,
      'individual' => $decorator->render(),
    ]);
  }

}
