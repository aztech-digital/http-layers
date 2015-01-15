<?php

namespace Aztech\Layers\Pagination;

class PaginatedQuery
{

    private $itemsPerPage;

    private $pageNumber;

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getItemsOffset()
    {
        return max($this->itemsPerPage * ($this->pageNumber - 1), 0);
    }

    /**
     * Set the number of results per page.
     * @param int $count
     */
    public function setItemsPerPage($count)
    {
        $this->itemsPerPage = max((int) $count, 0);
    }

    /**
     * Returns the page number, starting at 1.
     *
     * @return number
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * Set the page number, starting at 1.
     *
     * @param unknown $number
     */
    public function setPageNumber($number)
    {
        $this->pageNumber = max((int) $number, 1);
    }
}