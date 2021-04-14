<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        if(backpack_user()->role >= 1) {// Only admin can manage product panel
            $this->crud->setModel('App\Models\Product');
            $this->crud->setRoute(config('backpack.base.route_prefix') . '/product');
            $this->crud->setEntityNameStrings('product', 'products');
        } else {
            echo 'You can\'t use this feature!!!'; exit;
        }
    }

    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'image',
                'label' => 'Image',
                'type' => 'image', 
                'width' => '100px', 
                'height' => '100px'
            ], 
            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text'
            ], 
            [
                'name' => 'price',
                'label' => 'Price',
                'type' => 'text'
            ], 
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text'
            ], 
        ]);
    }

    protected function setupShowOperation()
    {

        $this->crud->addColumns([
            [
                'name' => 'image',
                'label' => 'Image',
                'type' => 'image', 
                'width' => '200px', 
                'height' => '200px'
            ], 
            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text'
            ], 
            [
                'name' => 'price',
                'label' => 'Price',
                'type' => 'text'
            ], 
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text'
            ], 
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ProductRequest::class);

        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
