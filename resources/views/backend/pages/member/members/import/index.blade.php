@extends('backend.layouts.master')
@section('title','Import Member')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <a href="{{ route('manage-member.index') }}"
                class="btn btn-info">
                <i class="fa fa-arrow-left me-2"></i> Back to Member list Page
            </a>
            <h4 class="card-title">Import Member</h4>
        </div>
        <div class="card-body p-2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-2">
                        <h5 class="text-center text-danger">Excel Import Format</h5>
                    </div>
                    <div class="member-import-format">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <tr>
                                <th>membership_no</th>
                                <th>name</th>
                                <th>email</th>
                                <th>city_name</th>
                                <th>mobile_no</th>
                                <th>membership</th>
                            </tr>
                            <tr>
                                <td>
                                    NZB044F
                                </td>
                                <td>
                                    Dr Shitij Bali
                                </td>
                                <td>
                                    shitij.bali@yahoo.com
                                </td>
                                <td>
                                    New Delhi
                                </td>
                                <td>
                                   9910755660
                                </td>
                                <td>
                                   Full Life
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="mb-2 mt-3">
                        <h5 class="text-center text-info">Excel Import Form</h5>
                    </div>
                    <div class="member-import-form">
                        @if(session('import_errors'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach(session('import_errors') as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="import-member-form" action="{{ route('manage-member.import.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">                                
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Import File 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" name="excel_file" id="excel_file">
                                        <div class="invalid-feedback"></div>
                                        <small class="text-danger">Upload .xlsx, .xls, or .csv file</small>
                                    </div>
                                </div>                                
                            </div>                           

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center justify-content-start mb-4">
                                        <a href="{{ route('manage-member.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" id="import-btn" class="btn btn-primary">Import</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="import-error-box"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/member-import.js') }}"></script>
@endpush