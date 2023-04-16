<?php
declare(strict_types=1);

namespace App\Http\Controllers\Front;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

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
