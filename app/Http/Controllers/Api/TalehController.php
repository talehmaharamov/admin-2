<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taleh;

class TalehController extends Controller
{
    public function index()
    {
        if (Taleh::where('status', 1)->exists()) {
            return response()->json(['taleh' => Taleh::where('status', 1)->with('photos')->get()], 200);
        } else {
            return response()->json(['message' => 'Taleh-is-empty'], 404);
        }
    }

    public function show($id)
    {
        if (Taleh::where('status', 1)->where('id', $id)->exists()) {
            return response()->json(['taleh' => Taleh::where('status', 1)->where('id', $id)->with('photos')->first()], 200);
        } else {
            return response()->json(['message' => 'taleh-is-not-founded'], 404);
        }
    }
}
