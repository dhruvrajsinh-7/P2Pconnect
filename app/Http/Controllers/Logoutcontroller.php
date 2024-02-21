<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Logoutcontroller extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return redirect()->route('login')->with('message', 'You have been logged out');
    }
}
