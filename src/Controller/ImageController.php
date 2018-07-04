<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Hashbangcode\Wevolution\Demos\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashbangcode\Wevolution\Evolution\Evolution;
use Hashbangcode\Wevolution\Evolution\EvolutionStorage;
use Hashbangcode\Wevolution\Evolution\Population\ImagePopulation;
use Hashbangcode\Wevolution\Evolution\Individual\ImageIndividual;

class ImageController extends BaseController
{

    public function image(Request $request, Response $response, $args)
    {
        $this->logger->info("Image '/image' route");

        $title = 'Image Test';

        $styles = "img{border:1px solid black;}";

        $output = '';

        $x = 100;
        $y = 100;

        $image1 = ImageIndividual::generateFromImageSize($x, $y);

        for ($i = 0; $i < 1000; ++$i) {
            $image1->getObject()->setPixel(rand(0, $x - 1), rand(0, $y - 1), 1);
        }

        $output .= '<p>Random Image</p>';
        $output .= $image1->render('image');

        $image2 = ImageIndividual::generateFromImageSize(5, 5);
        $image2->getObject()->setPixel(3, 2, 1);
        $image2->getObject()->setPixel(4, 2, 1);

        $output .= '<p>Image Height Test</p>';
        $output .= $image2->render('image');
        $output .= $image2->getFitness('height');

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles,
        ]);
    }

    public function imageSort(Request $request, Response $response, $args)
    {
        $this->logger->info("Image '/image_sort' route");

        $title = 'Image Sort Test';

        $styles = "img{border:1px solid black;}";

        $output = '';

        $object = new ImagePopulation();
        $object->setDefaultRenderType('image');

        // Create first iamge.
        $image1 = ImageIndividual::generateFromImageSize(10, 10);
        $image1->getObject()->setPixel(9, 5, 1);
        $image1->getObject()->setPixel(8, 5, 1);
        $image1->getObject()->setPixel(7, 5, 1);
        $image1->getObject()->setPixel(6, 5, 1);
        $image1->getObject()->setPixel(5, 5, 1);
        $image1->getObject()->setPixel(4, 5, 1);
        $object->addIndividual($image1);

        // Create second image.
        $image2 = ImageIndividual::generateFromImageSize(10, 10);
        $image2->getObject()->setPixel(9, 5, 1);
        $image2->getObject()->setPixel(8, 5, 1);
        $image2->getObject()->setPixel(7, 5, 1);
        $object->addIndividual($image2);

        // Create third iamge.
        $image3 = ImageIndividual::generateFromImageSize(10, 10);
        $image3->getObject()->setPixel(9, 5, 1);
        $image3->getObject()->setPixel(8, 5, 1);
        $object->addIndividual($image3);

        // Create fourth image.
        $image4 = ImageIndividual::generateFromImageSize(10, 10);
        $image4->getObject()->setPixel(9, 5, 1);
        $image4->getObject()->setPixel(8, 5, 1);
        $image4->getObject()->setPixel(7, 5, 1);
        $image4->getObject()->setPixel(6, 5, 1);
        $image4->getObject()->setPixel(5, 5, 1);
        $object->addIndividual($image4);

        // Create fifth iamge.
        $image5 = ImageIndividual::generateFromImageSize(10, 10);
        $image5->getObject()->setPixel(9, 5, 1);
        $object->addIndividual($image5);

        // Create sixth iamge.
        $image6 = ImageIndividual::generateFromImageSize(10, 10);
        $image6->getObject()->setPixel(9, 2, 1);
        $image6->getObject()->setPixel(8, 2, 1);
        $image6->getObject()->setPixel(7, 2, 1);
        $object->addIndividual($image6);

        $output .= '<p>Sort Ascending</p>';
        $object->sort();

        foreach ($object->getIndividuals() as $individual) {
            $output .= $individual->render($object->getDefaultRenderType());
        }

        $output .= '<p>Sort Descending</p>';

        $object->sort('DESC');

        foreach ($object->getIndividuals() as $individual) {
            $output .= $individual->render($object->getDefaultRenderType());
        }

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles,
        ]);
    }

    public function imageEvolution(Request $request, Response $response, $args)
    {
        $this->logger->info("Image Eovolution '/image_evolution' route");

        $title = 'Image Evolution Test';

        $styles = "img{border:1px solid black;}";

        $population = new ImagePopulation();
        $population->setPopulationFitnessType('height');
        $population->setDefaultRenderType('image');

        for ($i = 0; $i < 10; $i++) {
            $image = ImageIndividual::generateFromImageSize(25, 25);
            $image->getObject()->setPixel(24, 12, 1);
            $population->addIndividual($image);
        }

        $evolution = new Evolution($population, false);
        $evolution->setIndividualsPerGeneration(10);
        $evolution->setMaxGenerations(100);
        $evolution->setReplicationType(Evolution::REPLICATION_TYPE_CROSSOVER);

        $output = '';

        for ($i = 0; $i < $evolution->getMaxGenerations(); ++$i) {
            if ($evolution->runGeneration() === false) {
                // @todo : why does this sometimes appear randomly?
                $output .= '<p>Everyone is dead.</p>';
                break;
            }
        }

        $output .= $evolution->renderGenerations();

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles,
        ]);
    }

    public function imageEvolutionStorage(Request $request, Response $response, $args)
    {
        $title = 'Image Evolution Database Test';

        $styles = 'img{border:1px solid black;} span {width:30px;height:30px;display:inline-block;padding:0px;margin:-2px;}
a, a:link, a:visited, a:hover, a:active {padding:0px;margin:0px;}';

        $database = realpath(__DIR__ . '/../../database') . '/database.sqlite';
        $evolution = new EvolutionStorage();

        $evolution->setEvolutionId(6);

        $evolution->setupDatabase('sqlite:' . $database);

        $evolution->setIndividualsPerGeneration(100);

        $generation = $evolution->getGeneration();

        $population = new ImagePopulation();
        $population->setDefaultRenderType('image');

        if ($generation == 1) {
            for ($i = 0; $i < 10; $i++) {
                $image = ImageIndividual::generateFromImageSize(25, 25);
                $image->getObject()->setPixel(24, 12, 1);
                $population->addIndividual($image);
            }

            $evolution->setPopulation($population);
        } else {
            $evolution->setPopulation($population);
            $evolution->loadPopulation();
        }

        $evolution->runGeneration();

        $output = '';

        $output .= '<p>Generation: ' . $evolution->getGeneration() . '</p>';


        $population = $evolution->getCurrentPopulation();

        //$population->sort();

        $output .= '<p>';

        foreach ($population->getIndividuals() as $individual) {
            $output .= '<a href="/image_evolution_storage/' . $individual->getObject()->getPixel(0,0) . '">' . $individual->render('image') . '</a>' . PHP_EOL;
        }
        $output .= '</p>';

        //$output .= nl2br($evolution->renderGenerations());

        return $this->view->render($response, 'demos.twig', [
            'title' => $title,
            'output' => $output,
            'styles' => $styles,
            'meta' => '<meta http-equiv="refresh" content="5" >',
        ]);
    }
}
