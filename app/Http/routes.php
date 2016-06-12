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
Route::get('showAllSpeakers','DashboardServices@showAllSpeakers');//return all speakers details
Route::get('showSpeakerDetailsById','DashboardServices@showSpeakerDetailsById');//return only one speaker's details



Route::get('showAgenda','DashboardServices@showAgenda');
Route::get('saveAgenda','DashboardServices@saveAgenda');
Route::get('updateAgenda','DashboardServices@updateAgenda');
Route::get('deleteAgendaItemByID','DashboardServices@deleteAgendaItemByID');//delete one agenda item only
Route::get('deleteAllAgendaItemsFromDate','DashboardServices@deleteAllAgendaItemsFromDate');//delete all agenda items using date


Route::get('ratingAmount','DashboardServices@ratingAmount');

Route::get('userLoginDetails','DashboardServices@userLoginDetails');//user login details

Route::get('deleteUser','LoginController@deleteUser');
Route::get('getusers','LoginController@users');
Route::post('login','LoginController@login');
Route::post('register','LoginController@register');
Route::post('edit','LoginController@edit');


Route::get('allPatPDFS','DashboardServices@allPatPDFS');
