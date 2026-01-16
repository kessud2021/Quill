<?php

namespace Framework\Foundation;

use Framework\Container\Container;
use Framework\Config\Repository;
use Framework\Env\Loader;
use Framework\Routing\Router;
use Framework\View\Factory;
use Framework\Database\Manager;
use Framework\Events\Dispatcher;
use Framework\Middleware\Stack;
use Framework\Logging\Logger;

class Application extends Container {
    protected $path;
    protected $booted = false;
    protected $serviceProviders = [];
    protected $deferredServices = [];

    public function __construct($basePath) {
        parent::__construct();
        $this->path = $basePath;
        $this->registerBaseBindings();
    }

    protected function registerBaseBindings() {
        $this->instance('app', $this);
        $this->instance('path', $this->path);
    }

    public function loadEnvironment() {
        $envFile = $this->path . '/.env';
        if (file_exists($envFile)) {
            $loader = new Loader($envFile);
            $loader->load();
        }
    }

    public function registerCoreServices() {
        $this->singleton('config', function ($app) {
            return new Repository();
        });

        $this->singleton('env', function ($app) {
            return new Loader();
        });

        $this->singleton('router', function ($app) {
            return new Router();
        });

        $this->singleton('view', function ($app) {
            return new Factory($app['config']);
        });

        $this->singleton('db', function ($app) {
            return new Manager($app['config']->get('database'));
        });

        $this->singleton('events', function ($app) {
            return new Dispatcher();
        });

        $this->singleton('middleware', function ($app) {
            return new Stack();
        });

        $this->singleton('logger', function ($app) {
            return new Logger($app['config']->get('logging'));
        });
    }

    public function loadConfiguration() {
        $configPath = CONFIG_PATH;
        
        if (is_dir($configPath)) {
            foreach (glob($configPath . '/*.php') as $file) {
                $key = basename($file, '.php');
                $config = include $file;
                $this['config']->set($key, $config);
            }
        }
    }

    public function registerServiceProviders() {
        $providers = config('app.providers', []);
        
        foreach ($providers as $provider) {
            $this->registerServiceProvider($provider);
        }
    }

    public function registerServiceProvider($provider) {
        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        $this->serviceProviders[] = $provider;
    }

    public function bootProviders() {
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
        $this->booted = true;
    }

    public function handle($method, $uri, $query = [], $post = [], $server = [], $cookies = []) {
        $route = $this['router']->match($method, $uri);

        if (!$route) {
            return new \Framework\Http\Response('Not Found', 404);
        }

        $controller = $route->getController();
        $method = $route->getMethod();
        $params = $route->getParameters();

        if (is_string($controller)) {
            list($class, $method) = explode('@', $controller);
            $controller = $this->make($class);
        }

        $request = new \Framework\Http\Request($method, $uri, $query, $post, $server, $cookies);
        
        try {
            $result = call_user_func_array([$controller, $method], array_values($params));
            
            if (is_string($result)) {
                return new \Framework\Http\Response($result);
            }
            
            if ($result instanceof \Framework\Http\Response) {
                return $result;
            }

            if (is_array($result)) {
                return new \Framework\Http\Response(json_encode($result), 200, ['Content-Type' => 'application/json']);
            }

            return new \Framework\Http\Response((string)$result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e) {
        $this['logger']->error('Exception: ' . $e->getMessage());
        
        if (env('APP_DEBUG', false)) {
            return new \Framework\Http\Response(
                "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>",
                500,
                ['Content-Type' => 'text/html']
            );
        }

        return new \Framework\Http\Response('Internal Server Error', 500);
    }

    public function isBooted() {
        return $this->booted;
    }
}
