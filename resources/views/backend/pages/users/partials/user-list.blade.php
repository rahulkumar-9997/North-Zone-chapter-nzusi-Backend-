@if($users->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th width="70">ID</th>
                    <th width="80">Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            @if($user->profile_img)
                                <img
                                    src="{{ asset('storage/images/users-profile/'.$user->profile_img) }}"
                                    alt="{{ $user->name }}"
                                    width="50"
                                    height="50"
                                    class="rounded-circle"
                                    style="object-fit:cover;">
                            @else
                            @php
                            $colors = [
                                '#0d6efd',
                                '#198754',
                                '#dc3545',
                                '#fd7e14',
                                '#6f42c1',
                                '#20c997',
                                '#6610f2'
                            ];
                            $bgColor = $colors[$user->id % count($colors)];
                            @endphp

                                <div
                                    class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                    style="
                                        width:40px;
                                        height:40px;
                                        background:{{ $bgColor }};
                                        font-size:18px;
                                    ">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->is_admin == 1)
                                <span class="badge bg-danger">Admin</span>
                            @endif
                            @if($user->phone_number)
                            <br>
                            <small class="text-muted">
                                {{ $user->phone_number }}
                            </small>
                            @endif
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            @forelse($user->roles as $role)
                            <span class="badge bg-primary">
                                {{ $role->name }}
                            </span>
                            @empty
                            <span class="badge bg-danger">
                                No Role
                            </span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            {{ $user->created_at?->format('d M Y') }}
                        </td>
                        <td>
                            
                            <a
                                href="{{ route('users.edit',$user->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form
                                action="{{ route('users.destroy',$user->id) }}"
                                method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm show_confirm_users"
                                    data-name="{{ $user->name }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        No Users Found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $users->links() }}
    </div>
@else
    <div class="text-center p-5">
        <i class="ti ti-users-off fs-40"></i>
        <p class="mt-3 mb-0">No Users Found</p>
    </div>
@endif