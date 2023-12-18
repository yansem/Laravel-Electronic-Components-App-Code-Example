<?php
namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index()
    {
        $canEdit = \Spo::user()->canEdit($this->userPermission) ? 'true' : 'false';
        return view('home', compact('canEdit'));
    }
}
