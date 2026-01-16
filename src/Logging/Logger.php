<?php

namespace Framework\Logging;

class Logger {
    protected $config;
    protected $channel;

    public function __construct($config = []) {
        $this->config = $config;
        $this->channel = $config['default'] ?? 'stack';
    }

    public function debug($message, $context = []) {
        $this->log('debug', $message, $context);
    }

    public function info($message, $context = []) {
        $this->log('info', $message, $context);
    }

    public function warning($message, $context = []) {
        $this->log('warning', $message, $context);
    }

    public function error($message, $context = []) {
        $this->log('error', $message, $context);
    }

    public function critical($message, $context = []) {
        $this->log('critical', $message, $context);
    }

    public function log($level, $message, $context = []) {
        $log = $this->formatLog($level, $message, $context);

        $this->writeLog($log);
        $this->writeStructuredLog($level, $message, $context);
    }

    protected function formatLog($level, $message, $context) {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' ' . json_encode($context) : '';

        return "[$timestamp] $level: $message$contextString" . PHP_EOL;
    }

    protected function writeLog($log) {
        $path = STORAGE_PATH . '/logs';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = $path . '/' . date('Y-m-d') . '.log';

        file_put_contents($file, $log, FILE_APPEND);
    }

    protected function writeStructuredLog($level, $message, $context) {
        $path = STORAGE_PATH . '/logs';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = $path . '/' . date('Y-m-d') . '.json';

        $entry = json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ]) . PHP_EOL;

        file_put_contents($file, $entry, FILE_APPEND);
    }
}
