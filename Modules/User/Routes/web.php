<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {

    //User Profile
    Route::get('/user/profile', 'ProfileController@edit')->name('profile.edit');
    Route::patch('/user/profile', 'ProfileController@update')->name('profile.update');
    Route::patch('/user/password', 'ProfileController@updatePassword')->name('profile.update.password');

    //Users
    Route::resource('users', 'UsersController')->except('show');
    Route::patch('users/{user}/freeze', 'UsersController@freeze')->name('users.freeze');
    Route::patch('users/{user}/unfreeze', 'UsersController@unfreeze')->name('users.unfreeze');

    //Roles
    Route::resource('roles', 'RolesController')->except('show');

    //Stores (permission-gated in controller)
    Route::resource('stores', 'StoresController')->except('show');

});

Route::group(['middleware' => ['auth', 'role:Super Admin|Admin|Business Owner'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('users', 'UsersController')->except('show');
    Route::resource('roles', 'RolesController')->except('show');
    Route::get('login-logs', 'LoginLogsController@index')->name('login-logs.index')->middleware('subscribed:feature,login_logs');
    
    // Manage Storage
    Route::get('storage', 'StorageController@index')->name('storage.index');
    Route::delete('storage/media/{media}', 'StorageController@deleteMedia')->name('storage.delete-media');
    Route::post('storage/clear-logs', 'StorageController@clearLogs')->name('storage.clear-logs');
});
