<?php

namespace App\Http\Controllers\ApiPanel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $docs = Category::with('apiDocs')->get();

        return view('api_panel.index', compact('docs'));
    }
}
