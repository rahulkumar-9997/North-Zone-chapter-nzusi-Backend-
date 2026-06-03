<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('backend.pages.users.index', compact('users'));
    }
    
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('backend.pages.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'gender'    => 'nullable|in:male,female,other',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password'  => 'required|min:8|confirmed',
            'roles'     => 'required|array|min:1',
            'roles.*'   => 'exists:roles,id'
        ]);
        DB::beginTransaction();
        try {
            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $fileName = ImageHelper::generateFileName($request->name);
                $imageName = ImageHelper::uploadSingleImageWebpOnly(
                    $request->file('profile_picture'),
                    $fileName,
                    'users-profile',
                    null
                );
            }
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => $request->has('status') ? 1 : 0,
                'phone_number' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'is_active' => $request->has('status') ? 1 : 0,
                'is_admin' => 0,
                'profile_picture' => $imageName,
            ]);
            if($request->roles) {
                $user->roles()->attach($request->roles);
            }
            DB::commit();
            return response()->json([
                'status'   => 'success',
                'message'  => 'User created successfully.',
                'redirect' => route('users.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('backend.pages.users.edit', compact('user', 'roles', 'userRoles'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,

            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id'
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status ?? true
        ]);
        
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        
        // Sync multiple roles (remove old and assign new)
        $user->roles()->sync($request->roles);
        
        return redirect()->route('users.index')
            ->with('success', 'User updated with ' . count($request->roles) . ' roles successfully.');
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->roles()->detach(); // Remove all role associations first
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}