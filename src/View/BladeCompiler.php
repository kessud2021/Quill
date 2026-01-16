<?php

namespace Framework\View;

/**
 * Blade template compiler
 * 
 * Compiles Blade syntax to PHP
 */
class BladeCompiler
{
    /**
     * Compile blade template
     *
     * @param string $content
     * @return string
     */
    public function compile(string $content): string
    {
        // Compile directives
        $content = $this->compileDirectives($content);
        $content = $this->compileEchos($content);
        $content = $this->compileLoops($content);
        $content = $this->compileConditionals($content);
        $content = $this->compileIncludes($content);

        return $content;
    }

    /**
     * Compile blade directives
     *
     * @param string $content
     * @return string
     */
    protected function compileDirectives(string $content): string
    {
        // @csrf
        $content = str_replace('@csrf', '<?php echo csrf_field(); ?>', $content);

        // @auth
        $content = preg_replace(
            '/@auth\s*/',
            '<?php if (auth()->check()): ?>',
            $content
        );

        // @endauth
        $content = str_replace('@endauth', '<?php endif; ?>', $content);

        // @guest
        $content = preg_replace(
            '/@guest\s*/',
            '<?php if (!auth()->check()): ?>',
            $content
        );

        // @endguest
        $content = str_replace('@endguest', '<?php endif; ?>', $content);

        return $content;
    }

    /**
     * Compile echo statements
     *
     * @param string $content
     * @return string
     */
    protected function compileEchos(string $content): string
    {
        // {{ $variable }}
        $content = preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/',
            function ($matches) {
                return '<?php echo htmlspecialchars(' . $matches[1] . ', ENT_QUOTES, "UTF-8"); ?>';
            },
            $content
        );

        // {!! $html !!}
        $content = preg_replace_callback(
            '/\{!!\s*(.+?)\s*!!\}/',
            function ($matches) {
                return '<?php echo ' . $matches[1] . '; ?>';
            },
            $content
        );

        return $content;
    }

    /**
     * Compile loops
     *
     * @param string $content
     * @return string
     */
    protected function compileLoops(string $content): string
    {
        // @foreach
        $content = preg_replace(
            '/@foreach\s*\(\s*\$(.+?)\s+as\s+\$(.+?)\s*\)/',
            '<?php foreach ($\1 as $\2): ?>',
            $content
        );

        // @endforeach
        $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);

        // @for
        $content = preg_replace(
            '/@for\s*\(\s*(.+?)\s*\)/',
            '<?php for (\1): ?>',
            $content
        );

        // @endfor
        $content = str_replace('@endfor', '<?php endfor; ?>', $content);

        // @while
        $content = preg_replace(
            '/@while\s*\(\s*(.+?)\s*\)/',
            '<?php while (\1): ?>',
            $content
        );

        // @endwhile
        $content = str_replace('@endwhile', '<?php endwhile; ?>', $content);

        return $content;
    }

    /**
     * Compile conditionals
     *
     * @param string $content
     * @return string
     */
    protected function compileConditionals(string $content): string
    {
        // @if
        $content = preg_replace(
            '/@if\s*\(\s*(.+?)\s*\)/',
            '<?php if (\1): ?>',
            $content
        );

        // @elseif
        $content = preg_replace(
            '/@elseif\s*\(\s*(.+?)\s*\)/',
            '<?php elseif (\1): ?>',
            $content
        );

        // @else
        $content = str_replace('@else', '<?php else: ?>', $content);

        // @endif
        $content = str_replace('@endif', '<?php endif; ?>', $content);

        // @unless
        $content = preg_replace(
            '/@unless\s*\(\s*(.+?)\s*\)/',
            '<?php if (!\1): ?>',
            $content
        );

        // @endunless
        $content = str_replace('@endunless', '<?php endif; ?>', $content);

        return $content;
    }

    /**
     * Compile includes
     *
     * @param string $content
     * @return string
     */
    protected function compileIncludes(string $content): string
    {
        // @include('view.name')
        $content = preg_replace(
            '/@include\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php echo view("\1", get_defined_vars())->render(); ?>',
            $content
        );

        // @include('view.name', ['data' => $value])
        $content = preg_replace(
            '/@include\(\s*[\'"](.+?)[\'"],\s*(\[.+?\])\s*\)/',
            '<?php echo view("\1", array_merge(get_defined_vars(), \2))->render(); ?>',
            $content
        );

        return $content;
    }
}
