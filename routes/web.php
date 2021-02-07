<?php

use App\Models\FieldForce;
use App\Models\OrderDelivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::get('/data-clean', function () {


//    $retail_visits = \App\Models\RetailVisit::all();
//
//    foreach ($retail_visits as $retail_visit) {
//        $code = explode('-', $retail_visit->FieldForce);
//        $retail_visit->update([
//           'FieldForce' => $code[0]
//        ]);
//
//    }
//
//    dd('SUCCESSFULL');


});


Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();



    Route::post('synchronize-data', '\App\Http\Controllers\OrderDeliveryController@synchronize_data')->name('synchronize-data');

});

Route::get('dashboard', '\App\Http\Controllers\OrderDeliveryController@dashboard')->name('dashboard');

Route::get('/field-forces/region-wise-field-forces', '\App\Http\Controllers\OrderDeliveryController@region_wise_field_forces')->name('region-wise-field-forces');
Route::get('/field-forces/area-wise-field-forces', '\App\Http\Controllers\OrderDeliveryController@area_wise_field_forces')->name('area-wise-field-forces');
