<?php

namespace Hashbangcode\Wevolution\Demos\Controller;

use Interop\Container\ContainerInterface;

abstract class BaseController
{
    protected $container;

    protected $view;

    protected $logger;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $this->container->view;
        $this->logger = $this->container->logger;
    }
}
