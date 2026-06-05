@extends('backend.layouts.master')
@section('title', isset($user) ? 'Edit User' : 'Create User')
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>
                {{ isset($user) ? 'Edit User' : 'Create User' }}
            </h4>
            <a href="{{ route('users.index') }}"
                class="btn btn-primary">
                Back
            </a>
        </div>
        <div class="card-body">
            <form id="userForm"
                action="{{ isset($user) ? route('users.update',$user->id) : route('users.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if(isset($user))
                @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Full Name *</label>
                            <input type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="{{ old('name',$user->name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email',$user->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone',$user->phone_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Gender</label>
                            <select name="gender"
                                id="gender"
                                class="form-control select2">
                                <option value="">Select Gender</option>
                                <option value="male"
                                    {{ old('gender',$user->gender ?? '')=='male' ? 'selected':'' }}>
                                    Male
                                </option>
                                <option value="female"
                                    {{ old('gender',$user->gender ?? '')=='female' ? 'selected':'' }}>
                                    Female
                                </option>
                                <option value="other"
                                    {{ old('gender',$user->gender ?? '')=='other' ? 'selected':'' }}>
                                    Other
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>
                                Password
                                @if(!isset($user))
                                *
                                @endif
                            </label>
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>
                                Confirm Password
                            </label>
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>
                                Profile Picture
                            </label>
                            <input type="file"
                                name="profile_picture"
                                id="profile_picture"
                                class="form-control">
                            @if(isset($user) && $user->profile_img)
                            <img
                                src="{{ asset('storage/images/users-profile/' . $user->profile_img) }}"
                                width="80"
                                class="mt-2 border rounded">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>
                                Roles *
                            </label>
                            <select
                                name="roles[]"
                                id="roles"
                                class="form-control select2"
                                multiple>
                                @foreach($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    {{
                                        isset($user)
                                        && $user->roles->contains($role->id)
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
                            <label>Status</label>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="status"
                                    name="status"
                                    value="1"
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
                    <a href="{{ route('users.index') }}"
                        class="btn btn-secondary">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveUserBtn">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
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