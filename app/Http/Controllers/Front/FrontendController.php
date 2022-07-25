<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class FrontendController extends Controller
{
    /**
     * Handle requests to frontend index.
     *
     * @return  Response
     */
    public function index(): Response
    {
        return response()->view('front');
    }
}
