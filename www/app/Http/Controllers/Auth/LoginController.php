<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view("login");
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "string", "email"],
            "password" => ["required", "string"]
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Предоставленные учетные данные не соответствуют нашим записям.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route("dashboard");
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        return redirect("welcome");
    }
}
