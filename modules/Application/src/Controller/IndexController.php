<?php
namespace Application\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        // $cacheId = 'homepage_articles';
        // if (! $articles = $this->get('cache')->get($cacheId)) {
            $articles = $this->get('blog.model.article')->find([
                //..
            ], [ 'limit' => 5 ]);

        //     $this->get('cache')->set($cacheId, $articles, 3600);
        // }

        // $cacheId = 'homepage_carousel_photos';
        // if (! $carouselPhotos = $this->get('cache')->get($cacheId)) {
            $carouselPhotos = $this->get('blog.model.photo')->find([
                //..
            ], [ 'limit' => 5 ]);

        //     $this->get('cache')->set($cacheId, $carouselPhotos, 3600);
        // }


        $this->render('application/index/index', compact('articles', 'carouselPhotos'));
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
