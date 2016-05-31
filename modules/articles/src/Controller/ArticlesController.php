<?php
namespace Wordup\Controller;

class ArticlesController extends BaseController
{
    public function index()
    {
        $this->render('articles/index.html');
    }

    public function show($id)
    {
        $this->render('articles/show.html');
    }

    public function create()
    {
        $this->render('articles/create.html');
    }

    public function edit($id)
    {
        $this->render('articles/edit.html');
    }
}
