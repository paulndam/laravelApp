<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderItemRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Product;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Class OrderItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    public function setup()
    {
        $this->crud->setModel('App\Models\OrderItem');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/orderitem');
        $this->crud->setEntityNameStrings('orderitem', 'order_items');
    }

    protected function setupListOperation()
    {
        $orders = Order::where('user_id', backpack_user()->id)->get();
        $order_ids = array();
        foreach ($orders as $order) {
            array_push($order_ids, $order->id);
        }

        // Only display order that order_id is in $order_ids in the case of user
        if(!(backpack_user()->role >= 1)) {
            $this->crud->addClause('whereIn', 'order_id', $order_ids);
        }

        $this->crud->addColumn([
            'name' => 'order_id',
            'label' => 'Order ID',
            'type' => 'text',
        ]);

        // Add image field to list page
        $this->crud->addColumn(
            [
                'name' => 'product_image',
                'label' => 'Image',
                'type' => 'closure',
                'function' => function($entry) {
                    $product = Product::where('id', $entry->product_id)->first();
                    return '<img src="' . $product->image . '" width="100" alt="product" />';
                }
            ] 
        );

        // Add closure field to list page
        $this->crud->addColumn(
            [
                'name' => 'product_id',
                'label' => 'Name',
                'type' => 'closure',
                'function' => function($entry) {
                    $product = Product::where('id', $entry->product_id)->first();
                    return $product->title;
                }
            ] 
        );

        $this->crud->addColumn(
            [
                'name' => 'count',
                'label' => 'Count',
                'type' => 'text',
            ] 
        );
    }

    // Setup preview page on orderitem panel using orderitem id
    protected function show($id)
    {
        // not display all field in database
        $this->crud->set('show.setFromDb', false);

        $orders = Order::where('user_id', backpack_user()->id)->get();
        $order_ids = array();
        foreach ($orders as $order) {
            array_push($order_ids, $order->id);
        }

        $order_id = OrderItem::where('id', $id)->first()->order_id;

        if(backpack_user()->role >= 1 || array_search($order_id, $order_ids) !== false) {// Only show admin or owner
            $this->crud->addColumn([
                'name' => 'order_id',
                'label' => 'Order ID',
                'type' => 'text',
            ]);

            $this->crud->addColumn(
                [
                    'name' => 'product_image',
                    'label' => 'Image',
                    'type' => 'closure',
                    'function' => function($entry) {
                        $product = Product::where('id', $entry->product_id)->first();
                        return '<img src="' . $product->image . '" width="200" alt="product" />';
                    }
                ] 
            );
            $this->crud->addColumn(
                [
                    'name' => 'product_id',
                    'label' => 'Name',
                    'type' => 'closure',
                    'function' => function($entry) {
                        $product = Product::where('id', $entry->product_id)->first();
                        return $product->title;
                    }
                ] 
            );

            $this->crud->addColumn(
                [
                    'name' => 'count',
                    'label' => 'Count',
                    'type' => 'text',
                ] 
            );

            // Remove product column
            $this->crud->removeColumn('product');
        } else {
            $this->crud->denyAccess(['update', 'delete']);
        }


        $content = $this->traitShow($id);
        // cutom logic after
        return $content;
    }

    protected function setupCreateOperation()
    {
        // Set validation from OrderItem Request
        $this->crud->setValidation(OrderItemRequest::class);

        // Add select2 field with order ids
        $this->crud->addField([
            'label' => "Order ID",
            'type' => 'select2',
            'name' => 'order_id',
            'entity' => 'order', 
            'attribute' => 'id', 
            'model' => 'App\Models\Order', 
            'options'   => (function ($query) {
                return $query->where('user_id', backpack_user()->id)->get();
            })
         ]);

         // Add select2 field with product title
         $this->crud->addField([
            'label' => "Product",
            'type' => 'select2',
            'name' => 'product_id',
            'entity' => 'product', 
            'attribute' => 'title', 
            'model' => 'App\Models\Product'
         ]);
         $this->crud->addField(['type' => 'number', 'name' => 'count']);
    }

    protected function setupUpdateOperation()
    {
         $this->crud->addField([
            'label' => "Product",
            'type' => 'select2',
            'name' => 'product_id',
            'entity' => 'product', 
            'attribute' => 'title', 
            'model' => 'App\Models\Product'
         ]);
         $this->crud->addField(['type' => 'number', 'name' => 'count']);
    }

    // Setup Edit page with each orderitem id on orderitem panel
    public function update($id)
    {
        $orders = Order::where('user_id', backpack_user()->id)->get();
        $order_ids = array();
        foreach ($orders as $order) {
            array_push($order_ids, $order->id);
        }

        $order_id = OrderItem::where('id', $id)->first()->order_id;

        if(backpack_user()->role >= 1 || array_search($order_id, $order_ids) !== false) {// Only in the case of admin or owner
            $response = $this->traitUpdate();
            // do something after save
            return $response;
        } else {// In the case of not, not run update function
            \Alert::add('warning', 'You can\'t use this operation.')->flash();
            return redirect()->to('/admin/orderitem');
        }
    }
}
