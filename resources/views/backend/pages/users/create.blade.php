@extends('backend.layouts.master')
@section('title','Create User')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Create User</h4>
            <a href="{{ route('users.index') }}"
                data-title="Create User"
                class="btn btn-primary">
                <i data-feather="arrow-left" class="me-2"></i> Back to User List
            </a>
        </div>
        <div class="card-body">
            <form id="userForm" action="{{ isset($user) ? route('users.update',$user->id) : route('users.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name',$user->name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email"  name="email"  id="email" class="form-control" value="{{ old('email',$user->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone',$user->phone_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-control select2">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Password
                                @if(!isset($user))
                                *
                                @endif
                            </label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Roles *</label>
                            <select name="roles[]" id="roles" class="form-control select2" multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                {{
                                isset($user)
                                &&
                                $user->roles->contains($role->id)
                                ? 'selected'
                                : ''
                                }}>
                                {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input type="checkbox"  class="form-check-input" id="status" name="status" value="1"
                                    {{
                                        old(
                                            'status',
                                            $user->status ?? 1
                                        )
                                        ? 'checked'
                                        : ''
                                    }}>
                                <label
                                    class="form-check-label"
                                    for="status">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveUserBtn">
                        {{ isset($user) ? 'Update' : 'Submit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/users.js') }}?v={{ env('APP_VERSION') }}"></script>
@endpush