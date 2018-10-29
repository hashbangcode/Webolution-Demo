<?php
/**
 * @file settings.php
 *
 * Sets up some default settings for the application.
 */

return [
    'settings' => [
        // Set to false in production.
        'displayErrorDetails' => true,

         // Allow the web server to send the content-length header.
        'addContentLengthHeader' => false,

        // Renderer settings.
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        'database' => [
          'databasefile' => realpath(__DIR__ . '/../database') . '/evolutiondatabase.sqlite',
        ],

        // Monolog settings.
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
