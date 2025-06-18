<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReglamentoController extends Controller
{
    public function index()
    {
        return view('reglamento');
    }
}