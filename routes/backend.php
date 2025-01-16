<?php

use Illuminate\Support\Facades\Route;
use Spatie\TranslationLoader\LanguageLine;

Route::get('/', function () {
    return redirect(route('login.form'));
});

Route::get(PREFIX, function () {
    return redirect(route('login.form'));
});

Route::group(['namespace' => 'System', 'prefix' => PREFIX, 'middleware' => ['language', 'pinewheel-log']], function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login.form');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('forgot-password', 'Auth\ForgotPasswordController@showRequestForm')->name('forgot.password');
    Route::post('forgot-password', 'Auth\ForgotPasswordController@handleForgotPassword')->name('post.forgot.password');
    Route::get('/reset-password/{email}/{token}', 'Auth\ResetPasswordController@showSetResetForm')->name('reset.password');
    Route::post('/reset-password', 'Auth\ResetPasswordController@handleSetResetPassword');
    Route::get('/set-password/{email}/{token}', 'Auth\ResetPasswordController@showSetResetForm')->name('set.password');
    Route::post('/set-password', 'Auth\ResetPasswordController@handleSetResetPassword');
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::group(['middleware' => ['auth', 'antitwofa']], function () {
        Route::get('/login/verify', 'Auth\VerificationController@showVerifyPage');
        Route::post('/login/verify', 'Auth\VerificationController@verify')->name('verify.post');
        Route::get('/login/send-again', 'Auth\VerificationController@sendAgain')->name('verify.send.again');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('change-password', 'user\UserController@changePassword')->name('change.password');
    });

    Route::group(['middleware' => ['auth', 'permission', 'twofa', 'reset.password']], function () {
        Route::get('/home', 'IndexController@index')->name('home');
        Route::resource('/roles', 'user\RoleController', ['except' => ['show']]);

        Route::resource('/users', 'user\UserController', ['except' => ['show']]);

        Route::get('/profile', 'profile\ProfileController@index')->name('profile');
        Route::put('/profile/{id}', 'profile\ProfileController@update');

        Route::post('users/reset-password/{id}', 'user\UserController@passwordReset')->name('user.reset-password');

        Route::get('/login-logs', 'logs\LoginLogsController@index');
        Route::get('/activity-logs', 'logs\LogsController@index');

        Route::resource('/languages', 'language\LanguageController', ['except' => ['show', 'edit', 'update']]);
        Route::get('/languages/set-language/{lang}', 'language\LanguageController@setLanguage')->name('set.lang');
        Route::get('/country-language/{country_id}', 'countryLanguage\CountryLanguageController@getLanguages');

        Route::resource('/translations', 'language\TranslationController', ['except' => ['show', 'edit', 'create']]);
        Route::get('/translations/download-sample', 'language\TranslationController@downloadSample');
        Route::get('/translations/download/{group}', 'language\TranslationController@downloadExcel');
        Route::post('/translations/upload/{group}', 'language\TranslationController@uploadExcel');

        Route::resource('/email-templates', 'systemConfig\EmailTemplateController', ['except' => ['show', 'create', 'store']]);

        Route::resource('/configs', 'systemConfig\ConfigController');

        Route::resource('/categories', 'category\CategoryController', ['except' => ['show']]);
        Route::resource('categories/{id}/sub-category', 'category\SubCategoryController');
        Route::get('/clear-lang', function () {
            LanguageLine::truncate();
        });

        Route::get('/mail-test/create', 'MailTestController@create');
        Route::post('/mail-test', 'MailTestController@sendEmail');
        Route::get('/search-motels', 'google\GoogleController@searchMotels');
        Route::get('/search-motels-by-suburb', 'google\GoogleController@searchAllMotels');
    });
});
