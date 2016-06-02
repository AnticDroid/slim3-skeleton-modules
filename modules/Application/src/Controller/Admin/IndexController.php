<?php
namespace Application\Controller\Admin;

use MartynBiz\Slim3Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->render('admin/index/index');
    }
}
