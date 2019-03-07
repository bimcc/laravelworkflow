<?php

use App\Http\Controllers\IndexController;

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

Route::group(['prefix'=>''],function(){
    // IndexController
    Route::get('/index/index','IndexController@index');
    Route::get('/index/welcome','IndexController@welcome');
    Route::get('/index/doc','IndexController@doc');
    Route::any('/index/login','IndexController@login');
    
    // NewsController
    Route::any('/news/newstype','NewsController@newstype');
    Route::any('/news/index','NewsController@index');
    Route::any('/news/add','NewsController@add');
    Route::any('/news/edit/{id?}','NewsController@edit');
    Route::any('/news/del/{id?}','NewsController@del');
    Route::any('/news/view/{id?}','NewsController@view');
    Route::any('/news/type','NewsController@type');
    Route::any('/news/type_edit','NewsController@type_edit');
    Route::any('/news/type_del','NewsController@type_del');    

    // UserController
    Route::any('/user/type_del','NewsController@type_del');    
    
    // WorkflowController
    Route::any('/wf/wfindex','WorkflowController@wfindex');    
    Route::any('/wf/wfdesc/{flow_id}','WorkflowController@wfdesc');    
    Route::any('/wf/wfadd','WorkflowController@wfadd');    
    Route::any('/wf/wfedit/{id}','WorkflowController@wfedit');    
    Route::any('/wf/wfchange/{id?}/{status?}','WorkflowController@wfchange');    
    Route::any('/wf/delete_process','WorkflowController@delete_process');    
    Route::any('/wf/del_allprocess','WorkflowController@del_allprocess');    
    Route::any('/wf/add_process','WorkflowController@add_process');    
    Route::any('/wf/save_canvas','WorkflowController@save_canvas');    
    Route::any('/wf/wfatt','WorkflowController@wfatt');    
    Route::any('/wf/save_attribute','WorkflowController@save_attribute');    
    Route::any('/wf/super_user/{type}','WorkflowController@super_user');    
    Route::any('/wf/super_role','WorkflowController@super_role');    
    Route::any('/wf/super_get','WorkflowController@super_get');    
    Route::any('/wf/wfjk','WorkflowController@wfjk');    
    Route::any('/wf/btn','WorkflowController@btn');    
    Route::any('/wf/status','WorkflowController@status');    
    Route::any('/wf/wfstart','WorkflowController@wfstart');    
    Route::any('/wf/start_save','WorkflowController@start_save');    
    Route::any('/wf/wfcheck','WorkflowController@wfcheck');    
    Route::any('/wf/do_check_save','WorkflowController@do_check_save');    
    Route::any('/wf/ajax_back','WorkflowController@ajax_back');    
    Route::any('/wf/checkflow','WorkflowController@Checkflow');    
    Route::any('/wf/wfup','WorkflowController@wfup');    
    Route::any('/wf/wfend','WorkflowController@wfend');    
    Route::any('/wf/wfupsave','WorkflowController@wfupsave');    
    Route::any('/wf/wfupsave','WorkflowController@wfupsave');    

});


