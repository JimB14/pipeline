<?php

namespace App\Controllers;


use \App\Models\User;
use \Core\View;


class Home extends \Core\Controller
{
    public function indexAction()
    {

        View::renderTemplate('Home/index.html', [

        ]);
    }
}
