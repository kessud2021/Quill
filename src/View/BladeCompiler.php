<?php

namespace Framework\View;

class BladeCompiler {
    protected $content;

    public function compile($content) {
        $this->content = $content;

        $this->compileIncludes();
        $this->compileLayouts();
        $this->compileComponents();
        $this->compileEchos();
        $this->compileStatements();

        return $this->content;
    }

    protected function compileIncludes() {
        $this->content = preg_replace_callback(
            '/@include\([\'"]([^\'"]+)[\'"]\)/',
            function ($matches) {
                $view = str_replace('.', '/', $matches[1]);
                $path = RESOURCES_PATH . '/views/' . $view . '.blade.php';
                if (file_exists($path)) {
                    return '<?php include "' . $path . '"; ?>';
                }
                return '';
            },
            $this->content
        );
    }

    protected function compileLayouts() {
        $this->content = preg_replace_callback(
            '/@extends\([\'"]([^\'"]+)[\'"]\)/',
            function ($matches) {
                return '<?php $__layout = "' . $matches[1] . '"; ?>';
            },
            $this->content
        );
    }

    protected function compileComponents() {
        $this->content = preg_replace_callback(
            '/@component\([\'"]([^\'"]+)[\'"]\s*,\s*\$([a-zA-Z_]\w*)\)/',
            function ($matches) {
                return '<?php extract($' . $matches[2] . '); include "' . RESOURCES_PATH . '/views/components/' . str_replace('.', '/', $matches[1]) . '.blade.php"; ?>';
            },
            $this->content
        );
    }

    protected function compileEchos() {
        $this->content = preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/',
            function ($matches) {
                $expression = $matches[1];
                return '<?php echo htmlspecialchars(' . $expression . ', ENT_QUOTES, "UTF-8"); ?>';
            },
            $this->content
        );

        $this->content = preg_replace_callback(
            '/\{!!\s*(.+?)\s*!!\}/',
            function ($matches) {
                return '<?php echo ' . $matches[1] . '; ?>';
            },
            $this->content
        );
    }

    protected function compileStatements() {
        $this->compileLoops();
        $this->compileConditions();
        $this->compileAuthentication();
    }

    protected function compileLoops() {
        $this->content = preg_replace_callback(
            '/@foreach\s*\(\s*(.+?)\s+as\s+(.+?)\s*\)/',
            function ($matches) {
                return '<?php foreach (' . $matches[1] . ' as ' . $matches[2] . '): ?>';
            },
            $this->content
        );

        $this->content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $this->content);

        $this->content = preg_replace_callback(
            '/@forelse\s*\(\s*(.+?)\s+as\s+(.+?)\s*\)/',
            function ($matches) {
                return '<?php if (' . $matches[1] . '): foreach (' . $matches[1] . ' as ' . $matches[2] . '): ?>';
            },
            $this->content
        );

        $this->content = preg_replace('/@empty/', '<?php endforeach; else: ?>', $this->content);
        $this->content = preg_replace('/@endforelse/', '<?php endif; ?>', $this->content);
    }

    protected function compileConditions() {
        $this->content = preg_replace_callback(
            '/@if\s*\(\s*(.+?)\s*\)/',
            function ($matches) {
                return '<?php if (' . $matches[1] . '): ?>';
            },
            $this->content
        );

        $this->content = preg_replace_callback(
            '/@elseif\s*\(\s*(.+?)\s*\)/',
            function ($matches) {
                return '<?php elseif (' . $matches[1] . '): ?>';
            },
            $this->content
        );

        $this->content = preg_replace('/@else/', '<?php else: ?>', $this->content);
        $this->content = preg_replace('/@endif/', '<?php endif; ?>', $this->content);
    }

    protected function compileAuthentication() {
        $this->content = preg_replace('/@auth/', '<?php if (auth()->check()): ?>', $this->content);
        $this->content = preg_replace('/@endauth/', '<?php endif; ?>', $this->content);
        $this->content = preg_replace('/@guest/', '<?php if (!auth()->check()): ?>', $this->content);
        $this->content = preg_replace('/@endguest/', '<?php endif; ?>', $this->content);
    }
}
