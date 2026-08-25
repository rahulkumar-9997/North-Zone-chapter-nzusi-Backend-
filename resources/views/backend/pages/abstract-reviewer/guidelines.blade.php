@extends('backend.layouts.master')
@section('title','Abstract Review Guidelines')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
@section('main-content')
<div class="content">
    <div class="abstract-review-page">
        <div class="rev-card">
            <div class="rev-card-header">
                <h4 class="rev-title">Abstract Review Guidelines</h4>
                <a href="{{ route('abstract-submission.index') }}" class="btn-rev-outline text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="p-4">
                @include('backend.pages.abstract-reviewer.partials.review-guidelines-content')
            </div>
        </div>
    </div>
</div>
@endsection