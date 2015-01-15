<?php

namespace Aztech\Layers\Pagination;

use League\Fractal\Pagination\PaginatorInterface;

class PaginatedResultAdapter implements PaginatorInterface
{

    private $paginatedResult;

    public function __construct(PaginatedResult $results)
    {
        $this->paginatedResult = $results;
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getCurrentPage()
     */
    public function getCurrentPage()
    {
        return $this->paginatedResult->getQuery()->getPageNumber();
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getLastPage()
     */
    public function getLastPage()
    {
        $perPage = max($this->paginatedResult->getQuery()->getItemsPerPage(), 1);
        $total = $this->getTotal();
        $pageCount = $total / $perPage;

        if (round($pageCount, 1) - round($pageCount, 0) > 0) {
            return round($pageCount, 0) + 1;
        }

        return round($pageCount, 0);
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getTotal()
     */
    public function getTotal()
    {
        return $this->paginatedResult->getTotalCount();
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getCount()
     */
    public function getCount()
    {
        return $this->paginatedResult->count();
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getPerPage()
     */
    public function getPerPage()
    {
        return $this->paginatedResult->getQuery()->getItemsPerPage();
    }

    /*
     * (non-PHPdoc) @see \League\Fractal\Pagination\PaginatorInterface::getUrl()
     */
    public function getUrl($page)
    {
        return '';
    }
}