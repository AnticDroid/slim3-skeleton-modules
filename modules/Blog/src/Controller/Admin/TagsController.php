<?php
namespace Blog\Controller\Admin;

use Blog\Model\Tag;
use Blog\Exception\PermissionDenied as PermissionDeniedException;

use Blog\Controller\BaseController;

class TagsController extends BaseController
{
    public function index($request, $response, $args)
    {
        // get tags
        $options = $this->getQueryOptions();
        $tags = $this->get('blog.model.tag')->find([], $options);

        // set page info for pagination links
        $total = $this->get('blog.model.tag')->count([]);
        $path = $this->get('router')->pathFor('admin_tags');
        $pageInfo = $this->getPageInfo($total, $path, $options);

        return $this->render('blog/admin/tags/index', compact('tags', 'pageInfo'));
    }

    public function create($request, $response, $args)
    {
        return $this->render('blog/admin/tags/create', array(
            'params' => $this->getPost(),
        ));
    }

    public function post($request, $response, $args)
    {
        $currentUser = $this->get('auth')->getCurrentUser();

        if ($tag = $this->get('blog.model.tag')->create( $this->getPost())) {
            $this->get('flash')->addMessage('success', 'Tag created.');
            return $this->redirect( $this->get('router')->pathFor('admin_tags') );
        } else {
            $this->get('flash')->addMessage('errors', $tag->getErrors());
            return $this->forward('create');
        }
    }

    /**
     * Upon creation too, the tag will be redirect here to edit the tag
     */
    public function edit($request, $response, $args)
    {
        list($id) = $args;

        $tag = $this->get('blog.model.tag')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        // include any params that may have been sent
        $tag->set( $this->getPost() );

        return $this->render('blog/admin/tags/edit', array(
            'tag' => $tag,
        ));
    }

    /**
     * This method will update the tag (save draft) and 1) if xhr, return json,
     * or 2) redirect back to the edit page (upon which they can then submit when they
     * choose to)
     */
    public function update($request, $response, $args)
    {
        list($id) = $args;

        $tag = $this->get('blog.model.tag')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        $params = $this->getPost();

        if ( $tag->save($params) ) {
            $this->get('flash')->addMessage('success', 'Tag saved.');
            return $this->redirect('/admin/tags');
        } else {
            $this->get('flash')->addMessage('errors', $tag->getErrors());
            return $this->forward('edit', array(
                'id' => $id,
            ));
        }
    }

    public function delete($request, $response, $args)
    {
        list($id) = $args;

        $tag = $this->get('blog.model.tag')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        if ( $tag->delete() ) {
            $this->get('flash')->addMessage('success', 'Tag deleted successfully');
            return $this->redirect('/admin/tags');
        } else {
            $this->get('flash')->addMessage('errors', $tag->getErrors());
            return $this->edit($id);
        }
    }
}
