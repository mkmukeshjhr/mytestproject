<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\KundaliMatching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel\Kundali;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class KundaliMatchingController extends Controller
{
    //Add a kundali boy and girls
    public function addKundaliMatching(Request $req)
    {
        try {
            //Get a id of user
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'boyName',
                'boyBirthDate',
                'boyBirthTime',
                'boyBirthPlace',
                'girlName',
                'girlBirthDate',
                'girlBirthTime',
                'girlBirthPlace',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'boyName' => 'required',
                'boyBirthDate' => 'required',
                'boyBirthTime' => 'required',
                'boyBirthPlace' => 'required',
                'girlName' => 'required',
                'girlBirthDate' => 'required',
                'girlBirthTime' => 'required',
                'girlBirthPlace' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create kundali
            $kundaliMatching = KundaliMatching::create([
                'boyName' => $req->boyName,
                'boyBirthDate' => $req->boyBirthDate,
                'boyBirthTime' => $req->boyBirthTime  ?? '12:00',
                'boyBirthPlace' => $req->boyBirthPlace,
                'girlName' => $req->girlName,
                'girlBirthDate' => $req->girlBirthDate,
                'girlBirthTime' => $req->girlBirthTime ?? '12:00',
                'girlBirthPlace' => $req->girlBirthPlace,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'Boys and girls details add sucessfully',
                'recordList' => $kundaliMatching,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getMatchReport(Request $req)
    {
        try {


            $data = $req->only(
                'male_kundli_id',
                'female_kundli_id'
            );

            $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

            $maleKundliId = $req->male_kundli_id;
            $femaleKundliId = $req->female_kundli_id;
            $maleRcd = Kundali::where('id', $maleKundliId)->first();
            $femaleRcd = Kundali::where('id', $femaleKundliId)->first();
            $girlMangalikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                'tob' => date('h:i',strtotime($maleRcd->birthTime)),
                'tz' => $maleRcd->timezone,
                'lat' => $maleRcd->latitude,
                'lon' => $maleRcd->longitude,
                'api_key' => $api_key->value,
                'lang' => 'en'
            ]);
            $boyManaglikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                'tob' => date('h:i',strtotime($femaleRcd->birthTime)),
                'tz' => $femaleRcd->timezone,
                'lat' => $femaleRcd->latitude,
                'lon' => $femaleRcd->longitude,
                'api_key' => $api_key->value,
                'lang' => 'en'
            ]);
            if(strtolower($femaleRcd->match_type) == strtolower('North')){
                //
                $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/matching/ashtakoot', [
                    'boy_dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                    'boy_tob' => date('h:i',strtotime($maleRcd->birthTime)),
                    'boy_tz' => $maleRcd->timezone,
                    'boy_lat' => $maleRcd->latitude,
                    'boy_lon' => $maleRcd->longitude,
                    'girl_dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                    'girl_tob' => date('h:i',strtotime($femaleRcd->birthTime)),
                    'girl_tz' => $femaleRcd->timezone,
                    'girl_lat' => $femaleRcd->latitude,
                    'girl_lon' => $femaleRcd->longitude,
                    'api_key' => $api_key->value,
                    'lang' => 'en'
                ]);
            }else{
                //
                $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/matching/dashakoot', [
                    'boy_dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                    'boy_tob' => date('h:i',strtotime($maleRcd->birthTime)),
                    'boy_tz' => $maleRcd->timezone,
                    'boy_lat' => $maleRcd->latitude,
                    'boy_lon' => $maleRcd->longitude,
                    'girl_dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                    'girl_tob' => date('h:i',strtotime($femaleRcd->birthTime)),
                    'girl_tz' => $femaleRcd->timezone,
                    'girl_lat' => $femaleRcd->latitude,
                    'girl_lon' => $femaleRcd->longitude,
                    'api_key' => $api_key->value,
                    'lang' => 'en'
                ]);
            }
            $data = $dailyHorscope->json();

            return response()->json([
                'message' => 'Boys and girls matching details fetched sucessfully',
                'recordList' => $data,
                'girlMangalikRpt' => $girlMangalikRpt->json(),
                'boyManaglikRpt' => $boyManaglikRpt->json(),
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    // Get Kundali Report
   public function getKundaliReport(Request $req)
{
    try {
        // Fetching input data
        $data = $req->only(['kundali_id','lang','userId']);


        // Fetching API key from the database
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();

        // Fetching the user's kundali details
        $maleRcd = Kundali::where('id', $req->kundali_id)->first();

        $intakeForm=DB::table('intakeform')->where('userId',$req->userId)->latest()->first();

        if($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone){

            $maleRcd = (object) [
                'name' => $intakeForm->name,
                'gender' => $intakeForm->gender,
                'birthDate' => $intakeForm->birthDate,
                'birthTime' => date('H:i',strtotime($intakeForm->birthTime)),
                'latitude' => $intakeForm->latitude,
                'longitude' => $intakeForm->longitude,
                'birthPlace' => $intakeForm->birthPlace,
                'timezone' => $intakeForm->timezone,
            ];

        }

        // List of divisional charts to retrieve
        $divs = [
        'D1', 'D2', 'D3', 'D4', 'D5', 'D7', 'D8', 'D9', 'D10',
        'D12', 'D16', 'D20', 'D24', 'D27', 'D40', 'D45', 'D60', 'D30',
        'chalit', 'sun', 'moon', 'kp_chalit'
    ];
     $apiUrl = 'https://api.vedicastroapi.com/v3-json/horoscope/chart-image?';

    $results = [];

    foreach ($divs as $div) {
        // Build query parameters for each request
        $queryParams = http_build_query([
            'name' => $maleRcd->name,
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => date('H:i',strtotime($maleRcd->birthTime)),
            'lat' => $maleRcd->latitude,
            'lon' =>$maleRcd->longitude,
            'tz' => $maleRcd->timezone,
            'div' => $div,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
            'font_size' => 14,
            'font_style' => 'roboto',
            'style' => 'north',
            'color' => '#F0D08D',
            'size'=>'300',
            'stroke'=>2,
            'colorful_planets'=>1
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl . $queryParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            // Handle error as needed, log or return error
            return "Error fetching chart for div $div: $error_msg";
        }

        curl_close($curl);

        // Extract SVG content from the response
        if (preg_match('/<svg.*?<\/svg>/s', $response, $matches)) {
            $results[$div] = $matches[0];
        } else {
            $results[$div] = "No SVG found for div $div";
        }
    }

        // Fetch other horoscope details
        $personal = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/personal-characteristics', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => date('H:i',strtotime($maleRcd->birthTime)),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en"
        ]);

        $ascendant = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/ascendant-report', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => date('H:i',strtotime($maleRcd->birthTime)),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en"
        ]);

        $ashtakvarga = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/ashtakvarga', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => date('H:i',strtotime($maleRcd->birthTime)),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en"
        ]);

         $binnashtakvarga = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/binnashtakvarga', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
            'planet'=>"Sun",
        ]);

        $planet = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-details', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en"
        ]);


        //
         $maha_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);
        $maha_dasha_predictions = Http::get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha-predictions', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);
        $antar_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/antar-dasha', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);
        $char_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-current', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);
        $char_dasha_main = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-main', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);

        $yogini_dasha_main = Http::get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-main', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);
            $paryantar_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/paryantar-dasha', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",
        ]);


         $mangal_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/mangal-dosh', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",

        ]);

         $kaalsarp_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/kaalsarp-dosh', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",

        ]);

         $manglik_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",

        ]);

         $pitra_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/pitra-dosh', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",

        ]);

         $papasamaya = Http::get('https://api.vedicastroapi.com/v3-json/dosha/papasamaya', [
            'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
            'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
            'tz' => $maleRcd->timezone,
            'lat' => $maleRcd->latitude,
            'lon' => $maleRcd->longitude,
            'api_key' => $api_key->value,
            'lang' => $req->lang ?? "en",

        ]);


        $planetss = ['Sun', 'Moon', 'Mercury', 'Venus', 'Mars', 'Saturn', 'Jupiter', 'Rahu', 'Ketu'];
        $planet_reports = [];

        foreach ($planetss as $planets) {
            $response = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-report', [
                'dob' => date('d/m/Y', strtotime($maleRcd->birthDate)),
                'tob' => Carbon::parse($maleRcd->birthTime)->format('H:i'),
                'tz' => $maleRcd->timezone,
                'lat' => $maleRcd->latitude,
                'lon' => $maleRcd->longitude,
                'api_key' => $api_key->value,
                'lang' => $req->language,
                'planet' => $planets
            ]);

            $planet_reports[$planets] = $response->json(); // Store the response data keyed by planet name
        }




        return response()->json([
            'message' => 'Kundali Report Fetched Successfully',
            'recordList' => $maleRcd,
            'personal' => $personal->json(),

            'ashtakvarga' => $ashtakvarga->json(),
            'binnashtakvarga' => $binnashtakvarga->json(),
            'planet' => $planet->json(),
            'charts' => $results,
            'mahaDasha' => $maha_dasha->json(),
            'mahaDashaPrediction' => $maha_dasha_predictions->json(),
            'antarDasha' => $antar_dasha->json(),
            'charDashaCurrent' => $char_dasha->json(),
            'charDashaMain' => $char_dasha_main->json(),
            'yoginiDashaMain' => $yogini_dasha_main->json(),
            'paryantarDasha' => $paryantar_dasha->json(),
             'mangalDosh' => $mangal_dosh->json(),
            'kaalsarpDosh' => $kaalsarp_dosh->json(),
            'manglikDosh' => $manglik_dosh->json(),
            'pitraDosh' => $pitra_dosh->json(),
            'papasamayaDosh' => $papasamaya->json(),
            'ascendantReport' => $ascendant->json(),
            'planetReport'=>$planet_reports,
            'status' => 200,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}

}
