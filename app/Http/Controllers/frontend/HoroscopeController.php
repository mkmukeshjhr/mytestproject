<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Horoscope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HoroscopeController extends Controller
{

    public function horoScope(Request $request)
    {
        Artisan::call('cache:clear');
        $gethoroscopesign = Http::withoutVerifying()->post(url('/') . '/api/getHororscopeSign')->json();


        return view('frontend.pages.horoscopesign', [
            'gethoroscopesign' => $gethoroscopesign,

        ]);
    }

    public function dailyHoroscope(Request $request,$slug)
    {

        $horoscopeSignId=DB::table('hororscope_signs')->where('slug',$slug)->first();

        Artisan::call('cache:clear');
        $gethoroscopesign = Http::withoutVerifying()->post(url('/') . '/api/getHororscopeSign')->json();

        $horoscope = Http::withoutVerifying()->post(url('/') . '/api/getDailyHoroscope', [
            'horoscopeSignId' => $horoscopeSignId->id,
        ])->json();


        $signRcd = DB::table('hororscope_signs')->where('id', $horoscopeSignId->id)->get();


        return view('frontend.pages.dailyhoroscope', [
            'horoscope' => $horoscope,
            'gethoroscopesign' => $gethoroscopesign,
            'signRcd' => $signRcd,

        ]);
    }
}
