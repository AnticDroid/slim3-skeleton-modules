<?php
namespace Auth\Controller\Admin;

use Auth\Controller\BaseController;
use Auth\Model\User;
use Auth\Exception\PermissionDenied;

class UsersController extends BaseController
{
    public function init()
    {
        // only admin can do anything here
        $currentUser = $this->get('auth')->getCurrentUser();
        if (! $currentUser->isAdmin() ) {
            throw new PermissionDenied('Permission denied to manage users.');
        }
    }

    public function index()
    {
        $users = $this->get('model.user')->find();

        return $this->render('admin.users.index', array(
            'users' => $users,
        ));
    }

    /**
     * Upon creation too, the user will be redirect here to edit the user
     */
    public function edit($id)
    {
        $user = $this->get('model.user')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        $user->set( $this->getPost() );

        return $this->render('admin.users.edit', array(
            'user' => $user,
        ));
    }

    /**
     * This method will update the user (save draft) and 1) if xhr, return json,
     * or 2) redirect back to the edit page (upon which they can then submit when they
     * choose to)
     */
    public function update($id)
    {
        $user = $this->get('model.user')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        $params = $this->getPost();

        // for security reasons, some properties are not on the whitelist but
        // we can directly assign them by this way
        if (isset($params['role'])) $user->role = $params['role'];

        if ( $user->save($params) ) {
            $this->get('flash')->addMessage('success', 'User saved.');
            return $this->redirect('/admin/users');
        } else {
            $this->get('flash')->addMessage('errors', $user->getErrors());
            return $this->forward('edit', array(
                'id' => $id,
            ));
        }
    }

    public function delete($id)
    {
        $user = $this->get('model.user')->findOneOrFail(array(
            'id' => (int) $id,
        ));

        if ( $user->delete() ) {
            $this->get('flash')->addMessage('success', 'User deleted successfully');
            return $this->redirect('/admin/users');
        } else {
            $this->get('flash')->addMessage('errors', $user->getErrors());
            return $this->edit($id);
        }
    }
}
