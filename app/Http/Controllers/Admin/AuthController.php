<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Exception;


define('LOGINPATH', '/admin/login');

class AuthController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginView()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Or use your dashboard route name
        }
        return view('pages/login', [
            'layout' => 'login',
        ]);
    }

    /**
     * Authenticate login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
		
        if (!\Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return dd('error');
        }
    }

    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        \Auth::logout();
        session()->forget('token');
        return redirect(LOGINPATH);
    }

    public function editProfile()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return view('pages.edit-profile', compact('user'));
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function changePassword(Request $request)
    {
        //   return response()->json([
        //         'error' => ['This Option is disabled for Demo!'],
        //     ]);
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user && !password_verify($request->old, $user->password)) {
                return response()->json([
                    'error' => ["Password doesn't match with old password"],
                ]);
            } else {
                $user->password = Hash::make($request->new);
                $user->update();
                return response()->json([
                    'success' => ['Update Password'],
                ]);
            }
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function editProfileApi(Request $req)
    {
        try {
            // Validation
            $validator = Validator::make($req->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ], 422);
            }

            // Auth check
            if (!Auth::guard('web')->check()) {
                return response()->json([
                    'error' => ['Unauthorized'],
                ], 401);
            }

            $user = Auth::guard('web')->user();

            // Handle profile image
            $image = null;
            if ($req->hasFile('profile')) {
                $imageContent = base64_encode(file_get_contents($req->file('profile')));
                $extension = $req->file('profile')->getClientOriginalExtension();
                $time = Carbon::now()->timestamp;
                $destinationPath = 'public/storage/images/';
                $imageName = 'profile_' . $user->id . '_' . $time . '.' . $extension;
                $path = $destinationPath . $imageName;

                // Delete old profile if exists
                if ($user->profile && File::exists($user->profile)) {
                    File::delete($user->profile);
                }

                // Save new profile
                file_put_contents($path, base64_decode($imageContent));
                $image = $path;
            } elseif ($user->profile) {
                $image = $user->profile;
            }

            // Update user
            $user->update([
                'name' => $req->name,
                'email' => $req->email,
                'profile' => $image,
            ]);

            return response()->json([
                'success' => 'Profile updated successfully!',
                'user' => $user,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
