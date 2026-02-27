<?php

namespace App\Http\Controllers;

use App\Models\CheckingFoto;
use App\Models\ConfiguracaoEmail;
use Illuminate\Http\Request;

class CheckingController extends Controller
{
    public function publicShow($id)
    {
        $record = CheckingFoto::with('inventario')->findOrFail($id);
        $config = ConfiguracaoEmail::first(); // Pega seu logo e cor

        return view('checking-publico', compact('record', 'config'));
    }
}