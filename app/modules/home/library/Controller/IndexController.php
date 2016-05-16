<?php
namespace App\Controller;

use MartynBiz\Slim3Controller\Controller;

abstract class IndexController extends Controller
{
    public function index()
    {
        return $this->render('index.index');
    }
}
