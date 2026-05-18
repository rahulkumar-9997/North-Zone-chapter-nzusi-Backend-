@extends('backend.layouts.master')
@section('title','Abstract Submission')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Abstract Submission List</h4>            
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="abstract-submission-list-table-render">
                    @include('backend.pages.abstract-submission.partials.abstract-submission-list', ['abstractSubmissions' => $abstractSubmissions ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
@endpush