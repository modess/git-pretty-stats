<?php
use GitPrettyStats\RepositoryFactory;

class RepositoryController extends Controller {
    /**
     * Repository factory
     *
     * @var RepositoryFactory
     */
    protected $factory;

    /**
     * Create a new RepositoryController
     *
     * @param RepositoryFactory $factory
     */
    public function __construct (RepositoryFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * View for list
     *
     * @return View
     */
    public function all ()
    {
        $repositories = $this->factory->toArray();

        return Response::json($repositories);
    }

    /**
     * Get repository data
     *
     * @param str $name
     * @return Response
     */
    public function show ($name)
    {
        $this->factory->toArray(true);

        $repository   = $this->factory->fromName($name);

        return Response::json(array(
            'name'   => $repository->getName(),
            'branch' => $repository->getCurrentBranch(),
            'data'   => $repository->statistics()
        ));
    }
}
