<?php

namespace Aztech\Layers\Pagination;

class PaginatedResult implements \Countable
{

    private $query;

    private $results;

    private $total;

    public function __construct(PaginatedQuery $query, $results, $totalCount)
    {
        $this->query = $query;

        if ($results instanceof \Iterator) {
            $results = iterator_to_array($results);
        }

        $this->results = $results;
        $this->total = $totalCount;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getTotalCount()
    {
        return $this->total;
    }

    public function count()
    {
        return count($this->results);
    }

    public function merge(PaginatedResult $result)
    {
        $results = array_merge($this->results, $result->results);
        $totalCount = $this->getTotalCount() + $result->getTotalCount();

        $query = new PaginatedQuery();

        $query->setItemsPerPage($this->getQuery()->getItemsPerPage() + $result->getQuery()->getItemsPerPage());
        $query->setPageNumber($this->getQuery()->getPageNumber());

        return new PaginatedResult($query, $results, $totalCount);
    }

    public function sort(callable $callback)
    {
        $results = $this->getResults();
        uasort($results, $callback);

        return new PaginatedResult($this->getQuery(), $results, $this->getTotalCount());
    }

    public function slice($start, $length)
    {
        $results = array_slice($this->results, $start, $length);

        $query = new PaginatedQuery();

        $query->setItemsPerPage($length);
        $query->setPageNumber($this->getQuery()->getPageNumber());

        return new PaginatedResult($query, $results, $this->getTotalCount());
    }
}
