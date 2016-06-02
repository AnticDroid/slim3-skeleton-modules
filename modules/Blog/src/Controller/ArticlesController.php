<?php
namespace Wordup\Controller;

class ArticlesController extends BaseController
{
    public function index()
    {
        $this->render('blog::articles/index.html');
    }

    public function show($id)
    {
        $this->render('blog::articles/show.html');
    }

    public function create()
    {
        $this->render('blog::articles/create.html');
    }

    public function edit($id)
    {
        $this->render('blog::articles/edit.html');
    }
}
