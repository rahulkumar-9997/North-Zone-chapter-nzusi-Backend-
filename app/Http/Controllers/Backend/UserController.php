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
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        if ($authUser->is_admin == 1) {
            $users = User::with('roles')
                ->latest()
                ->paginate(10);

        } elseif ($authUser->hasAnyRole(['webadmin', 'admin'])) {
            $users = User::with('roles')
                ->where('is_admin', '!=', 1)
                ->latest()
                ->paginate(10);

        } else {
            $users = User::with('roles')
                ->where('id', $authUser->id)
                ->paginate(10);
        }

        return view('backend.pages.users.index', compact('users'));
    }
    
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('backend.pages.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'gender'    => 'nullable|in:male,female,other',
            'password'  => 'required|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'roles'     => 'required|array|min:1',
            'roles.*'   => 'exists:roles,id'
        ]);
        DB::beginTransaction();
        try {
            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $fileName = ImageHelper::generateFileName(
                    $request->name
                );
                $imageName = ImageHelper::uploadSingleImageWebpOnly(
                    $request->file('profile_picture'),
                    $fileName,
                    'users-profile',
                    null
                );
            }
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $request->phone,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
                'status' => $request->has('status'),
                'is_active' => $request->has('status'),
                'profile_img' => $imageName
            ]);
            $user->roles()->sync($request->roles);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'redirect' => route('users.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }
    
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('backend.pages.users.create', compact('user', 'roles', 'userRoles'));
    }
    
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id'
        ]);
        DB::beginTransaction();
        try {
            $imageName = $user->profile_picture;
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture &&
                    File::exists(public_path($user->profile_picture))) {
                    File::delete(
                        public_path($user->profile_picture)
                    );
                }
                $fileName = ImageHelper::generateFileName(
                    $request->name
                );
                $imageName = ImageHelper::uploadSingleImageWebpOnly(
                    $request->file('profile_picture'),
                    $fileName,
                    'users-profile',
                    $imageName
                );
            }
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone,
                'gender' => $request->gender,
                'status' => $request->has('status'),
                'is_active' => $request->has('status'),
                'profile_img' => $imageName
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make(
                    $request->password
                );
            }
            $user->update($data);
            $user->roles()->sync($request->roles);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'redirect' => route('users.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }
    
    public function destroy(User $user)
    {
        if(!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        if ($user->is_admin==1) {
            return redirect()->back()->with('error', 'You cannot delete an admin user.');
        }
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();
        try {
            if (!empty($user->profile_img)) {
                $imagePath = public_path(
                    'storage/images/users-profile/' . $user->profile_img
                );

                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            $user->roles()->detach();
            $user->delete();
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}