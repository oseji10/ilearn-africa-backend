<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\States;
class StatesController extends Controller
{
    public function show()
    {
        $states = States::all();
        return response()->json(['states' => $states]);
    }

}
