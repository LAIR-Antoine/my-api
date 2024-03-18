<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reco;

class RecoController extends Controller
{
    public function index()
    {
        return Reco::all();
    }
}