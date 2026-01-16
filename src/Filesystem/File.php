<?php

namespace Framework\Filesystem;

class File {
    protected $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public static function exists($path) {
        return file_exists($path);
    }

    public static function get($path) {
        if (!file_exists($path)) {
            throw new \Exception("File not found: $path");
        }

        return file_get_contents($path);
    }

    public static function put($path, $contents) {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return file_put_contents($path, $contents);
    }

    public static function append($path, $contents) {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    public static function delete($path) {
        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }

    public static function move($from, $to) {
        $directory = dirname($to);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return rename($from, $to);
    }

    public static function copy($from, $to) {
        $directory = dirname($to);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return copy($from, $to);
    }

    public static function makeDirectory($path) {
        return mkdir($path, 0755, true);
    }

    public static function isDirectory($path) {
        return is_dir($path);
    }

    public static function isFile($path) {
        return is_file($path);
    }

    public static function extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public static function name($path) {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public static function basename($path) {
        return basename($path);
    }

    public static function dirname($path) {
        return dirname($path);
    }

    public static function size($path) {
        return filesize($path);
    }

    public static function lastModified($path) {
        return filemtime($path);
    }

    public static function glob($pattern) {
        return glob($pattern);
    }

    public static function listDirectory($path) {
        return array_diff(scandir($path), ['.', '..']);
    }
}
