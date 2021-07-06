<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Mail;

Route::group([ 'middleware' => [ 'api', 'cors' ],'prefix' => 'auth' ], function ($router) {
    Route::get('/test', 'ApplicationController@test');
    
    //Users
    Route::post( 'login', 'AuthController@login');
    Route::post( 'add_user', 'AuthController@register');
    Route::post( 'update_user', 'ApplicationController@updateUser' );
    Route::post( 'logout', 'AuthController@logout');
    Route::post( 'update_password', 'ApplicationController@updatePassword');
    Route::post( 'refresh', 'AuthController@refresh');

    //Membership Application
    Route::post( 'add_application', 'ApplicationController@store');
    Route::post( 'manage_application', 'ApplicationController@manage_application');

    //Quaterly
    Route::post('add_quaterly', 'QuaterController@store');
    Route::post('add_activity', 'QuaterController@add_activity');
    Route::post('add_swimmer', 'QuaterController@add_swimmer');
    Route::post('add_waterpolo', 'QuaterController@add_waterpolo');
    Route::post('add_master', 'QuaterController@add_master');
    Route::post('add_coach', 'QuaterController@add_coach');
    Route::post('add_administrator', 'QuaterController@add_administrator');
    
    //Athletes
    Route::post('add_athlete', 'AthleteController@add_athlete');
    
    
    
    
    // Route::post('updateRequisition', 'ApplicationController@updateRequisition');
    // Route::post( 'getAll', 'ApplicationController@getAll');
    // Route::post('addDocuments', 'DocumentFileController@store');
    // Route::post('updateRequest', 'ApplicationController@updateItem');
    // Route::post('updateCashierVoucher', 'ApplicationController@updateCashierVoucher');
    // Route::post('getPaymentVoucher', 'ApplicationController@getPaymentVoucher');

    // Route::post('saveItems', 'ApplicationController@saveItems');
    // Route::post( 'getProcurementItems', 'ApplicationController@getProcurementItems' );
    // Route::post( 'getRole', 'ApplicationController@getRole' );
    // Route::get( 'getApproved', 'ApplicationController@approved' );

    // Route::post( 'submitLeaveApplication', 'LeaveApplicationController@store');
    // Route::get( 'getAllApplications', 'LeaveApplicationController@getAll');
    // Route::post('updateLeave', 'LeaveApplicationController@updateLeave');

    // Route::post( 'updateRecord', 'LeaveApplicationController@updateRecord');
    // Route::post( 'fetchUserFiles', 'DocumentFileController@fetchUserFiles' );

    // Route::post( 'add_payroll', 'DocumentFileController@add_payroll' );
    // Route::get( 'get_all_payrolls', 'DocumentFileController@get_all_payrolls' );
    // Route::post( 'update_payroll', 'DocumentFileController@updateRecord');
    // Route::post( 'updateProcurementRequest', 'DocumentFileController@updateProcurementRequest');

    // Route::post( 'add_remark', 'ApplicationController@add_remark' );
    // Route::post('getUserRemarks', 'ApplicationController@getRemarks');
    // Route::post('getOfficerRemarks', 'ApplicationController@getOfficerRemarks');
    // Route::post('getUserByEmail', 'ApplicationController@getUserByEmail');

    
    // Route::post('create_document', 'ApplicationController@generateFile');
    // Route::post('create_document', 'ApplicationController@downloadDocument');
    // Route::post('fetchApproved', 'ApplicationController@fetchApprovedRequests');
    // Route::post('addChildren', 'ApplicationController@addChildren');
    // Route::post('addSpouse', 'ApplicationController@addSpouse');
    
    // Route::post('getChildren', 'ApplicationController@getChildren');
    // Route::post('getHOD', 'ApplicationController@getHOD');

    // Route::get('file/country_list', 'ApplicationController@country_list');
    // Route::get('fetchAll', 'ApplicationController@fetchAll');
    // Route::post('downloadDDA', 'ApplicationController@generateDDALicense');
});




