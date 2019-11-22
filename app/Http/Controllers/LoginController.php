<?php

namespace App\Http\Controllers;

use App\Libraries\ItopManager\ItopManager;
use Illuminate\Http\Request;
use Response;

class LoginController extends Controller
{
    public function login(Request $request){
        $pwd = $request->input('password');
        $user =  $request->input('username');
        $itopManager = new ItopManager();
        $result = $itopManager->login($user, $pwd);
        if(!is_string($result)){
            if($result){
                $contact = $itopManager->read('Contact','name,email',"SELECT Contact AS c JOIN User AS u ON u.contactid = c.id WHERE u.login = '". $user ."'");
                return Response::json(array_values($contact)[0]['fields'], 200);
            }
        }
        return Response::json('not authorized', 403);
    }
}
