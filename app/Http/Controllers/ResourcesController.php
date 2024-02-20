<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
// use App\Document;
use App\Resources;
use App\User;
use Hashids;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ResourcesController extends Controller
{

    public function index(Request $request)
    {
        return view('resources.index');
    }

}