<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Hashbangcode\Wevolution\Evolution\EvolutionManager;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\Population\NumberPopulation;
use Hashbangcode\Wevolution\Evolution\Individual\NumberIndividual;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;

class NumberController extends BaseController
{

    public function numberEvolution(Request $request, Response $response, $args)
    {
        $this->logger->info("Number Eovolution '/number_evolution' route");

        $title = 'Number Evolution Test';

        // Setup the population.
        $population = new NumberPopulation();
        $population->setDefaultRenderType('html');

        // Add individuals to the population.
        for ($i = 0; $i < 30; $i++) {
            $population->addIndividual(NumberIndividual::generateFromNumber(1));
        }

        // Create the EvolutionManager object and add the population to it.
        $evolution = new EvolutionManager();
        $evolution->getEvolutionObject()->setPopulation($population);
        $evolution->getEvolutionObject()->setReplicationType('crossover');
        $evolution->runEvolution();

        $output = '';
        $output .= $evolution->getEvolutionObject()->renderGenerations(true);

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output
        ]);
    }

    public function numberEvolutionForm(Request $request, Response $response, $args)
    {
        $title = 'Number Evolution Form Test';

        $individualsPerGeneration = 6;

        // Setup EvolutionStorage object.
        $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
        $evolution = new EvolutionStorage();
        $evolution->setEvolutionId(6);
        $evolution->setupDatabase('sqlite:' . $database);
        //$evolution->setMaxGenerations(-1);
        $evolution->setIndividualsPerGeneration($individualsPerGeneration);

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
            // Create the population.
            $population = new NumberPopulation();
            $population->setDefaultRenderType('html');

            $evolution->clearDatabase();

            for ($i = 0; $i < $individualsPerGeneration; ++$i) {
                $population->addIndividual();
            }

            $evolution->setPopulation($population);
        }

        $evolution->getCurrentPopulation()->sort();

        $output = '';

        $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';

        $output .= '<form action="/number_evolution_form" method="post">';

        $population = $evolution->getCurrentPopulation();

        foreach ($population->getIndividuals() as $key => $individual) {
            $output .= $individual->render();
            $output .= '(' . $key . ')' . '<input type="checkbox" name="' . $key . '" value="' . $key . '" /><br>';
        }

        $output .= '<input type="submit" value="Run" />';

        $output .= '<input type="hidden" value="' . $evolution->getGeneration() . '" name="generation" />';

        $output .= '</form>';

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output
        ]);
    }
}
