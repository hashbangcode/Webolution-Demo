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

// Number.
$app->get('/number_evolution', '\Hashbangcode\WebolutionDemo\Controller\NumberController:numberEvolution');
//$app->map(['GET', 'POST'], '/number_evolution_form', '\Hashbangcode\WebolutionDemo\Controller\NumberController:numberEvolutionForm');

// Color.
$app->get('/color_evolution', '\Hashbangcode\WebolutionDemo\Controller\ColorController:colorEvolution');
$app->get('/color_evolution_database[/{step}]', '\Hashbangcode\WebolutionDemo\Controller\ColorDatabaseController:colorEvolution');

// Image.
$app->get('/image_evolution', '\Hashbangcode\WebolutionDemo\Controller\ImageController:imageEvolution');

// Dashboard
$app->get('/color_dashboard_evolution', '\Hashbangcode\WebolutionDemo\Controller\ColorDashboardController:colorDashboardEvolution');
