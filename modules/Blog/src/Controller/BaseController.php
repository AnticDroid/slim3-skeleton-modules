<?php
namespace Blog\Controller;

class BaseController extends \Application\Controller\BaseController
{
    /**
     * Get the options for the query (skip, limit)
     * @param array $options
     * @return array
     */
    protected function getQueryOptions($options=[])
    {
        $limit = (int) $this->getQueryParam('limit', 10);
        $page = (int) $this->getQueryParam('page', 1);
        $skip = $limit * ($page - 1);

        return array_intersect_key(array_merge([
            'limit' => $limit,
            'skip' => $skip,
        ], $options), array_flip(['limit', 'skip']));
    }

    /**
     * Get the page info for the pagination
     * @param int $count
     * @param array $options
     * @return array
     */
    protected function getPageInfo($count, $path='', $options=[])
    {
        return [
            'page' => (int) $this->getQueryParam('page', 1),
            'total_pages' => $count ? ceil($count / $options['limit']) : 1,
            'path' => $path,
        ];
    }
}
