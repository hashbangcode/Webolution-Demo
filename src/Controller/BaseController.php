<?php

namespace Hashbangcode\WebolutionDemo\Controller;

use Interop\Container\ContainerInterface;

/**
 * Class BaseController.
 *
 * Sets up some default dependencies for sub classes to use.
 *
 * @package Hashbangcode\WebolutionDemo\Controller
 */
abstract class BaseController
{

  /**
   * Container.
   *
   * @var \Interop\Container\ContainerInterface
   */
  protected $container;

  /**
   * The templating engine.
   *
   * @var \Slim\Views\Twig
   */
  protected $view;

  /**
   * The logger.
   *
   * @var \Monolog\Logger
   */
  protected $logger;

  /**
   * BaseController constructor.
   *
   * @param \Interop\Container\ContainerInterface $container
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->view = $this->container->view;
    $this->logger = $this->container->logger;
  }
}
