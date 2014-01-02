<?php
use GitPrettyStats\RepositoryFactory;

class ListController extends Controller {
    /**
     * Repository factory
     *
     * @var RepositoryFactory
     */
    protected $factory;

    /**
     * Create a new ListController
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
        return View::make('list');
    }

    /**
     * Fetch repository list
     *
     * @return Response
     */
    public function fetch ()
    {
        $list = $this->factory->toArray();

        return Response::json($list);
    }
}
