<?php
namespace Application\Controller\Admin;

use Application\Controller\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        return $this->render('application/admin/index/index');
    }
}
