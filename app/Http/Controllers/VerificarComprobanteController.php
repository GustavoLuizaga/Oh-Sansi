<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificarComprobanteController extends Controller
{
    public function index()
    {
        return view('inscripciones.VerificarComprobante');
    }
}
