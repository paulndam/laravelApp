<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GuestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

    
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use App\Guest;
use App\Models\User;

use Illuminate\Support\Str;

/**
 * Class GuestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GuestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }


    public function setup()
    {
        if(backpack_user()->role >= 1) {// In the case of admin
            $this->crud->setModel('App\Models\Guest');
            $this->crud->setRoute(config('backpack.base.route_prefix') . '/guest');
            $this->crud->setEntityNameStrings('guest', 'guests');

            // display resend button on Guest panel
            $this->crud->addButtonFromView('line', 'resend', 'resend', 'beginning');
            // remove show preview button on Guest panel
            $this->crud->denyAccess('show');
        } else {// In the case of user
            echo 'You can\'t use this feature!!!'; exit;
        }
    }
    
    // Setup list page on Guest panel using list operation
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'email', 
            'label' => 'Email', 
            'type' => 'email'
        ]);
        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Date',
            'type' => 'date'
        ]);
    }

    // Setup create page on Guest panel using create operation
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(GuestRequest::class);

        $this->crud->addField([
            'name' => 'email', 
            'label' => 'Email', 
            'type' => 'email'
        ]);
    }

    // Setup update page on Guest panel using update operation
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    // function when clicking create button on create page
    public function store()
    {
        $email = $this->crud->request->request->get('email');

        // Generate and set the value of token and password using Str::random
        $token = hash('sha256', Str::random(60));
        $pass = Str::random(8);
        $this->crud->request->request->add(['token'=> $token]);
        $this->crud->addField(['type' => 'hidden', 'name' => 'token']);
        $this->crud->request->request->add(['password'=> bcrypt($pass)]);
        $this->crud->addField(['type' => 'hidden', 'name' => 'password']);

        // Set mail template(mail.blade.php) parameter
        $to_name = '';
        $to_email = $email;
        $data = array(
            'name'=>"", 
            "content" => "Please Visit our site!", 
            'email' => $email, 
            "password" => $pass,
            "token" => $token
        );

        // Send mail
        Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject('Invitation');
            $message->from('info@immerchant.com', 'Product Admin');
        });

        $response = $this->traitStore();
        return $response;
    }

    // function when clicking resend button on list page
    public function resend($id) 
    {
        // Retrieve Guest using guest id
        $guest = Guest::where('id', $id)->first();
        $email = $guest->email;

        // Generate and set the value of token and password using Str::random
        $token = hash('sha256', Str::random(60));
        $pass = Str::random(8);
        $guest->token = $token;
        $guest->password = bcrypt($pass);
        $guest->save();

        // Set mail template(mail.blade.php) parameter
        $to_name = '';
        $to_email = $email;
        $data = array(
            'name'=>"", 
            "content" => "Please Visit our site!", 
            'email' => $email, 
            "password" => $pass,
            "token" => $token
        );

        // Send mail
        Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject('Invitation');
            $message->from('info@immerchant.com', 'Product Admin');
        });

        return back();
    }
}
