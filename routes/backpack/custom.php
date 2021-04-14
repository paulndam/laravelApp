<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UserCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::crud('order', 'OrderCrudController');
    Route::crud('orderitem', 'OrderItemCrudController');
    Route::crud('guest', 'GuestCrudController');
    // when clicking resend button on Guest panel
    Route::get('guest/{id}/resend', 'GuestCrudController@resend');
}); // this should be the absolute last line of this file
