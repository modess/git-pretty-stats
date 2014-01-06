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
    public function index ()
    {
        $repositories = $this->factory->toArray();

        return View::make('list')->withRepositories($repositories);
    }

    /**
     * Fetch repository list
     *
     * @return Response
     */
    public function all ()
    {
        $list = $this->factory->toArray();

        return Response::json($list);
    }
}
