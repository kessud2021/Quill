<?php

namespace Framework\Pagination;

class Paginator {
    protected $items;
    protected $perPage;
    protected $currentPage;
    protected $totalItems;

    public function __construct($items, $perPage = 15, $currentPage = 1) {
        $this->items = $items;
        $this->perPage = $perPage;
        $this->currentPage = max(1, intval($currentPage));
        $this->totalItems = count($items);
    }

    public function getItems() {
        $offset = ($this->currentPage - 1) * $this->perPage;
        return array_slice($this->items, $offset, $this->perPage);
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function getPerPage() {
        return $this->perPage;
    }

    public function getTotalItems() {
        return $this->totalItems;
    }

    public function getTotalPages() {
        return ceil($this->totalItems / $this->perPage);
    }

    public function hasPages() {
        return $this->getTotalPages() > 1;
    }

    public function hasMore() {
        return $this->currentPage < $this->getTotalPages();
    }

    public function hasPrevious() {
        return $this->currentPage > 1;
    }

    public function nextPage() {
        return $this->hasMore() ? $this->currentPage + 1 : null;
    }

    public function previousPage() {
        return $this->hasPrevious() ? $this->currentPage - 1 : null;
    }

    public function render($baseUrl = '') {
        if (!$this->hasPages()) {
            return '';
        }

        $html = '<nav class="pagination">';
        $html .= '<ul>';

        if ($this->hasPrevious()) {
            $prevPage = $this->previousPage();
            $html .= '<li><a href="' . $baseUrl . '?page=' . $prevPage . '">Previous</a></li>';
        }

        for ($i = 1; $i <= $this->getTotalPages(); $i++) {
            $active = $i === $this->currentPage ? ' active' : '';
            $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '"' . ($active ? ' class="' . $active . '"' : '') . '>' . $i . '</a></li>';
        }

        if ($this->hasMore()) {
            $nextPage = $this->nextPage();
            $html .= '<li><a href="' . $baseUrl . '?page=' . $nextPage . '">Next</a></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }
}
