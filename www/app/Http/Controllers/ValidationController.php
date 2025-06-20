<?php

namespace App\Http\Controllers;

use App\Models\Validation;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function show(Validation $validation)
    {
        return view('validation', compact('validation'));
    }
}
