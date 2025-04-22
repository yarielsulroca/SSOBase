<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Parsedown;

class DocumentationController extends Controller
{
    public function show()
    {
        $readmePath = base_path('README.md');
        $content = File::get($readmePath);
        
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($content);
        
        return view('documentation', ['content' => $htmlContent]);
    }
}
