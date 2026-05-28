<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbstractSubmission;

class AbstractSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = AbstractSubmission::query();
        if ($request->filled('presentation_type')) {
            $query->where(
                'presentation_type',
                $request->presentation_type
            );
        }
        $abstractSubmissions = $query
            ->latest()
            ->paginate(30);

        if ($request->ajax()) {
            return view(
                'backend.pages.abstract-submission.partials.abstract-submission-list',
                compact('abstractSubmissions')
            )->render();
        }
        return view(
            'backend.pages.abstract-submission.index',
            compact('abstractSubmissions')
        );
    }
    public function show($id)
    {
        $abstractSubmission = AbstractSubmission::findOrFail($id);
        return view('backend.pages.abstract-submission.show', compact('abstractSubmission'));
    }
}
