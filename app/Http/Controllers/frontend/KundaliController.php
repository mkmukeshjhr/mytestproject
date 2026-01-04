<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Session\Session;


class KundaliController extends Controller
{
    
     public function getPanchang(Request $request)
{
    Artisan::call('cache:clear');

    $panchangDate = $request->panchangDate ?: Carbon::now();

    $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
    $ip = $request->ip();
    if ($ip === '127.0.0.1' || $ip === '::1' || !$ip) {
        $ip = '103.238.108.209';
    }
    
    $geoResponse = Http::get("http://ip-api.com/json/{$ip}");
    $geoData = $geoResponse->json();
    $latitude = $geoData['lat'] ?? 28.6139;
    $longitude = $geoData['lon'] ?? 77.2090;
    $timezone = $geoData['timezone'] ?? 'Asia/Kolkata';

    $date = date('d/m/Y');
    if ($request->panchangDate) {
        $date = date('d/m/Y', strtotime($request->panchangDate));
    }

    // FIX: Ensure HH:MM format
    $time = Carbon::now($timezone)->format('H:i');

    $Todayspanchang = Http::get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
        'date'    => $date,
        'time'    => $time,  // no urlencode
        'tz'      => $this->getTimezoneOffset($timezone),
        'lat'     => $latitude,
        'lon'     => $longitude,
        'api_key' => $api_key->value,
        'lang'    => 'en'
    ]);

    $getPanchang = $Todayspanchang->json();
// dd($getPanchang);
    return view('frontend.pages.panchang', [
        'getPanchang' => $getPanchang,
    ]);
}
    
     private function getTimezoneOffset($timezone)
    {
        $time = new \DateTime('now', new \DateTimeZone($timezone));
        return $time->getOffset() / 3600; // Convert seconds to hours
    }

    public function getkundali(Request $request)
    {
        Artisan::call('cache:clear');

        $session = new Session();
        $token = $session->get('token');


        $getkundaliprice = Http::withoutVerifying()->post(url('/') . '/api/pdf/price', [
            'token' => $token,
        ])->json();

        $getkundali = Http::withoutVerifying()->post(url('/') . '/api/getkundali', [
            'token' => $token,
        ])->json();

         $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag',[
            'token' => $token,
        ])->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
            // dd( $getkundaliprice);

        return view('frontend.pages.kundali', [
            'getkundali' => $getkundali,
            'getkundaliprice' => $getkundaliprice,
            'currency' => $currency,

        ]);
    }

    public function kundaliMatch(Request $request)
    {

        return view('frontend.pages.kundali-matching', [


        ]);
    }

    public function kundaliMatchReport(Request $request)
    {
        $KundaliMatching = Http::withoutVerifying()->post(url('/') . '/api/KundaliMatching/report', [
            'male_kundli_id' => $request->male_kundli_id,
            'female_kundli_id' => $request->female_kundli_id,
        ])->json();

        $kundalimale = Kundali::where('id', $request->male_kundli_id)->first();
        $kundalifemale = Kundali::where('id', $request->female_kundli_id)->first();
       

        // dd($kundalimale);
        // return $KundaliMatching;
        return view('frontend.pages.kundali-match-report', [
            'KundaliMatching' => $KundaliMatching,
            'kundalimale' => $kundalimale,
            'kundalifemale' => $kundalifemale,

        ]);
    }


    public function kundaliReport(Request $request)
    {
        // Initialize session
        $session = new Session();
 
        // Create unique session key based on request parameters
        $sessionKey = 'kundali_report_' . $request->kundali_id . '_' . ($request->lang ?? 'en');
        if ($session->has($sessionKey)) {
            $KundaliReport = $session->get($sessionKey);
        } else {
            // Make API call if not in session
            $KundaliReport = Http::withoutVerifying()->post(url('/') . '/api/kundali/getKundaliReport', [
                'kundali_id' => $request->kundali_id,
                'lang' => $request->lang
            ])->json();
    
            // Store in session for subsequent requests
            $session->set($sessionKey, $KundaliReport);
        }
    
        return view('frontend.pages.kundali-report', compact('KundaliReport'));
    }


}
