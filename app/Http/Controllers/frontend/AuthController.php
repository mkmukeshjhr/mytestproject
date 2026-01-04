<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserDeviceDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function logout(Request $request)
    {

        if (!authcheck())
            return redirect()->route('front.home');


        $session = new Session();
        $token = $session->get('token');
        // $logout = Http::withoutVerifying()->post(url('/') . '/api/logout', [
        //     'token' => $token,
        // ])->json();

        $requestApi = Request::create(
            '/api/logout',
            'POST',
            ['token' => $token]
        );

        $response = Route::dispatch($requestApi);
        $logout = json_decode($response->getContent(), true);

        $userDeviceDetail = UserDeviceDetail::where('userId', authcheck()['id'])->first();
        if ($userDeviceDetail) {
            $userDeviceDetail->subscription_id_web = null;
            $userDeviceDetail->fcmToken = null;
            $userDeviceDetail->updated_at = Carbon::now()->timestamp;
            $userDeviceDetail->update();
        }
        $session = new Session();
        $session->remove('token');

        return response()->json([
            "message" => "Logout User Successfully",
        ], 200);
    }

    public function userLogin(Request $request)
    {

        return view('frontend.pages.user-login');
    }


    public function verifyOTL(Request $request)
    {
        if (!empty($request->fromWeb)) {

            $countryCode = !empty($request->countryCode) ? $request->countryCode : '+91';
            $session = new Session();
            $reftoken = $session->get('referrel_token');

            if (!empty($request->isGoogleLogin)) {

                $login = Http::withoutVerifying()->post(url('/') . '/api/loginAppUser', [
                    'email' => $request->email,
                    'name' => $request->name,
                    'countryCode' => $countryCode,
                    'country' => $countryCode == '+91' ? 'india' : $request->country,
                    'name' => $request->name,
                    'referral_token' => $reftoken,
                ])->json();

                if ($login['status'] != 400) {
                    $session = new Session();
                    $session->set('token', $login['token']);
                    return response()->json([
                        'status' => 200,
                        'message' => "Login Successfully",
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => $login['error']['email'][0],
                    ], 400);
                }
            } else {
                $msg91AuthKey = DB::table('systemflag')->where('name', 'msg91AuthKey')->pluck('value')->first();
                $countryCode = ltrim($request->countryCode, '+');
                $fullMobile = (string)$countryCode . $request->contactNo;
                $response = Http::withHeaders([
                    'authkey' => $msg91AuthKey,
                ])->get('https://control.msg91.com/api/v5/otp/verify', [
                    'otp' => $request->otp,
                    'mobile' => $fullMobile
                ]);
                // dd($response);
                if ($response->successful()) {
                    // $login = Http::withoutVerifying()->post(url('/') . '/api/loginAppUser', [
                    //     'contactNo' => $request->contactNo,
                    //     'countryCode' => $countryCode,
                    //     'country' => $countryCode == '+91' ? 'india' : $request->country,
                    //     'referral_token' => $reftoken,
                    // ])->json();

                    $requestApi = Request::create(
                        '/api/loginAppUser',
                        'POST',
                        [
                            'contactNo' => $request->contactNo,
                            'countryCode' => $countryCode,
                            'country' => $countryCode == '+91' ? 'india' : $request->country,
                            'referral_token' => $reftoken,
                        ]
                    );

                    $response = Route::dispatch($requestApi);
                    $login = json_decode($response->getContent(), true);
                    // dd($login);
                    if ($login['status'] != 400) {
                        $session = new Session();
                        $session->set('token', $login['token']);
                        // return redirect()->back();
                        return response()->json([
                            'status' => 200,
                            'message' => "Login Successfully",
                        ], 200);
                    } else {
                        // return redirect()->route('front.home', ['error' => $login['error']['contactNo'][0]]);
                        return response()->json([
                            'status' => 400,
                            'message' => $login['error']['contactNo'][0],
                        ], 400);
                    }
                } else {
                    // Log or handle error
                    return response()->json([
                        'status' => 400,
                        'message' => 'Failed to verify OTP',
                        'details' => $response->body()
                    ], 400);
                }
            }
        }
    }
}
