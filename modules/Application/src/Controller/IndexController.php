<?php
namespace Application\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        return $this->render('application/index/index');
    }

    public function portfolio()
    {
        return $this->render('application/index/portfolio');
    }

    public function contact()
    {
        return $this->render('application/index/contact');
    }
}
