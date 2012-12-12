<?php

namespace Nerd\View\Locator;

class FileLocator implements LocatorInterface
{
    protected $extension = '.php';
    protected $path;

    public function __construct()
    {
        $this->path = func_get_arg(0);

        if (!is_dir($this->path)) {
            throw new \InvalidArgumentException("View path [$this->path] is not a valid directory");
        }
    }

    public function resolve($view = null)
    {
        return $this->path;
    }
}