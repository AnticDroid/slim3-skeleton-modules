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
        if ($this->request->isPost()) {

        }

        return $this->render('application/index/contact');
    }
}
