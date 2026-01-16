<?php

namespace Framework\View;

class View {
    protected $path;
    protected $data = [];

    public function __construct($path, $data = []) {
        $this->path = $path;
        $this->data = $data;
    }

    public function render() {
        $compiler = new BladeCompiler();
        $compiled = $compiler->compile(file_get_contents($this->path));

        ob_start();
        try {
            extract($this->data);
            eval('?>' . $compiled);
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    public function __toString() {
        return $this->render();
    }
}
