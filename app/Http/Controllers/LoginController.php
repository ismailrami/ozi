<?php

namespace App\Http\Controllers;

use App\Libraries\ItopManager\ItopManager;
use Illuminate\Http\Request;
use Response;

class LoginController extends Controller
{
    public function login(Request $request){
        $itopManager = new ItopManager();
        $result = $itopManager->read('Applicant','firstname','SELECT Applicant WHERE email LIKE "%.%"');
        return Response::json($result, 200);
    }
}
