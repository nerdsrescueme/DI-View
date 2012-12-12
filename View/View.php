<?php

namespace Nerd\View;

class View
{
    /**
     * Globally defined data available to all views
     *
     * @var    array
     */
    public static $global_data = [];


    /**
     * Add a key/value pair to the global view data.
     *
     * Bound data will be available to all views as variables.
     *
     * @param     string|array
     * @param     mixed|null
     * @return View
     */
    public static function set($keys, $value = null)
    {
        if (!is_array($keys)) {
            static::$global_data[$keys] = $value;
        } else {
            foreach ($keys as $key => $data) {
                static::$global_data[$key] = $data;
            }
        }
    }

    /**
     * The view data
     *
     * @var     array
     */
    public $data = [];

    /**
     * The name of the view
     *
     * @var     string
     */
    protected $view;

    /**
     * The view name with dots replace by slashes
     *
     * @var     string
     */
    protected $path;

    /**
     * Create a new instance of the View class
     *
     * @param     string
     * @param     array
     * @param     string
     * @return View
     */
    public function __construct($view, $data = [], $path)
    {
        // this shouldn't be here
        $this->view = $view;
        $this->data = $data;

        if (substr(strrchr($view, '.'), 1) !== false) {
            if (file_exists($this->path = $path.'/'.$view)) {
                return $this;
            }
        }

        if (!file_exists($this->path = $path.'/'.$view.'.php')) {
            throw new \InvalidArgumentException("View [{$this->path}] does not exist");
        }
    }

    /**
     * Magic method for getting items from the view data
     *
     * @param     string
     * @return mixed
     */
    public function __get($key)
    {
        return $this->data[$key];
    }

    /**
     * Magic method for setting items in the view data
     *
     * @param     string
     * @param     mixed
     * @return void
     */
    public function __set($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Magic method for determining if an item is in the view data
     *
     * @param     string
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic method for removing an item from the view data.
     *
     * @param     string
     * @return void
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Add a view instance to the view data.
     *
     * @param     string
     * @param     string
     * @param     array
     * @return View
     */
    public function partial($key, $view, $data = [], $path = null)
    {
        return $this->with($key, new static($view, $data, $path));
    }

    /**
     * Add a key/value pair to the view data.
     *
     * Bound data will be available to the view as variables.
     *
     * @param     string|array
     * @param     mixed|null
     * @return View
     */
    public function with($keys, $value = null)
    {
        if (!\is_array($keys)) {
            $this->data[$keys] = $value;
        } else {
            foreach ($keys as $key => $data) {
                $this->data[$key] = $data;
            }
        }

        return $this;
    }

    /**
     * Add a key/value pair to the global view data.
     *
     * Bound data will be available to all views as variables.
     *
     * @param     string|array
     * @param     mixed|null
     * @return View
     */
    public function with_global($keys, $value = null)
    {
        static::set($keys, $value);

        return $this;
    }

    /**
     * Evaluate and render the contents of this instance
     *
     * @return string The evaluated and rendered contents
     */
    public function render()
    {
        ob_start();
        extract(static::$global_data, EXTR_SKIP);
        extract($this->data, EXTR_SKIP);

        try {
            include $this->path;
        } catch (\Exception $e) {
            // Silently fail?
        }

        return \ob_get_clean();
    }
}
