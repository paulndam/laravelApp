<?php

use Illuminate\Support\Facades\Route;

use App\Models\BackpackUser;
use App\Guest;
use App\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return view('pages.home');
});

Route::get('/home', function () {
    return redirect('/');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

// when coming back to site with invite token
Route::get('/invite/{token}', function ($token) {

    $guest = Guest::where('token', $token)->first();
    $email = $guest->email;

    if(!(count(User::where('email', $email)->get()) > 0)) {// in the case of new user
        $UserModel = BackpackUser::create([
            'name' => "guest",
            'email' => $guest->email,
            "password" => $guest->password,
            "role" => 0
        ]);
    } else { // in the case of existing user
        $UserModel = BackpackUser::where('email', $email)->first();
    }
    backpack_auth()->login($UserModel);
    return redirect()->to('/admin/dashboard');
});
