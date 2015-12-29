<?php

namespace NavJobs\LaravelApi\Test\Integration;

class TestBuilder
{
    protected $limit;
    protected $offset;
    protected $sort;
    protected $order;

    public function take($count)
    {
        $this->limit = $count;

        return $this;
    }

    public function skip($count)
    {
        $this->offset = $count;

        return $this;
    }

    public function orderBy($sort, $order)
    {
        $this->sort = $sort;
        $this->order = $order;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }
}
