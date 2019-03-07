<?php
namespace App\Http\Controllers;

use App\Work\Model\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Work\Model\Menu;

class IndexController extends Controller{
    public function index(Request $request){
        $user = User::select('id','username','role')->get();
        $menu = Menu::get();
        return view('index.index',['user'=>$user,'menu'=>$menu]);
    }

    public function welcome(Request $request){
       $user = User::select('id','username','role')->get();
        return view('index.welcome',['user'=>$user]);
    }

    public function doc(Request $request){
        return view('index.doc');
    }

    public function login(Request $request){
        Session::flush();
        $info = User::find($request->get('id'));
        Session::put('uid', $info['id']);
		Session::put('uname', $info['username']);
        Session::put('role', $info['role']);
        
        return response('success');
    }

}