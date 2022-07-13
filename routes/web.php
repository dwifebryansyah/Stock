<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
// Route::get('/logout', 'DashboardController@logout')->name('dashboard');

Route::name('barang.')->group(function () {
    Route::get('/barang', 'BarangController@index')->name('index');
    Route::post('/barang_store', 'BarangController@store')->name('store');
    Route::post('/barang_update', 'BarangController@update')->name('update');
    Route::get('/tableBarang', 'BarangController@table')->name('table');
    Route::get('/delete/{id}', 'BarangController@delete')->name('delete');
});

Route::name('stok.')->group(function () {
    Route::get('/stok', 'StockController@index')->name('index');
    Route::post('/detailbarang', 'StockController@detailData')->name('detail.barang');    
    Route::post('/stok_store', 'StockController@store')->name('store');
    Route::post('/stok_update', 'StockController@update')->name('update');
    Route::get('/tableStock', 'StockController@table')->name('table');
    Route::get('/stok/{id}', 'StockController@delete')->name('delete');
});

Route::name('laporan.')->group(function () {
    Route::get('/laporan', 'ReportController@index')->name('index');
    Route::get('/tableLaporan', 'ReportController@table')->name('table');
    Route::get('/exportExcel/{mulai}/{akhir}/{barang_id}', 'ReportController@excel')->name('excel');
});

