<?php

namespace App\Controllers;

use Illuminate\View\Factory;

class HomeController
{
    protected Factory $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    public function index(): string
    {
        return $this->view->make('home', ['name' => 'Black'])->render();
    }
}
