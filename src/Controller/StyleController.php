<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\Population\StylePopulation;
use Hashbangcode\Wevolution\Type\Style\Style;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Evolution\Individual\StyleIndividual;

class StyleController extends BaseController
{

    public function styleEvolution(Request $request, Response $response, $args)
    {
        $styles = 'div {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}';

        $title = 'Style Test';

        $population = new StylePopulation();
        $population->setDefaultRenderType('html');

        for ($i = 0; $i < 10; $i++) {
            $population->addIndividual(StyleIndividual::generateFromSelector('div'));
        }

        $evolution = new Evolution($population);
        $evolution->setIndividualsPerGeneration(10);
        $evolution->setMaxGenerations(100);
        $evolution->setGlobalMutationFactor(1);

        $output = '';

        for ($i = 0; $i < $evolution->getMaxGenerations(); ++$i) {
            if ($evolution->runGeneration() === false) {
                print '<p>Everyone is dead.</p>';
                break;
            }
        }

        $output .= $evolution->renderGenerations();

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }
}
