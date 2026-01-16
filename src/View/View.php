<?php

namespace Framework\View;

/**
 * View renderer
 */
class View
{
    /**
     * View paths
     *
     * @var array
     */
    protected array $paths = [];

    /**
     * View data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * View name
     *
     * @var string|null
     */
    protected ?string $view = null;

    /**
     * Create a new view instance
     *
     * @param array $paths
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths ?: [resources_path('views')];
    }

    /**
     * Create a view
     *
     * @param string $name
     * @param array $data
     * @return self
     */
    public function create(string $name, array $data = []): self
    {
        $this->view = $name;
        $this->data = $data;
        return $this;
    }

    /**
     * Share data across all views
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function share(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Render the view
     *
     * @return string
     */
    public function render(): string
    {
        $path = $this->findView($this->view);

        if (!$path || !file_exists($path)) {
            throw new \RuntimeException("View [{$this->view}] not found");
        }

        // Compile blade if needed
        if (str_ends_with($path, '.blade.php')) {
            $compiled = $this->compileBlade($path);
        } else {
            $compiled = $path;
        }

        // Extract data
        extract($this->data);

        // Render
        ob_start();
        include $compiled;
        return ob_get_clean();
    }

    /**
     * Find view file
     *
     * @param string $name
     * @return string|null
     */
    protected function findView(string $name): ?string
    {
        $name = str_replace('.', '/', $name);

        foreach ($this->paths as $path) {
            $file = $path . '/' . $name . '.blade.php';
            if (file_exists($file)) {
                return $file;
            }

            $file = $path . '/' . $name . '.php';
            if (file_exists($file)) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Compile blade template
     *
     * @param string $path
     * @return string
     */
    protected function compileBlade(string $path): string
    {
        $compiler = new BladeCompiler();
        $compiled = $compiler->compile(file_get_contents($path));

        $cachePath = storage_path('views');
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        $cacheFile = $cachePath . '/' . md5($path) . '.php';
        file_put_contents($cacheFile, $compiled);

        return $cacheFile;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
