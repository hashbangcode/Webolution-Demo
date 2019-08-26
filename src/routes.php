<?php
/**
 * @file routes.php
 *
 * Sets up the routes of the application.
 */

// Main.
$app->get('/', '\Hashbangcode\WebolutionDemo\Controller\HomeController:home');

// Adminer.
$app->any('/adminer', '\Hashbangcode\WebolutionDemo\Controller\AdminerController:adminer');
$app->any('/clear_database', '\Hashbangcode\WebolutionDemo\Controller\AdminerController:clearDatabase');

// Text
$app->get('/text_evolution', '\Hashbangcode\WebolutionDemo\Controller\TextController:textEvolution');

// Number.
$app->get('/number_evolution', '\Hashbangcode\WebolutionDemo\Controller\NumberController:numberEvolution');
//$app->map(['GET', 'POST'], '/number_evolution_form', '\Hashbangcode\WebolutionDemo\Controller\NumberController:numberEvolutionForm');

// Color.
$app->get('/color_evolution', '\Hashbangcode\WebolutionDemo\Controller\ColorController:colorEvolution');
$app->get('/color_evolution_database[/{step}]', '\Hashbangcode\WebolutionDemo\Controller\ColorDatabaseController:colorEvolution');

// Image.
$app->get('/image_evolution', '\Hashbangcode\WebolutionDemo\Controller\ImageController:imageEvolution');
$app->get('/image_evolution_database[/{step}]', '\Hashbangcode\WebolutionDemo\Controller\ImageDatabaseController:imageEvolution');

// Page.
$app->get('/page_evolution', '\Hashbangcode\WebolutionDemo\Controller\PageController:pageEvolution');

// Dashboard
$app->map(['get', 'post'], '/color_dashboard_evolution[/{params:.*}]', '\Hashbangcode\WebolutionDemo\Controller\ColorDashboardController:dashboardEvolution')->setName('color_dashboard_evolution');
$app->map(['get', 'post'], '/image_dashboard_evolution[/{params:.*}]', '\Hashbangcode\WebolutionDemo\Controller\ImageDashboardController:dashboardEvolution')->setName('image_dashboard_evolution');
$app->map(['get', 'post'], '/page_dashboard_evolution[/{params:.*}]', '\Hashbangcode\WebolutionDemo\Controller\PageDashboardController:dashboardEvolution')->setName('page_dashboard_evolution');

$app->get('/individual/{individual}', '\Hashbangcode\WebolutionDemo\Controller\IndividualController:viewIndividual')->setName('individual');
