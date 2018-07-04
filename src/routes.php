<?php

// Routes

// Main.
$app->get('/', '\Hashbangcode\Wevolution\Demos\Controller\HomeController:home');

// Adminer.
$app->any('/adminer', '\Hashbangcode\Wevolution\Demos\Controller\AdminerController:adminer');
$app->any('/clear_database', '\Hashbangcode\Wevolution\Demos\Controller\AdminerController:clearDatabase');

// Number.
$app->get('/number_evolution', '\Hashbangcode\Wevolution\Demos\Controller\NumberController:numberEvolution');
$app->map(['GET', 'POST'], '/number_evolution_form', '\Hashbangcode\Wevolution\Demos\Controller\NumberController:numberEvolutionForm');

// Color.
$app->get('/color_sort[/{type}]', '\Hashbangcode\Wevolution\Demos\Controller\ColorController:colorSort');
$app->get('/color_evolution', '\Hashbangcode\Wevolution\Demos\Controller\ColorController:colorEvolution');
$app->get('/colour_evolution_interactive[/{color}]', '\Hashbangcode\Wevolution\Demos\Controller\ColorController:colorEvolutionInteractive');
$app->get('/color_evolution_storage[/{evolutionid}]', '\Hashbangcode\Wevolution\Demos\Controller\ColorController:colorEvolutionStorage');

// Text.
$app->get('/text_evolution', '\Hashbangcode\Wevolution\Demos\Controller\TextController:textEvolution');
$app->get('/text_evolution_length', '\Hashbangcode\Wevolution\Demos\Controller\TextController:textEvolutionLength');

// Style.
$app->get('/style_evolution', '\Hashbangcode\Wevolution\Demos\Controller\StyleController:styleEvolution');

// Image.
$app->get('/image', '\Hashbangcode\Wevolution\Demos\Controller\ImageController:image');
$app->get('/image_sort', '\Hashbangcode\Wevolution\Demos\Controller\ImageController:imageSort');
$app->get('/image_evolution', '\Hashbangcode\Wevolution\Demos\Controller\ImageController:imageEvolution');
$app->get('/image_evolution_storage[/{evolutionid}]', '\Hashbangcode\Wevolution\Demos\Controller\ImageController:imageEvolutionStorage');

// Element.
$app->get('/element', '\Hashbangcode\Wevolution\Demos\Controller\ElementController:element');
$app->get('/element_evolution', '\Hashbangcode\Wevolution\Demos\Controller\ElementController:elementEvolution');
$app->get('/element_evolution_storage', '\Hashbangcode\Wevolution\Demos\Controller\ElementController:elementEvolutionStorage');

// Page.
$app->get('/page', '\Hashbangcode\Wevolution\Demos\Controller\PageController:page');
$app->get('/page_evolution', '\Hashbangcode\Wevolution\Demos\Controller\PageController:pageEvolution');
$app->map(['GET', 'POST'], '/page_evolution_form', '\Hashbangcode\Wevolution\Demos\Controller\PageController:pageEvolutionForm');
$app->get('/page_evolution_storage[/{evolutionid}]', '\Hashbangcode\Wevolution\Demos\Controller\PageController:pageEvolutionStorage');
