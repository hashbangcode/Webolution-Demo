<?php
/**
 * @file routes.php
 *
 * Sets up the routes of the application.
 */

// Main.
$app->get('/', '\Hashbangcode\WevolutionDemo\Controller\HomeController:home');

// Adminer.
$app->any('/adminer', '\Hashbangcode\WevolutionDemo\Controller\AdminerController:adminer');
$app->any('/clear_database', '\Hashbangcode\WevolutionDemo\Controller\AdminerController:clearDatabase');

// Number.
$app->get('/number_evolution', '\Hashbangcode\WevolutionDemo\Controller\NumberController:numberEvolution');
//$app->map(['GET', 'POST'], '/number_evolution_form', '\Hashbangcode\WevolutionDemo\Controller\NumberController:numberEvolutionForm');

// Color.
$app->get('/color_evolution', '\Hashbangcode\WevolutionDemo\Controller\ColorController:colorEvolution');

// Image.
$app->get('/image_evolution', '\Hashbangcode\WevolutionDemo\Controller\ImageController:imageEvolution');

// Dashboard
$app->get('/color_dashboard_evolution', '\Hashbangcode\WevolutionDemo\Controller\ColorDashboardController:colorDashboardEvolution');
