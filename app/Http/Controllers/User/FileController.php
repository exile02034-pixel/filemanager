<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index(){
        return Inertia::render('user/files/index');
    }
}
