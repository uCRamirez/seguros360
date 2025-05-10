<?php

namespace App\Http\Controllers;

use App\Models\Company;

class LandingPageController extends Controller
{
    public function landing()
    {
        $this->data['company'] = Company::first();

        return view('landing', $this->data);
    }
}
