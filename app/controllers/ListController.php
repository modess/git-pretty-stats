<?php
use GitPrettyStats\RepositoryFactory;

class ListController extends Controller {
    protected $factory;

    public function __construct (RepositoryFactory $factory)
    {
        $this->factory = $factory;
    }

    public function index ()
    {
        return View::make('list');
    }

}
