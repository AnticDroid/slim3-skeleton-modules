<?php
namespace Application\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        $container = $this->app->getContainer();

        $cacheId = 'homepage_articles';
        if (! $articles = $this->get('cache')->get($cacheId) or 1) {
            $articles = $container->get('blog.model.article')->find([
                //..
            ], [ 'limit' => 5 ]);

            $this->get('cache')->set($cacheId, $articles, 1); // TODO change time
        }

        $cacheId = 'homepage_carousel_photos';
        if (! $carouselPhotos = $this->get('cache')->get($cacheId) or 1) {
            $carouselPhotos = $container->get('blog.model.photo')->find([
                //..
            ], [ 'limit' => 5 ]);

            $this->get('cache')->set($cacheId, $carouselPhotos, 1); // TODO change time
        }


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
