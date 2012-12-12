<?php

namespace Nerd\View\Locator;

interface LocatorInterface
{
    public function __construct();

    public function resolve($path);
}