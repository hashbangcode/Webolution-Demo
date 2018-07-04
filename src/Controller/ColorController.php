<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Type\Color\Color;
use Hashbangcode\Wevolution\Evolution\Population\ColorPopulation;
use Hashbangcode\Wevolution\Evolution\Individual\ColorIndividual;

class ColorController extends BaseController
{

    public function colorSort(Request $request, Response $response, $args)
    {
        $styles = 'span {width:1px;height:10px;display:inline-block;padding:0px;margin:0px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}
img {padding:0px;margin:0px;}';

        $title = 'Color Evolution Sort';

        $population = new ColorPopulation();
        $population->setDefaultRenderType('html');

        for ($i = 0; $i < 1500; ++$i) {
            $population->addIndividual();
        }

        if (!isset($args['type'])) {
            $args['type'] = '';
        }

        switch ($args['type']) {
            case 'hue':
                $population->sort('hue');
                break;
            case 'hex':
                $population->sort('hex');
                break;
            case 'intensity':
                $population->sort('intensity');
                break;
            case 'hsi_saturation':
                $population->sort('hsi_saturation');
                break;
            case 'hsl_saturation':
                $population->sort('hsl_saturation');
                break;
            case 'hsv_saturation':
                $population->sort('hsv_saturation');
                break;
            case 'luma':
                $population->sort('luma');
                break;
            case 'value':
                $population->sort('value');
                break;
            case 'lightness':
                $population->sort('lightness');
                break;
            case 'fitness':
                $population->sort('fitness');
                break;
            case 'none':
                break;
            default:
                $population->sort();
        }

        $output = '';

        foreach ($population->getIndividuals() as $individual) {
            $output .= $individual->render($population->getDefaultRenderType());
        }

        $output .= '<p>Sort by:';
        $output .= '<ul>';
        $output .= '<li><a href="/color_sort/hue">hue</a></li>';
        $output .= '<li><a href="/color_sort/hex">hex</a></li>';
        $output .= '<li><a href="/color_sort/intensity">intensity</a></li>';
        $output .= '<li><a href="/color_sort/hsi_saturation">hsi_saturation</a></li>';
        $output .= '<li><a href="/color_sort/hsl_saturation">hsl_saturation</a></li>';
        $output .= '<li><a href="/color_sort/hsv_saturation">hsv_saturation</a></li>';
        $output .= '<li><a href="/color_sort/luma">luma</a></li>';
        $output .= '<li><a href="/color_sort/value">value</a></li>';
        $output .= '<li><a href="/color_sort/lightness">lightness</a></li>';
        $output .= '<li><a href="/color_sort/fitness">default fitness (i.e. lightness)</a></li>';
        $output .= '<li><a href="/color_sort">default (i.e. hue)</a></li>';
        $output .= '<li><a href="/color_sort/none">no sort</a></li>';
        $output .= '</p>';

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function colorEvolution($request, $response, $args)
    {
        $styles = 'span {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}';

        $title = 'Color Evolution Test';

        $population = new ColorPopulation();
        $population->setDefaultRenderType('html');
        $population->setPopulationFitnessType('hue');

        for ($i = 0; $i < 40; ++$i) {
            $population->addIndividual(ColorIndividual::generateFromRgb(255, 255, 255));
        }

        $evolution = new Evolution($population);
        $evolution->setIndividualsPerGeneration(40);
        $evolution->setMaxGenerations(200);
        $evolution->setGlobalMutationFactor(5);
        $evolution->setGlobalMutationAmount(5);

        $output = '';

        for ($i = 0; $i < $evolution->getMaxGenerations(); ++$i) {
            $evolution->runGeneration();
        }

        $output .= $evolution->renderGenerations(true, 'html');

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function colorEvolutionInteractive(Request $request, Response $response, $args)
    {
        $title = 'Color Evolution Test';

        $styles = 'span {width:30px;height:30px;display:inline-block;padding:0px;margin:-2px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}
img {padding:0px;margin:0px;}';

        $population = new ColorPopulation();
        $population->setDefaultRenderType('html');

        $output = '';

        if (!isset($args['color'])) {
            $colorObject = Color::generateFromHex('000000');
        } else {
            $colorObject = Color::generateFromHex($args['color']);
            $output .= '<p><a href="/colour_evolution_interactive">Reset</a></a>';
        }

        $colorIndividual = ColorIndividual::generateFromRgb($colorObject->getRed(), $colorObject->getGreen(), $colorObject->getBlue());

        $population->addIndividual($colorIndividual);

        $evolution = new Evolution($population);

        $evolution->setGlobalMutationFactor(25);
        $evolution->setIndividualsPerGeneration(1000);

        // Run one generation.
        $evolution->runGeneration(false);

        $colorPopulation = $evolution->getCurrentPopulation();

        $colorPopulation->sort('hex');

        $output .= '<p>';

        foreach ($colorPopulation->getIndividuals() as $individual) {
            $output .= '<a href="/colour_evolution_interactive/' . $individual->getObject()->getHex() . '">' . $individual->render('html') . '</a>' . PHP_EOL;
        }
        $output .= '</p>';

        $output .= '<p>Colour<pre>' . $colorObject->render() . '</pre></p>';

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function colorEvolutionStorage(Request $request, Response $response, $args)
    {
        $title = 'Colour Evolution Database Test';

        $styles = 'span {width:30px;height:30px;display:inline-block;padding:0px;margin:-2px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}
img {padding:0px;margin:0px;}';

        $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
        $evolution = new EvolutionStorage();

        $evolution->setEvolutionId(1);

        $evolution->setupDatabase('sqlite:' . $database);

        $evolution->setIndividualsPerGeneration(5000);
        $evolution->setGlobalMutationFactor(1);

        $generation = $evolution->getGeneration();

        $population = new ColorPopulation();
        $population->setDefaultRenderType('html');

        if ($generation == 1) {
            $population->addIndividual();

            $evolution->setPopulation($population);
        } else {
            $evolution->setPopulation($population);
            $evolution->loadPopulation();
        }

        $evolution->runGeneration();

        $output = '';

        $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

        $output .= nl2br($evolution->renderGenerations());

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }
}
