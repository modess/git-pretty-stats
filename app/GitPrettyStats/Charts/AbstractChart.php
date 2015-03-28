<?php namespace GitPrettyStats\Charts;

use GitPrettyStats\Repository;

class AbstractChart
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

}
