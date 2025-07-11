<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view("register");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => ["required", "string"],
            "email" => ["required", "string", "email", "unique:users"],
            "password" => ["required", "min:6"]
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        Auth::login($user);

        return redirect("dashboard");
    }
}
