<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use App\Models\UserModel\Kundali;
use App\Models\KundaliPrice;
use App\Models\UserModel\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Session\Session;


class KundaliController extends Controller
{

    public function addKundali(Request $req)
    {
        DB::beginTransaction();

        try {
            // Get user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $company_name=SystemFlag::where('name','AppName')->first();
            $company_email=SystemFlag::where('name','siteemail')->first();
            $company_address=SystemFlag::where('name','siteaddress')->first();
            $company_number=SystemFlag::where('name','sitenumber')->first();
            $company_website=env('APP_URL');

            $data = $req->only('kundali', 'amount', 'is_match');

            // Validate the data
            $validator = Validator::make($data, [
                'kundali' => 'required|array',
                'kundali.*.name' => 'required',
                'kundali.*.birthDate' => 'required',
                'kundali.*.birthPlace' => 'required',
                'kundali.*.latitude' => 'required',
                'kundali.*.longitude' => 'required',
            ]);


            // Send a failed response if the request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $kundali2 = [];

            if($req->is_match=="false"){
                $req->is_match=0;
            }

            // Create or update Kundali
            foreach ($req->kundali as $kundali) {

                if($kundali['pdf_type']=='basic'){
                    $newKundali = Kundali::create([
                    'name' => $kundali['name'],
                    'gender' => $kundali['gender'],
                    'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                    'birthTime' => $kundali['birthTime'] ?? '12:00',
                    'birthPlace' => $kundali['birthPlace'],
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                    'latitude' => $kundali['latitude'],
                    'longitude' => $kundali['longitude'],
                    'timezone' => $kundali['timezone'],
                    'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                    'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                    'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                    'pdf_link' => isset($kundaliList) ? $kundaliList : '',
                ]);

                $kundali2[] = $newKundali;
                }else{
                     if (isset($kundali['id'])) {
                    $kundalis = Kundali::find($kundali['id']);

                    if ($kundalis) {
                        try {
                            $kundaliList = $this->getKundliViaVedic(
                                $kundali['lang'],
                                $kundali['name'],
                                $kundali['latitude'],
                                $kundali['longitude'],
                                $kundali['birthDate'],
                                $kundali['birthTime'],
                                $kundali['timezone'],
                                $kundali['birthPlace'],
                                $kundali['pdf_type'],
                                $match_type='north',
                                $company_name->value,
                                $company_address->value,
                                $company_website,
                                $company_email->value,
                                $company_number->value


                            );
                        } catch (\Exception $e) {
                            // Log the error or handle it accordingly
                            $kundaliList = null; // Proceed without the PDF link if there's an error
                        }

                        $kundalis->name = $kundali['name'];
                        $kundalis->gender = $kundali['gender'];
                        $kundalis->birthDate = date('Y-m-d', strtotime($kundali['birthDate']));
                        $kundalis->birthTime = $kundali['birthTime'];
                        $kundalis->birthPlace = $kundali['birthPlace'];
                        $kundalis->latitude = $kundali['latitude'];
                        $kundalis->longitude = $kundali['longitude'];
                        $kundalis->timezone = $kundali['timezone'];
                        $kundalis->pdf_type = isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '';
                        $kundalis->match_type = isset($kundali['match_type']) ? $kundali['match_type'] : '';
                        $kundalis->forMatch = isset($kundali['forMatch']) ? $kundali['forMatch'] : 0;
                        $kundalis->pdf_link = $kundaliList ?? ''; // Use the PDF link if available, else empty string
                        $kundalis->update();
                        $kundali2[] = $kundalis;
                    }
                } else {
                    // Handle wallet and kundali creation logic as before
                    $kundalicount = Kundali::where('createdBy', '=', $id)->count();
                    if (!$req->is_match && $kundalicount > 0) {
                        $wallet = DB::table('user_wallets')
                            ->where('userId', '=', $id)
                            ->first();

                        // $user_country=User::where('id',$id)->where('country','India')->first();  // commented by bhushan borse on 04 june 2025
                        $user_country=User::where('id',$id)->where('countryCode','+91')->first();  // added by bhushan borse on 04 june 2025
                        $inr_usd_conv_rate = DB::table('systemflag')->where('name','UsdtoInr')->select('value')->first();


                        $requiredAmount = $req->amount;


                        if ($wallet && $wallet->amount >= $requiredAmount) {
                            // if($user_country){
                            //     $requiredAmount=convertinrtousd($requiredAmount);
                            // }
                            $requiredAmount = $user_country ? $requiredAmount : convertusdtoinr($requiredAmount);

                            // $updatedAmount = $wallet->amount - ($user_country ? ($requiredAmount * $inr_usd_conv_rate->value) : $requiredAmount);
                            $updatedAmount = $wallet->amount - $requiredAmount;

                            DB::table('user_wallets')
                                ->where('userId', $id)
                                ->update(['amount' => $updatedAmount]);

                            try {
                                $kundaliList = $this->getKundliViaVedic(
                                    $kundali['lang'],
                                    $kundali['name'],
                                    $kundali['latitude'],
                                    $kundali['longitude'],
                                    $kundali['birthDate'],
                                    $kundali['birthTime'],
                                    $kundali['timezone'],
                                    $kundali['birthPlace'],
                                    $kundali['pdf_type'],
                                    $match_type='north',
                                    $company_name->value,
                                    $company_address->value,
                                    $company_website,
                                    $company_email->value,
                                    $company_number->value
                                );
                            } catch (\Exception $e) {
                                $kundaliList = null;
                            }

                            $newKundali = Kundali::create([
                                'name' => $kundali['name'],
                                'gender' => $kundali['gender'],
                                'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                                'birthTime' => $kundali['birthTime'],
                                'birthPlace' => $kundali['birthPlace'],
                                'createdBy' => $id,
                                'modifiedBy' => $id,
                                'latitude' => $kundali['latitude'],
                                'longitude' => $kundali['longitude'],
                                'timezone' => $kundali['timezone'],
                                'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                                'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                                'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                                'pdf_link' => $kundaliList ?? '',
                            ]);

                            $kundali2[] = $newKundali;

                            // Add wallet transaction entry
                            $transaction = [
                                'userId' => $id,
                                'amount' => $requiredAmount,
                                'isCredit' => false,
                                'transactionType' => 'KundliView',
                                'created_at' => now(),
                                'updated_at' => now(),
                                'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                            ];
                            if($requiredAmount > 0){
                               DB::table('wallettransaction')->insert($transaction);
                            }

                        } else {
                            // Insufficient funds in the wallet
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient funds in the wallet.',
                                'status' => 400,
                            ], 400);
                        }
                    } else {
                        if(!$req->is_match && $kundalicount == 0){
                            // dd($req->all());
                            try {
                                $kundaliList = $this->getKundliViaVedic(
                                    $kundali['lang'],
                                    $kundali['name'],
                                    $kundali['latitude'],
                                    $kundali['longitude'],
                                    $kundali['birthDate'],
                                    $kundali['birthTime'],
                                    $kundali['timezone'],
                                    $kundali['birthPlace'],
                                    $kundali['pdf_type'],
                                    $match_type='north',
                                    $company_name->value,
                                    $company_address->value,
                                    $company_website,
                                    $company_email->value,
                                    $company_number->value
                                );
                            } catch (\Exception $e) {
                                $kundaliList = null;
                            }

                            $newKundali = Kundali::create([
                                'name' => $kundali['name'],
                                'gender' => $kundali['gender'],
                                'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                                'birthTime' => $kundali['birthTime'],
                                'birthPlace' => $kundali['birthPlace'],
                                'createdBy' => $id,
                                'modifiedBy' => $id,
                                'latitude' => $kundali['latitude'],
                                'longitude' => $kundali['longitude'],
                                'timezone' => $kundali['timezone'],
                                'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                                'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                                'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                                'pdf_link' => $kundaliList ?? '',
                            ]);

                            $kundali2[] = $newKundali;
                        } else {
                            $newKundali = Kundali::create([
                                'name' => $kundali['name'],
                                'gender' => $kundali['gender'],
                                'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                                'birthTime' => $kundali['birthTime'],
                                'birthPlace' => $kundali['birthPlace'],
                                'createdBy' => $id,
                                'modifiedBy' => $id,
                                'latitude' => $kundali['latitude'],
                                'longitude' => $kundali['longitude'],
                                'timezone' => $kundali['timezone'],
                                'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                                'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                                'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                                'pdf_link' => isset($kundaliList) ? $kundaliList : '',
                            ]);

                            $kundali2[] = $newKundali;
                        }
                    }
                }
                }



            }

            DB::commit();
            return response()->json([
                'message' => 'Kundali updated successfully',
                'recordList' => $kundali2,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }



//  public function getPanchang(Request $req)
//     {
//         $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

//         // dd($api_key);

//         try {
//             $curl = curl_init();
//             $date = date('d/m/Y',strtotime($req->panchangDate));
//             curl_setopt_array($curl, array(
//             CURLOPT_URL => 'https://api.vedicastroapi.com/v3-json/panchang/panchang?api_key='.$api_key->value.'&date='.$date.'&tz=5.5&lat=11.2&lon=77.00&time=05%3A20&lang=en',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'GET',
//             ));
//             $response = curl_exec($curl);
//             curl_close($curl);
//             return response()->json([
//                 'recordList' => json_decode($response),
//                 'status' => 200,
//             ], 200);
//         } catch (\Exception$e) {
//             return response()->json([
//                 'error' => false,
//                 'message' => $e->getMessage(),
//                 'status' => 500,
//             ], 500);
//         }
//     }


public function getPanchang(Request $req)
{
    $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();

    $ip = $req->ip() ? $req->ip() : $req->ip;
    if ($ip === '127.0.0.1' || $ip === '::1' || !$ip) {
        $ip = '103.238.108.209'; 
    }

    $date = $req->panchangDate ? date('d/m/Y', strtotime($req->panchangDate)) : date('d/m/Y');
    $sessionKey = 'panchang_data_' . $ip . '_' . $date;

    if (session()->has($sessionKey)) {
        $sessionData = session($sessionKey);

        // Ensure the session data is properly structured
        if (is_array($sessionData) && isset($sessionData['expires_at']) && isset($sessionData['data'])) {
            if (time() > $sessionData['expires_at']) {
                session()->forget($sessionKey); 
            } else {
                return response()->json([
                    'recordList' => $sessionData['data'],
                    'status' => 200,
                ], 200);
            }
        } else {
            // If the session data is invalid, clear it and fetch new data
            session()->forget($sessionKey);
        }
    }

    // If the session does not exist or has expired, fetch new data from the API
    // try {
        $geoResponse = Http::get("http://ip-api.com/json/{$ip}");
        $geoData = $geoResponse->json();

        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
        $timezone = $geoData['timezone'];

        $time = now($timezone)->format('H:i');
        $lang = $req->lang;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.vedicastroapi.com/v3-json/panchang/panchang?api_key=' . $api_key->value . '&date=' . $date . '&tz=' . $this->getTimezoneOffset($timezone) . '&lat=' . $latitude . '&lon=' . $longitude . '&time=' . $time . '&lang='.$lang,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        $panchangData = json_decode($response);
        //dd($panchangData);

        // Store the panchang data in the session with a 24-hour expiry
        session([$sessionKey => [
            'data' => $panchangData,
            'expires_at' => time() + (24 * 60 * 60), 
        ]]);

        return response()->json([
            'recordList' => $panchangData,
            'status' => 200,
        ], 200);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'error' => false,
    //         'message' => $e->getMessage(),
    //         'status' => 500,
    //     ], 500);
    // }
}

     private function getTimezoneOffset($timezone)
    {
        $time = new \DateTime('now', new \DateTimeZone($timezone));
        return $time->getOffset() / 3600; // Convert seconds to hours
    }



public function getKundaliPrice(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $kundali = Kundali::where('createdBy', '=', $id)->count();

            $kundali_price=KundaliPrice::all();

            return response()->json([
                'recordList' => $kundali_price,
                'isFreeSession' => $kundali > 0 ? false : true,
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



  //Dynamic part
    public function getKundliViaVedic($lang = 'en',$name, $lat, $long, $dob, $tob, $timezone, $pob, $pdfType = 'small', $match_type = 'north',$company_name,$company_address,$company_website,$company_email,$company_number)
    {
    $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

    $formattedBirthDate = date('d/m/Y', strtotime($dob));
    $apiUrl = 'https://api.vedicastroapi.com/v3-json/pdf/horoscope-queue?';

    $queryParams = http_build_query([
        'name' => $name,
        'dob' => $formattedBirthDate,
        'tob' => $tob,
        'lat' => $lat,
        'lon' => $long,
        'tz' => $timezone,
        'pob' => $pob,
        'api_key' => $api_key->value,
        'lang' => $lang,
        'style' => $match_type,
        'color' => '140',
        'pdf_type' => $pdfType,
        'company_name' => $company_name,
        'address' => $company_address,
        'website' => $company_website,
        'email' => $company_email,
        'phone' => $company_number,
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

    // Check if the request was successful
    // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // if ($httpCode == 200) {
    //     $response = json_decode($response);

    //     $timestamp = now()->timestamp;
    //     $path = 'kundli/' . $name . '_kundali_' . $timestamp . '.pdf';

    //     // Save the PDF to a local file
    //     $pdfPath = public_path($path);

    //     $content = file_get_contents($response->response);
    //     file_put_contents($pdfPath, $content);

    //     // Close the cURL session
    //     curl_close($curl);

    //     // Return the local path to the saved PDF
    //     return $path;
    // } else {
    //     // Handle error (e.g., log or return an error message)
    //     curl_close($curl);
    //     return false;
    // }

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode == 200) {
        $response = json_decode($response);
        // Return the PDF URL directly from the API response
        return $response->response;
    } else {
        // Handle error (e.g., log or return an error message)
        return false;
    }
    }

    //Get kundali
    public function getKundalis(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $kundali = Kundali::query();
            $kundali->where('createdBy', '=', $id)->where('forMatch',0)->orderByDesc('created_at');
            $kundaliCount = Kundali::query();
            $kundaliCount->where('createdBy', '=', $id)
                ->where('forMatch',0)->count();
            if ($s = $req->input(key:'s')) {
                $kundali->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }

            return response()->json([
                'recordList' => $kundali->get(),
                'status' => 200,
                'totalRecords' => $id,
				'kundliList' => json_decode('{"status":200,"response":"https://s3.ap-south-1.amazonaws.com/vapi.public.pdf/Tue%20Jan%2009%202024/hor_Karan%20Test-03011996-0904-1704796707150.pdf?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAVSWEL6DIXT6LAQVK%2F20240109%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20240109T103827Z&X-Amz-Expires=21600&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEFMaCmFwLXNvdXRoLTEiSDBGAiEAriZ4vzJ0CAA2H0N29ZMW1neGqFa1IdcDyOCHnPh5%2FE8CIQDJja9Kos0jIqMPoJs5WUmimBnymLdPx4zmpsjPp5BdSCr4Agjs%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8BEAQaDDM4MzczNzY1NTUwNSIMWoYe92QqOOym96aCKswCGx7RjJjuAPolXNSBpB2XNNTFQESlMDoA7R4uQHhiLNMbk7BllB9j3Gz5ajQAPnIoiyyDEhaN9XFVLayAeU%2F8i%2Bk8LLrwwrv16NZ%2F4DR%2BTjkfrViKbKyNUXaJpRMT4t8iWP5%2FKEdkpVNfAjCoVvXFX3Nq1nE%2BBI2jf2AIPjgfXRjinYLuPVsErK2mMxk0V2C8wl5%2BPAkPlSsKuTbo1vvnGNd6Ny0mKsnA8U642CJUvaxKGIDSHAiNn7jYTcLsN9Un%2FOtQntNRNmGrRbEa3SJvVZLIgVqpTsOusvRLNIOCVpE5wQX3JpoOPWYr302nA%2FQ0zj4j9%2F4hmxzMJWDbZVlzNOIwxNdRlCbh%2FtcOAi9Sg00SPLxUFB1FzPz9hHphfVIoZwWy5vEJ1fVXx%2BpCwaCNom%2Bltyccr%2FL915Yrto8oHhoKl3YeFaqJNlvEWx0wiML0rAY6nQEB0myq%2B%2FG9KzhzoGh9t9NGpbr8bfzgcj273Ru6sn8CzATeYOIKSK8Lusd9KVv7s2VvwRMmlcenuRSOIJEMObOxPUqaO2hG9SjnpCbu8DMShd%2BUoHo505%2BEm9K520gEA5cvhVieGHwlFxk4BbSN4bh8A2b7F4j17G9Stp1q6XrMGmLcY3RVmMYdRfjQ2u%2BQu2hr%2FiSu9olOUXtLyDg0&X-Amz-Signature=d50e3e354b5c1cfab0953c1eaf088a750b804af4d03185e63060e9a169b6cddc&X-Amz-SignedHeaders=host"}'),//$kundaliList,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


	// 	public function getKundali(Request $req, $id)
    // {
    //     try {
    //         $kundali = Kundali::where('id', $id)->first();
    //         $dob = date('d/m/Y', strtotime($kundali->birthDate));
    //         return response()->json([
    //             'message' => 'Kundali update sucessfully',
    //             'recordList' => json_decode('{"status":200,"response":"https://astroway.diploy.in/public/hor_Karan%20Test-03011996-0904-1704796707150.pdf"}'),
    //             'status' => 200,
    //         ], 200);
    //     } catch (\Exception$e) {
    //         return response()->json([
    //             'error' => false,
    //             'message' => $e->getMessage(),
    //             'status' => 500,
    //         ], 500);
    //     }
    // }

	//dynamic part
		public function getKundali(Request $req, $id)
    {
        try {
            $kundali = Kundali::where('id', $id)->first();
            $dob = date('d/m/Y', strtotime($kundali->birthDate));
            return response()->json([
                'message' => 'Kundali update sucessfully',
                'recordList' => ['status'=>200,'response'=>url('public/'.$kundali->pdf_link)],
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




    //Update kundali
    public function updateKundali(Request $req, $id)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $req->validate = ([
                'name',
                'gender',
                'birthDate',
                'birthTime',
                'birthPlace',
            ]);

            $kundali = Kundali::find($id);
            if ($kundali) {
                $kundali->name = $req->name;
                $kundali->gender = $req->gender;
                $kundali->birthDate = $req->birthDate;
                $kundali->birthTime = $req->birthTime;
                $kundali->birthPlace = $req->birthPlace;
                $kundali->latitude = $req->latitude;
                $kundali->longitude = $req->longitude;
                $kundali->timezone = $req->timezone;
                $kundali->update();
                return response()->json([
                    'message' => 'Kundali update sucessfully',
                    'recordList' => $kundali,
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Delete kundali
    public function deleteKundali(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $kundali = Kundali::find($req->id);
            if ($kundali) {
                $kundali->delete();
                return response()->json([
                    'message' => 'Kundali delete Sucessfully',
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show single kundali
    public function kundaliShow($id)
    {
        try {
            $kundali = Kundali::find($id);
            if ($kundali) {
                return response()->json([
                    'recordList' => $kundali,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Kundali is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function removeFromTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = array(
                'isForTrackPlanet' => false,
            );
            DB::table('kundalis')->where('createdBy', '=', $id)->where('isForTrackPlanet', '=', true)->update($data);
            return response()->json([
                'message' => "Remove Kundali Successfully",
                'status' => 200,
                "id" => $id,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addForTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = array(
                'isForTrackPlanet' => true,
            );
            DB::table('kundalis')->where('id', '=', $req->id)->update($data);
            return response()->json([
                'message' => "Kundali Add Successfully",
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

    public function getForTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $trackPlanetKundali = DB::table('kundalis')->where('createdBy', '=', $id)->where('isForTrackPlanet', '=', true)->get();

            return response()->json([
                'recordList' => $trackPlanetKundali,
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





    // New One
      // Kundali New Section Apis


      public function getBasicKundali(Request $request)
      {
          // Initialize session
          $session = new Session();
      
          // Create unique session key
          $sessionKey = 'basic_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en');
      
          // Check if data exists in session
          if ($session->has($sessionKey)) {
            // dd($sessionKey);
              return response()->json($session->get($sessionKey), 200);
          }
      
          // Fetch kundali data
          $kundali = Kundali::where('id', $request->id)->first();
          
          if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();
             //dd($intakeForm);
            if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                $kundali = (object) [
                    'name' => $intakeForm->name,
                    'gender' => $intakeForm->gender,
                    'birthDate' => $intakeForm->birthDate,
                    'birthTime' => $intakeForm->birthTime,
                    'latitude' => $intakeForm->latitude,
                    'longitude' => $intakeForm->longitude,
                    'birthPlace' => $intakeForm->birthPlace,
                    'timezone' => $intakeForm->timezone,
                ];
            }
          }
          
          //dd($kundali);
          
      
          // Get API key
          $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
      
          // Make API call for planet details
          $planet = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-details', [
              'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
              'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
              'tz' => $kundali->timezone,
              'lat' => $kundali->latitude,
              'lon' => $kundali->longitude,
              'api_key' => $api_key->value,
              'lang' => $request->language,
          ]);
      
          // Prepare response
          $responseData = [
              'message' => 'Kundali Report Fetched Successfully',
              'recordList' => $kundali,
              'planetDetails' => $planet->json(),
              'status' => 200,
          ];
      
          // Store in session before returning
          $session->set($sessionKey, $responseData);
      
          return response()->json($responseData, 200);
      }

    // Get Chart
    public function getChartKundali(Request $request)
    {
        // Initialize session
        $session = new Session();
    
        // Create unique session key
        $sessionKey = 'chart_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en') . '_' . ($request->div ?? 'D1') . '_' . ($request->style ?? 'north');
    
        // Check if data exists in session
        if ($session->has($sessionKey)) {
            
            return response()->json($session->get($sessionKey), 200);
        }
    
        // Fetch kundali data
        $kundali = Kundali::where('id', $request->id)->first();

        if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();
    
            if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                $kundali = (object) [
                    'birthDate' => $intakeForm->birthDate,
                    'birthTime' => $intakeForm->birthTime,
                    'latitude' => $intakeForm->latitude,
                    'longitude' => $intakeForm->longitude,
                    'timezone' => $intakeForm->timezone,
                ];
            }
        }
       
    
        // Get API key
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
    
        // Make API call for chart image
        $chart = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/chart-image', [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
            'div' => $request->div,
            // 'font_size' => 14,
            'font_size' => 24,
            'font_style' => 'roboto',
            'style' => $request->style,
            'color' => '#F0D08D',
            'stroke' => 2,
            'colorful_planets' => 1
        ]);
    
        // Prepare response
        $responseData = [
            'message' => 'Kundali Report Fetched Successfully',
            'chartDetails' => $chart->body(),
            'status' => 200,
        ];
    
        // Store in session
        $session->set($sessionKey, $responseData);
    
        return response()->json($responseData, 200);
    }
    

    // Get Ashtak Varga And Binastakvarga
    public function getAstakvargaKundali(Request $request)
    {
        // Initialize session
        $session = new Session();
    
        // Create unique session key
        $sessionKey = 'astakvarga_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en');
    
        // Check if data exists in session
        if ($session->has($sessionKey)) {
            return response()->json($session->get($sessionKey), 200);
        }
    
        // Fetch kundali data
        $kundali = Kundali::where('id', $request->id)->first();

        if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();
    
            if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                $kundali = (object) [
                    'birthDate' => $intakeForm->birthDate,
                    'birthTime' => $intakeForm->birthTime,
                    'latitude' => $intakeForm->latitude,
                    'longitude' => $intakeForm->longitude,
                    'timezone' => $intakeForm->timezone,
                ];
            }
        }
       
    
        // Get API key
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
    
        // API call for Ashtakvarga
        $ashtakvarga = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/ashtakvarga', [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
        ]);
    
        // API call for Binnashtakvarga
        $binnashtakvarga = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/binnashtakvarga', [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
            'planet' => 'Sun',
        ]);
    
        // Prepare response
        $responseData = [
            'message' => 'Kundali Report Fetched Successfully',
            'ashtakvarga' => $ashtakvarga->json(),
            'binnashtakvarga' => $binnashtakvarga->json(),
            'status' => 200,
        ];
    
        // Store in session
        $session->set($sessionKey, $responseData);
    
        return response()->json($responseData, 200);
    }
    

    // Ascendenat Report
    public function getAscendantKundali(Request $request)
    {
        // Initialize session
        $session = new Session();
    
        // Create unique session key
        $sessionKey = 'ascendant_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en');
    
        // Check if data exists in session
        if ($session->has($sessionKey)) {
            return response()->json($session->get($sessionKey), 200);
        }
    
        // Fetch kundali data
        $kundali = Kundali::where('id', $request->id)->first();
        
        if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();
    
            if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                $kundali = (object) [
                    'birthDate' => $intakeForm->birthDate,
                    'birthTime' => $intakeForm->birthTime,
                    'latitude' => $intakeForm->latitude,
                    'longitude' => $intakeForm->longitude,
                    'timezone' => $intakeForm->timezone,
                ];
            }
        }
        
    
        // Get API key
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
    
        // API call for Ascendant report
        $ascendant = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/ascendant-report', [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
        ]);
    
        // Prepare response
        $responseData = [
            'message' => 'Kundali Report Fetched Successfully',
            'ascendant' => $ascendant->json(),
            'status' => 200,
        ];
    
        // Store in session
        $session->set($sessionKey, $responseData);
    
        return response()->json($responseData, 200);
    }
    

    // Planet Report
    public function getPlanetKundali(Request $request)
    {
        // Initialize session
        $session = new Session();

        // Create unique session key (include planet for uniqueness)
        $sessionKey = 'planet_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en') . '_' . strtolower($request->planet ?? 'all');

        // Check if data exists in session
        if ($session->has($sessionKey)) {
            return response()->json($session->get($sessionKey), 200);
        }

        // Fetch kundali data
        $kundali = Kundali::where('id', $request->id)->first();

        if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();

        if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
            $kundali = (object) [
                'birthDate' => $intakeForm->birthDate,
                'birthTime' => $intakeForm->birthTime,
                'latitude' => $intakeForm->latitude,
                'longitude' => $intakeForm->longitude,
                'timezone' => $intakeForm->timezone,
            ];
        }
        }
        

        // Get API key
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();

        // API call for Planet Report
        $planet_report = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-report', [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
            'planet' => $request->planet,
        ]);

        // Prepare response
        $responseData = [
            'message' => 'Kundali Report Fetched Successfully',
            'planetReport' => $planet_report->json(),
            'status' => 200,
        ];

        // Store in session
        $session->set($sessionKey, $responseData);

        return response()->json($responseData, 200);
    }



    // Dasha
    public function getDashaKundali(Request $request)
        {
            $session = new Session();

            $sessionKey = 'dasha_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en');

            // Check if cached in session
            if ($session->has($sessionKey)) {
                
                return response()->json($session->get($sessionKey), 200);
            }
            
            // Fetch kundali data
            $kundali = Kundali::where('id', $request->id)->first();

            if($request->userId){
                $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();

                if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                    $kundali = (object) [
                        'birthDate' => $intakeForm->birthDate,
                        'birthTime' => $intakeForm->birthTime,
                        'latitude' => $intakeForm->latitude,
                        'longitude' => $intakeForm->longitude,
                        'timezone' => $intakeForm->timezone,
                    ];
                }
            }
           

            $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();

            $commonParams = [
                'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
                'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
                'tz' => $kundali->timezone,
                'lat' => $kundali->latitude,
                'lon' => $kundali->longitude,
                'api_key' => $api_key->value,
                'lang' => $request->language,
            ];

            $maha_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha', $commonParams);
            $maha_dasha_predictions = Http::get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha-predictions', $commonParams);
            $antar_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/antar-dasha', $commonParams);
            $char_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-current', $commonParams);
            $char_dasha_main = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-main', $commonParams);
            $yogini_dasha_main = Http::get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-main', $commonParams);
            $paryantar_dasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/paryantar-dasha', $commonParams);

            $responseData = [
                'message' => 'Kundali Report Fetched Successfully',
                'mahaDasha' => $maha_dasha->json(),
                'mahaDashaPrediction' => $maha_dasha_predictions->json(),
                'antarDasha' => $antar_dasha->json(),
                'charDashaCurrent' => $char_dasha->json(),
                'charDashaMain' => $char_dasha_main->json(),
                'yoginiDashaMain' => $yogini_dasha_main->json(),
                'paryantarDasha' => $paryantar_dasha->json(),
                'status' => 200,
            ];

            $session->set($sessionKey, $responseData);

            return response()->json($responseData, 200);
        }


    // Dosha
    public function getDoshaKundali(Request $request)
    {
        $session = new Session();
    
        $sessionKey = 'dosha_kundali_' . $request->id . '_' . ($request->userId ?? '0') . '_' . ($request->language ?? 'en');
    
        // Return cached data if available
        if ($session->has($sessionKey)) {
            return response()->json($session->get($sessionKey), 200);
        }
    
        $kundali = Kundali::where('id', $request->id)->first();

        if($request->userId){
            $intakeForm = DB::table('intakeform')->where('userId', $request->userId)->latest()->first();
    
            if ($intakeForm && $intakeForm->latitude && $intakeForm->longitude && $intakeForm->timezone) {
                $kundali = (object)[
                    'birthDate' => $intakeForm->birthDate,
                    'birthTime' => $intakeForm->birthTime,
                    'latitude' => $intakeForm->latitude,
                    'longitude' => $intakeForm->longitude,
                    'timezone' => $intakeForm->timezone,
                ];
            }
        }
        
    
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();
    
        $params = [
            'dob' => date('d/m/Y', strtotime($kundali->birthDate)),
            'tob' => Carbon::parse($kundali->birthTime)->format('H:i'),
            'tz' => $kundali->timezone,
            'lat' => $kundali->latitude,
            'lon' => $kundali->longitude,
            'api_key' => $api_key->value,
            'lang' => $request->language,
        ];
    
        $mangal_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/mangal-dosh', $params);
        $kaalsarp_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/kaalsarp-dosh', $params);
        $manglik_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', $params);
        $pitra_dosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/pitra-dosh', $params);
        $papasamaya = Http::get('https://api.vedicastroapi.com/v3-json/dosha/papasamaya', $params);
    
        $responseData = [
            'message' => 'Kundali Report Fetched Successfully',
            'mangalDosh' => $mangal_dosh->json(),
            'kaalsarpDosh' => $kaalsarp_dosh->json(),
            'manglikDosh' => $manglik_dosh->json(),
            'pitraDosh' => $pitra_dosh->json(),
            'papasamayaDosh' => $papasamaya->json(),
            'status' => 200,
        ];
    
        $session->set($sessionKey, $responseData);
    
        return response()->json($responseData, 200);
    }
    


}
