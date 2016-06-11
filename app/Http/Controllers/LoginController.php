<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\user_detail;

class LoginController extends Controller
{
    //

    public function login(Request $request){



      $user = DB::select(DB::raw("select * from user_details where email = '".$request->input('email')."' AND password = '".$request->input('password')."'"));

return $user;
      if(!empty($user)){

        return $user[0];
      }
      else{

        return 0;
      }
    }


    public function register(Request $request){

      $user = new user_detail;

      $user->username = $request->input('username');
      $user->password = $request->input('password');
      $user->permissions = json_encode($request->input('permissions'));
      $user->email = $request->input('email');

      $user->save();

    }

    public function edit(Request $request){

      $user = user_detail::find($request->input('id'));

      var_dump($request->input('id'));
      $user->username = $request->input('username');
      $user->password = $request->input('password');
      $user->permissions =  json_encode($request->input('permissions'));
      $user->email = $request->input('email');

      $user->save();

    }

    public function users(){


      $users = user_detail::all();

      return $users;
    }

    public function deleteUser(Request $request){
      $user = user_detail::find($request->input('id'));

      $user->delete();

    }
}
