<?php

namespace App\Http\Controllers\frontend\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserDeviceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if(!astroauthcheck()){
            return redirect()->route('front.astrologerlogin');
        }


        //  dd(astroauthcheck()['astrologerId']);
        $astrologerId=astroauthcheck()['astrologerId'];
        Artisan::call('cache:clear');

        $getChatRequest = Http::withoutVerifying()->post(url('/') . '/api/chatRequest/get', [
            'astrologerId' => $astrologerId,
        ])->json();


        $getCallRequest = Http::withoutVerifying()->post(url('/') . '/api/callRequest/get', [
            'astrologerId' => $astrologerId,
        ])->json();
            // return $getCallRequest;

        // dd($getCallRequest);
        $getUserReport = Http::withoutVerifying()->post(url('/') . '/api/getUserReport', [
            'astrologerId' => $astrologerId,
        ])->json();

        $agoraAppIdValue = DB::table('systemflag')
        ->where('name', 'AgoraAppId')
        ->select('value')
        ->first();

        $agorcertificateValue = DB::table('systemflag')
        ->where('name', 'AgoraAppCertificate')
        ->select('value')
        ->first();

        $channel_name='astrowayGuruLive_'.astroauthcheck()['astrologerId'].'';

        $getUserReportRequestById = Http::withoutVerifying()->post(url('/') . '/api/getUserReportRequestById', [
            'id' => $request->id,
        ])->json();

            // dd($getChatRequest);
        return view('frontend.astrologers.pages.index',compact('getChatRequest','getCallRequest','getUserReport','agoraAppIdValue','agorcertificateValue','channel_name','getUserReportRequestById'));
    }


public function getChatRequests(Request $request)
{
    Artisan::call('cache:clear');
    $astrologerId = astroauthcheck()['astrologerId'];

    $response = Http::withoutVerifying()->post(url('/') . '/api/chatRequest/get', [
        'astrologerId' => $astrologerId,
    ]);

    if ($response->successful()) {
        $getChatRequest = $response->json();
    } else {
        \Log::error('Chat request API failed', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $getChatRequest = [
            'error' => true,
            'message' => 'Unable to fetch chat requests from API.',
        ];
    }

    return response()->json($getChatRequest);
}


public function getCallRequests(Request $request)
{
    Artisan::call('cache:clear');
    $astrologerId = astroauthcheck()['astrologerId'];
    $getCallRequest = Http::withoutVerifying()->post(url('/') . '/api/callRequest/get', [
        'astrologerId' => $astrologerId,
    ])->json();

    return response()->json($getCallRequest);
}

public function getReportRequests(Request $request)
{
    Artisan::call('cache:clear');
    $astrologerId = astroauthcheck()['astrologerId'];
    $getUserReport = Http::withoutVerifying()->post(url('/') . '/api/getUserReport', [
        'astrologerId' => $astrologerId,
    ])->json();

    return response()->json($getUserReport);
}

public function storeSubscriptionIdForAstro(Request $request){
    if(astroauthcheck()){
        $userId = astroauthcheck()['id'];
          // dd($userId);
      // Find the user's device details
          $userDeviceDetails = DB::table('user_device_details')->where('userId', $userId)->first();

          if($userDeviceDetails){
               DB::table('user_device_details')
              ->where('userId', $userId)
               ->update([
                      'subscription_id_web' => $request->subscription_id_web,
                      'updated_at' => now()
                  ]);
          }else{
               $userDeviceDetail = UserDeviceDetail::create([
                  'userId' => $userId,
                  'appId' => 1,
                  'subscription_id_web' => $request->subscription_id_web,
                  'created_at' => now(),
                  'updated_at' => now(),
              ]);
          }

          return response()->json(['message' => 'Subscription ID stored successfully.'], 200);


      }

}





public function astroAppointment(Request $request)
{
    if(!astroauthcheck()){
            return redirect()->route('front.astrologerlogin');
        }

    $userId = astroauthcheck()['id']; // user id

    $appointments = DB::table('call_request_apoinments')
        ->join('callrequest', 'callrequest.id', '=', 'call_request_apoinments.callId')
        ->join('astrologers', 'astrologers.id', '=', 'call_request_apoinments.astrologerId')
        ->where('call_request_apoinments.userId', $userId)
        ->select(
            // call_request_apoinments se
            'call_request_apoinments.id as id',
            'call_request_apoinments.callId',
            'call_request_apoinments.astrologerId',
            'call_request_apoinments.userId',
            'call_request_apoinments.amount',
            'call_request_apoinments.call_duration',
            'call_request_apoinments.call_method',
            'call_request_apoinments.status as appointmentStatus',
            'call_request_apoinments.IsActive',
            'call_request_apoinments.created_at',
            'call_request_apoinments.updated_at',

            // callrequest se
            'callrequest.callStatus',
            'callrequest.IsSchedule',
            'callrequest.channelName',
            'callrequest.call_type',
            'callrequest.totalMin',
            'callrequest.schedule_date',
            'callrequest.schedule_time',

            // astrologer info
            'astrologers.name as astrologerName',
            'astrologers.profileImage'
        )
        ->orderBy('call_request_apoinments.id', 'DESC')
        ->get();

    return view('frontend.astrologers.pages.astro-appointments', compact('appointments'));
}




public function deleteAstroAppointment($id)
{
    if(!astroauthcheck()){
            return redirect()->route('front.astrologerlogin');
        }

    $userId = astroauthcheck()['id'];

    $appointment = DB::table('callrequest')
        ->where('id', $id)
        ->where('userId', $userId)
        ->first();

    if (!$appointment) {
        return redirect()->back()->with('error', 'Appointment not found.');
    }

    // Check schedule date & time
    if ($appointment->IsSchedule != 1 || !$appointment->schedule_date || !$appointment->schedule_time) {
        // Not scheduled, can delete
        DB::table('callrequest')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }

    $scheduleDateTime = \Carbon\Carbon::parse($appointment->schedule_date . ' ' . $appointment->schedule_time);
    $now = \Carbon\Carbon::now();
    $diffMinutes = $now->diffInMinutes($scheduleDateTime, false); // negative if past

    if ($diffMinutes < 0) {
        return redirect()->back()->with('info', 'Your appointment has already started or expired. You cannot delete it.');
    } elseif ($diffMinutes <= 5) {
        return redirect()->back()->with('warning', 'Your appointment starts soon. You cannot cancel it.');
    } else {
        DB::table('callrequest')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }
}



}
