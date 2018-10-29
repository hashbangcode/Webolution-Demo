<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Hashbangcode\WebolutionDemo\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Webolution\Evolution\EvolutionStorage;

class AdminerController extends BaseController
{

  public function adminer(Request $request, Response $response, $args)
  {

    if (!isset($_GET['username']) && !isset($_GET['db']) && !isset($_GET['file'])) {
      $dbSettings = $this->container->get('settings')['database'];
      header('Location: http://localhost:8000/adminer?sqlite=&username=&db=' . $dbSettings['databasefile']);
    }

    include __DIR__ . "/../../database/adminer.php";
  }


  public function clearDatabase(Request $request, Response $response, $args) {

    $evolutionMapper = new \Hashbangcode\WebolutionDemo\Model\Evolution($this->container->database);
    $evolutionMapper->createDatabase();

    return $response->withStatus(302)->withHeader('Location', '/');
  }

}
