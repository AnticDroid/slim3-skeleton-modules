<?php
namespace Blog\Controller\Admin;

use Blog\Model\Tag;
use Blog\Exception\PermissionDenied as PermissionDeniedException;

use Application\Controller\BaseController;

class TagsController extends BaseController
{
    public function index()
    {
        // fetch articles this user manages
        $tags = $this->get('blog.model.tag')->find();

        return $this->render('blog/admin/tags/index', compact('tags'));
    }

    public function create()
    {
        return $this->render('admin.tags.create', array(
            'params' => $this->getPost(),
        ));
    }

    public function post()
    {
        $currentUser = $this->get('auth')->getCurrentUser();
        $tag = $this->get('blog.model.tag')->factory();

        if ( $tag->save( $this->getPost() ) ) {
            $this->get('flash')->addMessage('success', 'Tag created.');
            return $this->redirect('/admin/tags');
        } else {
            $this->get('flash')->addMessage('errors', $tag->getErrors());
            return $this->forward('create');
        }
    }

    /**
     * Upon creation too, the tag will be redirect here to edit the tag
     */
    public function edit($id)
    {
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
    public function update($id)
    {
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

    public function delete($id)
    {
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
