<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('targetVsAchievment','DashboardServices@targetVsAchievment');
Route::get('registrationAndPayments','DashboardServices@registrationAndPayments');
Route::get('IndividualPayment','DashboardServices@IndividualPayment');
Route::get('CompanyPayment','DashboardServices@CompanyPayment');
Route::get('IndividualList','DashboardServices@IndividualList');

Route::get('IndividualsFromCompany','DashboardServices@IndividualsFromCompany');
Route::get('registrationVsAttendance','DashboardServices@registrationVsAttendance');
Route::get('IndividualsFromCompany','DashboardServices@IndividualsFromCompany');

Route::get('addSpeakers','DashboardServices@addSpeakers');
Route::get('UpdateSpeakers','DashboardServices@UpdateSpeakers');
Route::get('deleteSpeaker','DashboardServices@deleteSpeaker');

Route::get('ratingAmount','DashboardServices@ratingAmount');



