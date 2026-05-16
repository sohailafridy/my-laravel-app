<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\City;

class TestController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $cities = City::all();
        return view('test', compact('countries', 'cities'));
    }
}
