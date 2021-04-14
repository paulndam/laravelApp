<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\User;
use App\Models\Order;


/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    public function setup()
    {
        $this->crud->setModel('App\Models\Order');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/order');
        $this->crud->setEntityNameStrings('order', 'orders');
    }

    // Setup list page on Guest panel
    protected function setupListOperation()
    {
        if(!(backpack_user()->role >= 1)) {
            $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);
            $this->crud->denyAccess(['update']);
        }

        $this->crud->addColumn(
            [
                'name' => 'id', 
                'label' => 'ID', 
                'type' => 'text'
            ] 
        );
        $this->crud->addColumn(
            [
                'name' => 'user_id',
                'label' => 'User',
                'type' => 'closure',
                'function' => function($entry) {
                    $user = User::where('id', $entry->user_id)->first();
                    return $user->name;
                }
            ] 
        );
        $this->crud->addColumn(
            [
                'name' => 'created_at',
                'label' => 'Date',
                'type' => 'date'
            ]
        );
    }

    // Setup preview page from id on Guest panel
    protected function show($id)
    {
        $this->crud->set('show.setFromDb', false);
        $user_id = Order::where('id', $id)->first()->user_id;
        if(backpack_user()->role >= 1 || $user_id == backpack_user()->id) {
            if(!(backpack_user()->role >= 1)) {
                $this->crud->denyAccess('update');
            }
            $this->crud->addColumn(
                [
                    'name' => 'id', 
                    'label' => 'ID', 
                    'type' => 'text'
                ] 
            );

            // Add closure field
            $this->crud->addColumn(
                [
                    'name' => 'user_id',
                    'label' => 'User',
                    'type' => 'closure',
                    'function' => function($entry) {
                        $user = User::where('id', $entry->user_id)->first();
                        return $user->name;
                    }
                ] 
            );
            //Add Date field
            $this->crud->addColumn(
                [
                    'name' => 'created_at',
                    'label' => 'Date',
                    'type' => 'date'
                ]
            );
        } else {
            // Remove Edit and Delete Button
            $this->crud->denyAccess(['update', 'delete']);
        }
        
        $content = $this->traitShow($id);
        // cutom logic after
        return $content;
    }

    // Setup create page on Guest panel
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(OrderRequest::class);

        if(backpack_user()->role >= 1) {// In the case of admin
            $this->crud->addField([
                'label' => "User",
                'type' => 'select2',
                'name' => 'user_id',
                'entity' => 'user', 
                'attribute' => 'name', 
                'model' => 'App\Models\User'
            ]);
        } else {// In the case of user
            // Add select2 filed
            $this->crud->addField([
                'label' => "User",
                'type' => 'select2',
                'name' => 'user_id',
                'entity' => 'user', 
                'attribute' => 'name', 
                'model' => 'App\Models\User', 
                'options'   => (function ($query) {
                    return $query->where('id', backpack_user()->id)->get();
                })
            ]);
        }
    }

    // Setup update page on Guest panel
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    // Function when clicking save button on update page
    public function update()
    {
        if(backpack_user()->role >= 1) {// In the case of admin
            $response = $this->traitUpdate();
            // do something after save
            return $response;
        } else {// In the case of user
            \Alert::add('warning', 'You can\'t use this operation.')->flash();
            return redirect()->to('/admin/user');
        }
    }
}
