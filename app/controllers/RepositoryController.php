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
     * Get repository
     *
     * @param str $name
     * @return View
     */
    public function show ($name)
    {
        $repositories = $this->factory->toArray();
        $repository   = $this->factory->fromName($name);

        return View::make('repository')->with(array(
            'repositories'  => $repositories,
            'name'          => $repository->getName(),
            'branch'        => $repository->getCurrentBranch()
        ));
    }

    /**
     * Get repository data
     *
     * @param str $name
     * @return Response
     */
    public function data ($name)
    {
        $repositories = $this->factory->toArray();
        $repository   = $this->factory->fromName($name);
        $repository->loadCommits();

        $statistics = $repository->getStatistics();

        return Response::json(array(
            'repositories'  => $repositories,
            'repository'    => array(
                'name'          => $repository->getName(),
                'branch'        => $repository->getCurrentBranch(),
                'data'          => $statistics
            )
        ));
    }
}
