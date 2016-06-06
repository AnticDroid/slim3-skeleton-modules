<?php

namespace Blog\Model;

use MartynBiz\Mongo;
use Blog\Utils;
use Blog\Model\Tag;
use Blog\Model\Photo;
use Auth\Model\User;

use Application\Model\Base;

/**
 *
 */
class Article extends Base
{
    // types
    const TYPE_ARTICLE = 'article';
    const TYPE_PLACE = 'place';

    // statuses
    const STATUS_DRAFT = 0;
    const STATUS_SUBMITTED = 1;
    const STATUS_APPROVED = 2;

    // collection this model refers to
    protected static $collection = 'articles';

    // define on the fields that can be saved
    protected static $whitelist = array(
        'title',
        'slug',
        'content',
        'tags',
        'photos',
        'published_at',
    );

    /**
     * Find articles which belong to a given $user
     * @param App\Model\User $user
     * @param array $query Optional query to find articles
     */
    public function findArticlesManagedBy(User $user, $query=array())
    {
        // members can only view their own articles
        if ($user->isContributor()) {
            $query = array_merge(array(
                'author' => $user,
            ), $query);
        }

        // TODO editors can only view their members articles

        return $this->find($query);
    }

    /**
     * Convert published_at date to human readable TODO test
     */
    public function getPublishedAt($value)
    {
        return date('d/m/Y h:i', $value->sec);
    }

    /**
     * Additional Save procedures
     */
    public function save($data=array())
    {
        if (empty($this->data['slug'])) {
            $this->data['slug'] = Utils::slugify($this->data['title']);
        }

        return parent::save($data);
    }

    /**
     * Additional Save procedures
     */
    public function has(Mongo $item)
    {
        // if $item doesn't have a DBRef, then we can't proceed
        if(!$item->getDBRef()) return false;

        // check for Tags
        if ($item instanceof Tag) {
            if ($tags = @$this->data['tags']) {
                foreach($this->data['tags'] as $tag) {
                    if($item->getDBRef() == $tag) return true;
                }
            }
        }

        // check for Photo
        if ($item instanceof Photo) {
            if ($photos = @$this->data['photos']) {
                foreach($this->data['photos'] as $photo) {
                    if($item->getDBRef() == $photo) return true;
                }
            }
        }

        return false;
    }
}
