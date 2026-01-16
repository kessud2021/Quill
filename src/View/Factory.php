<?php

namespace Framework\View;

class Factory {
    protected $config;
    protected $viewsPath;
    protected $shared = [];

    public function __construct($config) {
        $this->config = $config;
        $this->viewsPath = RESOURCES_PATH . '/views';
    }

    public function make($view, $data = []) {
        $path = $this->getPath($view);

        if (!file_exists($path)) {
            throw new \Exception("View not found: $view");
        }

        return new View($path, array_merge($this->shared, $data));
    }

    public function share($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->shared[$k] = $v;
            }
        } else {
            $this->shared[$key] = $value;
        }
        return $this;
    }

    public function exists($view) {
        return file_exists($this->getPath($view));
    }

    protected function getPath($view) {
        $view = str_replace('.', '/', $view);
        return $this->viewsPath . '/' . $view . '.blade.php';
    }
}
