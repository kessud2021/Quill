<?php

namespace Framework\Exceptions;

class Handler {
    protected $dontReport = [];
    protected $render = [];

    public function report(\Exception $exception) {
        if ($this->shouldntReport($exception)) {
            return;
        }

        app('logger')->error('Exception: ' . $exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    public function render($request, \Exception $exception) {
        if (env('APP_DEBUG', false)) {
            return $this->renderDebug($exception);
        }

        return $this->renderProduction($exception);
    }

    protected function renderDebug(\Exception $exception) {
        return new \Framework\Http\Response(
            $this->formatDebugError($exception),
            500,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    protected function renderProduction(\Exception $exception) {
        return new \Framework\Http\Response(
            'Internal Server Error',
            500,
            ['Content-Type' => 'text/plain']
        );
    }

    protected function formatDebugError(\Exception $exception) {
        $html = '<html><head><title>Error</title></head><body style="font-family: monospace; margin: 20px;">';
        $html .= '<h1 style="color: #d32f2f;">' . get_class($exception) . '</h1>';
        $html .= '<p style="color: #666; font-size: 14px;">' . $exception->getMessage() . '</p>';
        $html .= '<p style="color: #999; font-size: 12px;">' . $exception->getFile() . ':' . $exception->getLine() . '</p>';
        $html .= '<pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow: auto;">';
        $html .= htmlspecialchars($exception->getTraceAsString());
        $html .= '</pre>';
        $html .= '</body></html>';

        return $html;
    }

    protected function shouldntReport(\Exception $exception) {
        foreach ($this->dontReport as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
