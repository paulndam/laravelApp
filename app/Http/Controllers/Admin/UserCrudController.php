<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateUserRequest as StoreRequest;
use App\Http\Requests\UpdateUserRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }


    public function setup()
    {
        $this->crud->setModel('App\Models\User');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user');
        $this->crud->setEntityNameStrings('user', 'users');
    }

    protected function setupListOperation()
    {
        if(!(backpack_user()->role >= 1)) {
            // In the case of user, remove create, preview, delete button
            $this->crud->denyAccess(['create', 'delete', 'show']);
            $this->crud->addClause('where', 'id', '=', backpack_user()->id);
        }

        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text'
            ], 
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'text'
            ], 
            [
                'name' => 'role',
                'label' => 'Role',
                'type' => 'closure',
                'function' => function($entry) {
                    $role = $entry->role == 1 ? 'admin' : 'user';
                    return $role;
                }
            ], 
        ]);
    }

    // Setup preview page with user id on user panel
    protected function show($id) {
        $this->crud->set('show.setFromDb', false);
        if(backpack_user()->role >= 1 || $id == backpack_user()->id) { // Only admin or owner
            if(!(backpack_user()->role >= 1)) {
                // In the case of user remove delete button
                $this->crud->denyAccess('delete');
            }

            $this->crud->addColumns([
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'text'
                ], 
                [
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => 'text'
                ], 
                [
                    'name' => 'created_at',
                    'label' => 'Date',
                    'type' => 'text'
                ], 
                [
                    'name' => 'role',
                    'label' => 'Role',
                    'type' => 'closure',
                    'function' => function($entry) {
                        $role = $entry->role == 1 ? 'admin' : 'user';
                        return $role;
                    }
                ], 
                [
                    'name' => 'email_verified_at',
                    'label' => 'Verified',
                    'type' => 'closure',
                    'function' => function($entry) {
                        $verify = $entry->email_verified_at ? 'verified' : 'not verified';
                        return $verify;
                    }
                ]
            ]);
        } else {
            $this->crud->denyAccess(['update', 'delete']);
        }


        $content = $this->traitShow($id);
        // cutom logic after
        return $content;
    }
    
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(StoreRequest::class);
        if(backpack_user()->role >= 1) {// Only in the case of admin

            $this->crud->addField(['type' => 'text', 'name' => 'name']);
            $this->crud->addField(['type' => 'email', 'name' => 'email']);
            $this->crud->addField(['type' => 'password', 'name' => 'password']);

            // Add select_from_array field
            $this->crud->addField([   
                'name' => 'role',
                'label' => "Role",
                'type' => 'select_from_array',
                'options' => [0 => 'user', 1 => 'admin'],
                'allows_null' => false,
                'default' => 0,
            ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation(UpdateRequest::class);
        
        $this->crud->addField(['type' => 'text', 'name' => 'name']);
        $this->crud->addField(['type' => 'email', 'name' => 'email']);
        $this->crud->addField(['type' => 'password', 'name' => 'password']);

        if(backpack_user()->role >= 1) {// Only in the case of admin
            $this->crud->addField([
                'name' => 'role',
                'label' => "Role",
                'type' => 'select_from_array',
                'options' => [0 => 'user', 1 => 'admin'],
                'allows_null' => false,
                'default' => 0,
            ]);
        }
    }

    // Function when clicking save button on update page on user panel
    public function update($id)
    {
        if(backpack_user()->role >= 1 || $id == backpack_user()->id) {// Admin or owner
            $response = $this->traitUpdate();
            // do something after save
            return $response;
        } else {
            \Alert::add('warning', 'You can\'t use this operation.')->flash();
            return redirect()->to('/admin/user');
        }
    }
}
