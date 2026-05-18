<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbstractSubmission;

class AbstractSubmissionController extends Controller
{
    public function index()
    {
        $abstractSubmissions = AbstractSubmission::latest()->paginate(30);
        return view('backend.pages.abstract-submission.index', compact('abstractSubmissions'));
    }
}
