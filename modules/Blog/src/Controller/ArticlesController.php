<?php
namespace Blog\Controller;

class ArticlesController extends BaseController
{
    public function index($request, $response, $args)
    {
        // set query
        $query = [];
        if ($search = $this->getQueryParam('search')) {
            $query = array_merge_recursive($query, [
                '$text' => [
                    '$search' => $search,
                ]
            ]);
        }

        // set params
        $limit = (int) $this->getQueryParam('limit', 10);
        $page = (int) $this->getQueryParam('page', 1);
        $skip = $limit * ($page - 1);
        $options = array_intersect_key(array_merge([
            'limit' => $limit,
            'skip' => $skip,
        ], $this->getQueryParams()), array_flip(['limit', 'skip']));

        $articles = $this->get('blog.model.article')->find($query, $options);

        $this->render('blog/articles/index', compact('articles'));
    }

    public function show($request, $response, $args)
    {
        list($id) = $args;

        $article = $this->get('blog.model.article')->findOneOrFail([
            'id' => (int) $id,
        ]);

        $otherArticles = $this->get('blog.model.article')->find([
            'id' => [ '$ne' => $article->id ],
        ], [ 'limit' => 5 ]);

        $this->render('blog/articles/show', compact('article', 'otherArticles'));
    }
}
