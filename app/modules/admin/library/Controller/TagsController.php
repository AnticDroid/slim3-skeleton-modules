<?php
namespace Wordup\Controller\Admin;

use Wordup\Controller\BaseController;
use Wordup\Model\Tag;
use Wordup\Exception\PermissionDenied;

class TagsController extends BaseController
{
    public function init()
    {
        // only admin can do anything here
        $currentUser = $this->get('auth')->getCurrentUser();
        if (! $currentUser->isAdmin() ) {
            throw new PermissionDenied('Permission denied to manage tags.');
        }
    }

    public function index()
    {
        $tags = $this->get('model.tag')->find();

        return $this->render('admin.tags.index', array(
            'tags' => $tags,
        ));
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
        $tag = $this->get('model.tag')->factory();

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
        $tag = $this->get('model.tag')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        // include any params that may have been sent
        $tag->set( $this->getPost() );

        return $this->render('admin.tags.edit', array(
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
        $tag = $this->get('model.tag')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        $params = $this->getPost();

        // for security reasons, some properties are not on the whitelist but
        // we can directly assign them by this way
        if (isset($params['role'])) $tag->role = $params['role'];

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
        $tag = $this->get('model.tag')->findOneOrFail(array(
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
