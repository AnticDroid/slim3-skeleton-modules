<?php
namespace App\Modules\Home\Controller;

use MartynBiz\Slim3Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->render('index.index');
    }
}
