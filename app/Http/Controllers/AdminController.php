<?php
namespace App\Http\Controllers;

class AdminController extends Controller{
    public function initialize(){
        defined('uid') or define('uid',session('uid'));
        if(null === uid){
            return response('请先模拟登入');
        }
    }
}