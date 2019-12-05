<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user->first_name= $request->input('first_name');
        $user->last_name= $request->input('last_name');
        $user->mail = $request->input('email');
        $user->mobile = $request->input('tel_mobile');
        $user->driver_licence= $request->input('driver_licence');
        $user->function = $request->input('function');
        $user->source= $request->input('source');
        $user->pre_sal= $request->input('pre_sal');
        $user->act_sal = $request->input('act_sal');
        $user->disponibility= $request->input('disponibility');
        $user->status= $request->input('status');
        $user->save();
        return response()->json('success', 201);





    }

    /**
     * Display the specified resource.
     *
     * @param  string  $email
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $user = User::where('mail',$email)->get();
        ///dd($user);
        return Response::json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $email
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $email)
    {
        $user = User::where('email',$email)->get();

        $user->first_name= $request->input('first_name');
        $user->last_name= $request->input('last_name');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->driver_licence= $request->input('driver_licence');
        $user->function = $request->input('function');
        $user->source= $request->input('source');
        $user->pre_sal= $request->input('pre_sal');
        $user->act_sal = $request->input('act_sal');
        $user->disponibility= $request->input('disponibility');
        $user->status= $request->input('status');

        $user->save();

        return Response::json('success', 201);

    }

}
