<?php

namespace Nerd\View;

class Manager
{
    protected $locator;

    public function __construct(Locator\LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function get($view, array $data = [])
    {
        return new View($view, $data, $this->locator->resolve($view));
    }
}