<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaticController extends Controller
{
    public function privacyPolicy() {
        return view('static.privacypolicy');
    }

    public function termsConditions() {
        return view('static.termsconditions');
    }

    public function marketing() {
        return view('static.marketing');
    }
}
