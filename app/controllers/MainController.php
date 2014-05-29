<?php

class MainController extends Controller {
    /**
     * Index view
     *
     * @return View
     */
    public function index ()
    {
        return View::make('index');
    }
}
