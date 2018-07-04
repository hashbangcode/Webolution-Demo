<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Hashbangcode\Wevolution\Evolution\Individual\Decorators\IndividualDecoratorFactory;
use Hashbangcode\Wevolution\Evolution\Population\StylePopulation;
use Hashbangcode\Wevolution\Type\Page\Page;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\Population\ElementPopulation;
use Hashbangcode\Wevolution\Type\Element\Element;
use Hashbangcode\Wevolution\Type\Style\Style;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Evolution\Individual\ElementIndividual;
use Hashbangcode\Wevolution\Evolution\Individual\StyleIndividual;
use Hashbangcode\Wevolution\Evolution\Individual\PageIndividual;
use Hashbangcode\Wevolution\Evolution\Population\PagePopulation;

class PageController extends BaseController
{

    public function page(Request $request, Response $response, $args)
    {
        $styles = 'div {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}.elementframe{width:500px;height:500px;}';

        $title = 'Page Test';

        $output = '';

        $page = new Page();

        $pageIndividual = new PageIndividual($page);

        $style = new Style('div');
        $style->setAttribute('font-size', '20px');
        $page->setStyle($style);

        $body = new Element('div');
        $p = new Element('p');
        $body->addChild($p);

        $pageIndividual->getObject()->setBody($body);

        $pageIndividualDecorator = IndividualDecoratorFactory::getIndividualDecorator($pageIndividual, 'html');

        $pageHtml = $pageIndividualDecorator->render('html');

        $output .= '<iframe class="elementframe" height="200" width="200" srcdoc=\'' . $pageHtml . '\'></iframe>';
        $output .= '<textarea rows="35" cols="35">' . $pageHtml . '</textarea>';

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function pageEvolution(Request $request, Response $response, $args)
    {
        $styles = 'div {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}.elementframe{width:500px;height:500px;}';

        $title = 'Page Test';

        $population = new PagePopulation();

        $population->setDefaultRenderType('htmlfull');

        // Setup evolution storage.
        $evolution = new Evolution();
        $evolution->setIndividualsPerGeneration(8);
        $evolution->setMaxGenerations(100);

        $pageIndividual = PageIndividual::generateBlankPage();

        $div = new Element('div');

        $p = new Element('p');
        $ul = new Element('ul');
        $li = new Element('li');

        $div->addChild($p);
        $p->addChild($ul);
        $ul->addChild($li);

        $pageIndividual->getObject()->setBody($div);

        $pageIndividual->getObject()->generateStylesFromBody();

        $population->addIndividual($pageIndividual);

        $evolution->setPopulation($population);

        $output = '';

        for ($i = 0; $i < $evolution->getMaxGenerations(); ++$i) {
            if ($evolution->runGeneration() === false) {
                $output .= '<p>Everyone is dead.</p>';
                break;
            }
        }

        $output .= $evolution->renderGenerations(false, 'iframe');

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function pageEvolutionStorage(Request $request, Response $response, $args)
    {
        $styles = 'div {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}.elementframe{width:500px;height:500px;}';

        $title = 'Page Storage Test';

        $population = new PagePopulation();

        $population->setDefaultRenderType('htmlfull');

        // Setup evolution storage.
        $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
        $evolution = new EvolutionStorage();

        $evolution->setEvolutionId(4);

        $evolution->setupDatabase('sqlite:' . $database);

        $evolution->setIndividualsPerGeneration(10);

        // Get generation.
        $generation = $evolution->getGeneration();

        if ($generation == 1) {
            $pageIndividual = PageIndividual::generateBlankPage();

            $p = new Element('p');
            $ul = new Element('ul');
            $li = new Element('li');

            $p->addChild($ul);
            $ul->addChild($li);

            $pageIndividual->getObject()->setBody($p);

            $style_object = new Style('.text');
            $pageIndividual->getObject()->setStyle($style_object);

            $population->addIndividual($pageIndividual);

            $evolution->setPopulation($population);
        } else {
            $evolution->setPopulation($population);
            $evolution->loadPopulation();
        }

        // Run generation.
        $evolution->runGeneration(false, 'iframe');

        $output = '';

        $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

        $output .= $evolution->renderGenerations();

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles
        ]);
    }

    public function pageEvolutionForm(Request $request, Response $response, $args)
    {
        $title = 'Page Evolution Form Test';

        $styles = '';
        $styles .= 'div {width:10px;height:10px;display:inline-block;padding:0px;margin:0px;}';
        $styles .= '.elementframe{width:500px;height:500px;}';
        /* Double-sized Checkboxes */
        $styles .= 'input[type=checkbox] {transform: scale(3);}';

        $individualsPerGeneration = 6;

        // Setup EvolutionStorage object.
        $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
        $evolution = new EvolutionStorage();
        $evolution->setEvolutionId(7);
        $evolution->setupDatabase('sqlite:' . $database);
        //$evolution->setMaxGenerations(-1);
        $evolution->setIndividualsPerGeneration($individualsPerGeneration);

        // Create the population.
        $population = new PagePopulation();
        $population->setDefaultRenderType('htmlfull');

        if ($request->isPost()) {
            // Run form.
            $parameters = $request->getParsedBody();

            // Load the population from the database.
            $evolution->loadPopulation();

            if (isset($parameters['generation']) && $parameters['generation'] + 1  == $evolution->getGeneration()) {
                // Page has been refreshed, reload the population with a previous generation.
                $evolution->setGeneration($parameters['generation']);
                $evolution->loadPopulation();
            }

            foreach ($evolution->getCurrentPopulation()->getIndividuals() as $key => $individual) {
                if (!in_array($key, $parameters)) {
                    $evolution->getCurrentPopulation()->removeIndividual($key);
                }
            }

            // Increment the generation.
            $evolution->incrementGeneration();

            // Run the evolution.
            $evolution->runGeneration(false, false);
        } else {
            $evolution->clearDatabase();

            for ($i = 0; $i < $individualsPerGeneration; ++$i) {
                $population->addIndividual();
            }

            $evolution->setPopulation($population);
        }

        $evolution->getCurrentPopulation()->sort();

        $output = '';

        $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

        $output .= '<form action="/page_evolution_form" method="post">';

        $population = $evolution->getCurrentPopulation();

        foreach ($population->getIndividuals() as $key => $individual) {
            $output .= '<h2>' . $key . '</h2>';
            $output .= '<iframe class="elementframe" height="450" width="400" srcdoc=\'' . $individual->render('html') . '\'></iframe>';
            $output .= '<textarea rows="35" cols="35">' . $individual->render('html') . '</textarea>';
            $output .= '<input type="checkbox" name="' . $key . '" value="' . $key . '" />';
            $output .= '<br>';
        }

        $output .= '<input type="submit" value="Run" />';
        $output .= '<input type="hidden" value="' . $evolution->getGeneration() . '" name="generation" />';
        $output .= '</form>';

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles,
        ]);
    }
}
