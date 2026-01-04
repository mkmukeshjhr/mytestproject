<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\AstrologerModel\AstrologerStory;
use Carbon\Carbon;
use App\Models\UserModel\AstromallProduct;
use App\Models\AstrologerModel\Astrologer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\AdminModel\SystemFlag;
use App\Models\AdminModel\Banner;
use App\Models\AstrologerModel\AstrologyVideo;
use App\Models\AstrologerModel\Blog;
use App\Models\UserModel\AstrotalkInNews;
use App\Models\UserModel\ProductCategory;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\Horosign;
use App\Models\WebHomeFaq;
use App\Models\UserModel\UserDeviceDetail;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\UserModel\UserWallet;
use App\Models\PujaCategory;

use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        //    $session = new Session();
        //      $token = $session->get('token');
        //     JWTAuth::setToken($token);
        //             $user = JWTAuth::authenticate();
        //             // $user->load('wallet');
        //             //  $user->totalWalletAmount = $user->wallet ? $user->wallet->amount : 0;
        //         dd($user);
        $userId = '';
        if (authcheck()) {
            $userId = authcheck()['id'];
        }

        $session = new Session();
        $token = $session->get('token');

        Artisan::call('cache:clear');
        $session = new Session();
        $token = $session->get('token');

        $banner = Banner::query()->join('banner_types', 'banner_types.id', 'banners.bannerTypeId')->where('banners.isActive', '=', '1')->whereDate('fromDate', '<=', Carbon::today())->whereDate('ToDate', '>=', Carbon::today())
            ->limit(10)->select('banners.*', 'banner_types.name as bannerType')->orderBy('banners.id', 'DESC')->get();
        $blogs = Blog::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
        $productCategory = ProductCategory::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
        $astrotalkInNews = AstrotalkInNews::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
        $astrologyVideo = AstrologyVideo::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();


        $liveAstrologer = DB::table('liveastro')->join('astrologers', 'astrologers.id', '=', 'liveastro.astrologerId')->join('skills', function ($join) {
            $join->whereRaw('FIND_IN_SET(skills.id, astrologers.primarySkill)');
        })->where('liveastro.isActive', true)->select(
            'astrologers.name',
            'astrologers.profileImage',
            'liveastro.*',
            'astrologers.charge',
            'astrologers.videoCallRate',
            DB::raw('GROUP_CONCAT(skills.name) as skill_names')
        )->groupBy(
            'liveastro.id',
            'astrologers.name',
            'astrologers.profileImage',
            'astrologers.charge',
            'astrologers.videoCallRate'
        )->orderBy('liveastro.id', 'DESC')->get();


        $horosign = Horosign::query()->where('isActive', 1)->get();

        $boostCutoff = Carbon::now()->subHours(24);
        $astrologerQuery = Astrologer::query()
            ->select('astrologers.*', DB::raw('
              (CASE WHEN abp.astrologer_id IS NOT NULL THEN 1 ELSE 0 END) as is_boosted
          '))
            ->leftJoin('astrologer_boosted_profiles as abp', function ($join) use ($boostCutoff) {
                $join->on('astrologers.id', '=', 'abp.astrologer_id')
                    ->where('abp.boosted_datetime', '>=', $boostCutoff);
            })
            ->where([
                ['astrologers.isActive', true],
                ['astrologers.isVerified', true],
                ['astrologers.isDelete', false]
            ]);

        // Search functionality
        if ($s = $request->input('s')) {
            $astrologerQuery->where('name', 'LIKE', "%{$s}%");
        }

        // Apply sorting by chatStatus, callStatus, boost status, and random order
        $astrologerQuery->orderByRaw('
          CASE
              WHEN astrologers.chatStatus = "online" OR astrologers.callStatus = "online" THEN 1
              ELSE 2
          END
      ')->orderByRaw('
          CASE
              WHEN abp.astrologer_id IS NOT NULL THEN 1
              ELSE 2
          END
      ')->inRandomOrder();

        // Fetch the results
        $astrologer = $astrologerQuery->take(50)->get();
        

        $astr = [];
        foreach ($astrologer as $astro) {
            $review = DB::table('user_reviews')
                ->where('astrologerId', '=', $astro['id'])
                ->get();

            $astro['rating'] = 0;
            if ($review && count($review) > 0) {
                $avgRating = 0;
                foreach ($review as $re) {
                    $avgRating += $re->rating;
                }
                $avgRating = $avgRating / count($review);
                $astro['rating'] = $avgRating;
            }

            array_push($astr, $astro);
            $astro['reviews'] = $review ? count($review) : 0;
        }

        $astrologer = $astr;


        $astromallProduct = AstromallProduct::orderBy('id', 'DESC')->get();
        $getsystemflag = SystemFlag::all();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        $freekundali = $getsystemflag->where('name', 'FreeKundali')->first();
        $kundali_matching = $getsystemflag->where('name', 'KundaliMatching')->first();
        $panchang = $getsystemflag->where('name', 'TodayPanchang')->first();
        // $blog = $getsystemflag->where('name', 'Blog')->first();
        $blog = Blog::query()->where('isActive', 1)->orderBy('created_at', 'desc')->take(3)->get();
        $shop = $getsystemflag->where('name', 'Astromall')->first();
        $daily_horoscope = $getsystemflag->where('name', 'DailyHoroscope')->first();
        $puja = $getsystemflag->where('name', 'Puja')->first();

        $webfaqs = WebHomeFaq::query()->get();
        // Stories
        $twentyFourHoursAgo = Carbon::now()->subHours(24);
        if (authcheck()) {
            $id = authcheck()['id'];
            $stories = AstrologerStory::join('astrologers', 'astrologers.id', '=', 'astrologer_stories.astrologerId')
                ->where('astrologer_stories.created_at', '>=', $twentyFourHoursAgo)
                ->where('astrologer_stories.created_at', '<=', Carbon::now())
                ->select(
                    'astrologer_stories.astrologerId',
                    'astrologers.name',
                    'astrologers.profileImage',
                    DB::raw('COUNT(astrologer_stories.id) as story_count'),
                    DB::raw('MAX(astrologer_stories.created_at) as latest_story_date'),
                    DB::raw(
                        '(CASE WHEN (select Count(story_view_counts.id) from story_view_counts inner join astrologer_stories as sub_story ON sub_story.id = story_view_counts.storyId where sub_story.astrologerId=astrologer_stories.astrologerId AND story_view_counts.userId="' . $id . ' ") = COUNT(astrologer_stories.id) THEN 1 ELSE 0 END) as allStoriesViewed'
                    )
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();
        } else {
            $stories = AstrologerStory::join('astrologers', 'astrologers.id', '=', 'astrologer_stories.astrologerId')
                ->where('astrologer_stories.created_at', '>=', $twentyFourHoursAgo)
                ->where('astrologer_stories.created_at', '<=', Carbon::now())
                ->select(
                    'astrologer_stories.astrologerId',
                    'astrologers.name',
                    'astrologers.profileImage',
                    DB::raw('COUNT(astrologer_stories.id) as story_count'),
                    DB::raw('MAX(astrologer_stories.created_at) as latest_story_date')
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();
        }
        $getstoriesbyid = AstrologerStory::select('*', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('astrologerId', $request->astrologerId)
            ->orderBy('created_at', 'DESC')
            ->get();

        $Productlist = AstromallProduct::query()->where('isActive', 1)->orderBy('created_at', 'desc')->take(3)->get();
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->first();


        // Get the user's IP address
        $ip = $request->ip();

        if ($ip === '127.0.0.1' || $ip === '::1' || !$ip) {
            $ip = '103.238.108.209';
        }


        // $geoResponse = Http::get("http://ip-api.com/json/{$ip}");
        $geoResponse = Http::get("http://208.95.112.1/json/{$ip}");
        $geoData = $geoResponse->json();

        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
        $timezone = $geoData['timezone'];
        // dd($timezone);

        $date = date('d/m/Y');
        $time = now($timezone)->format('H:i');

        // Generate a unique session key based on the IP and date
        $sessionKey = 'panchang_data_' . $ip . '_' . date('Y-m-d');

        // Check if the panchang data is already stored in the session
        if ($session->has($sessionKey)) {
            $sessionData = $session->get($sessionKey);

            // Check if the session data has expired
            if (time() > $sessionData['expires_at']) {
                $session->remove($sessionKey);
            } else {
                // Use the existing session data
                $Tpanchangs = $sessionData['data'];
            }
        }

        // If the session does not exist or has expired, fetch new data from the API
        if (!$session->has($sessionKey)) {


            $Todayspanchang = Http::get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
                'date' => $date,
                'time' => urlencode($time),
                'tz' => $this->getTimezoneOffset($timezone),
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key->value,
                'lang' => 'en'
            ]);

            $Tpanchangs = $Todayspanchang->json();

            // Store the panchang data in the session with a 24-hour expiry
            $session->set($sessionKey, [
                'data' => $Tpanchangs,
                'expires_at' => time() + (24 * 60 * 60), // 24 hours from now
            ]);
        }

        if ($request->ref) {
            $session->set('referrel_token', $request->ref);
        }

        $sortBy = $request->sortBy;
        $astrologerCategoryId = (int)$request->astrologerCategoryId;
        $searchTerm = $request->input('s');

        // $getIntakeForm = Http::withoutVerifying()->post(url('/') . '/api/chatRequest/getIntakeForm', [
        //     'token' => $token,
        // ])->json();

        $requestApi = Request::create(
            '/api/chatRequest/getIntakeForm',
            'POST',
            ['token' => $token]
        );

        $response = Route::dispatch($requestApi);
        $getIntakeForm = json_decode($response->getContent(), true);
        // dd($getIntakeForm);

        $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
        $isFreeAvailable = true;
        if ($isFreeChat->value == 1) {
            if ($userId) {
                $isChatRequest = DB::table('chatrequest')->where('userId', $userId)->where('chatStatus', '=', 'Completed')->first();
                $isCallRequest = DB::table('callrequest')->where('userId', $userId)->where('callStatus', '=', 'Completed')->first();
                if ($isChatRequest || $isCallRequest) {
                    $isFreeAvailable = false;
                } else {
                    $isFreeAvailable = true;
                }
            }
        } else {
            $isFreeAvailable = false;
        }


        $getAstrologerCategory = AstrologerCategory::where('isActive', 1)->get();


        $boostCutoff = Carbon::now()->subHours(24);

        // Query to get astrologers
        $astrologerQuery = Astrologer::query()
            ->select('astrologers.*', DB::raw('
                (CASE WHEN abp.astrologer_id IS NOT NULL THEN 1 ELSE 0 END) as is_boosted,
                COALESCE(AVG(user_reviews.rating), 0) as rating
            '))
            ->leftJoin('astrologer_boosted_profiles as abp', function ($join) use ($boostCutoff) {
                $join->on('astrologers.id', '=', 'abp.astrologer_id')
                    ->where('abp.boosted_datetime', '>=', $boostCutoff);
            })
            ->leftJoin('user_reviews', 'astrologers.id', '=', 'user_reviews.astrologerId')
            ->where([
                ['astrologers.isActive', true],
                ['astrologers.isVerified', true],
                ['astrologers.isDelete', false]
            ])
            ->whereNotIn('astrologers.id', function ($query) use ($request) {
                $query->select('astrologerId')
                    ->from('blockastrologer')
                    ->where('userId', '=', $request->userId);
            })
            ->groupBy('astrologers.id');

        // Apply category filter
        if ($astrologerCategoryId) {
            $astrologerQuery->whereRaw("FIND_IN_SET(?, astrologers.astrologerCategoryId)", [$astrologerCategoryId]);
        }

        // Sorting without switch or case
        $sortBy = $request->sortBy;

        if ($sortBy === 'experienceHighToLow') {
            $astrologerQuery->orderBy('astrologers.experienceInYears', 'DESC');
        } elseif ($sortBy === 'experienceLowToHigh') {
            $astrologerQuery->orderBy('astrologers.experienceInYears', 'ASC');
        } elseif ($sortBy === 'priceHighToLow') {
            $astrologerQuery->orderBy('astrologers.charge', 'DESC');
        } elseif ($sortBy === 'priceLowToHigh') {
            $astrologerQuery->orderBy('astrologers.charge', 'ASC');
        } else {
            // Default random + boosted + online + rating order
            $astrologerQuery->orderByRaw('
                CASE
                    WHEN abp.astrologer_id IS NOT NULL THEN 1
                    ELSE 2
                END
            ')
                ->orderByRaw('
                CASE
                    WHEN astrologers.chatStatus = "Online" OR astrologers.callStatus = "Online" THEN 1
                    ELSE 2
                END
            ')
                ->orderBy('rating', 'DESC');
        }

        // Search filter
        if ($s = $request->input('s')) {
            $astrologerQuery->where('name', 'LIKE', "%{$s}%");
        }

        // Apply pagination
        $astrologer = $astrologerQuery->paginate(15);
        
        foreach ($astrologer as $astro) {
            $astro->isFreeAvailable = $isFreeAvailable;

            // Fetch and count reviews
            $reviewCount = DB::table('user_reviews')
                ->where('astrologerId', $astro->id)
                ->count();
            $astro->reviews = $reviewCount;

            // Calculate average rating
            $astro->rating = DB::table('user_reviews')
                ->where('astrologerId', $astro->id)
                ->avg('rating') ?? 0;

            // Convert comma-separated values into arrays
            $astrologerCategoryIds = array_map('intval', explode(',', $astro->astrologerCategoryId));
            $allSkillIds = array_map('intval', explode(',', $astro->allSkill));
            $primarySkillIds = array_map('intval', explode(',', $astro->primarySkill));
            $languageIds = array_map('intval', explode(',', $astro->languageKnown));

            // Fetch related data
            $astro->astrologerCategory = DB::table('astrologer_categories')
                ->whereIn('id', $astrologerCategoryIds)
                ->pluck('name')
                ->implode(',');

            $astro->allSkill = DB::table('skills')
                ->whereIn('id', $allSkillIds)
                ->pluck('name')
                ->implode(',');

            $astro->primarySkill = DB::table('skills')
                ->whereIn('id', $primarySkillIds)
                ->pluck('name')
                ->implode(',');

            $astro->languageKnown = DB::table('languages')
                ->whereIn('id', $languageIds)
                ->pluck('languageName')
                ->implode(',');
        }

        if ($request->ajax()) {
            return response()->json([
                'getAstrologer' => $astrologer,
            ]);
        }

        $getsystemflag = SystemFlag::all();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        $Callsection = $getsystemflag->where('name', 'Callsection')->first();
        $userWalletAmount = 0;
        if (authcheck()) {
            $userWalletAmount = UserWallet::where('userId', authcheck()->id)->value('amount') ?? 0;
        }

        $currentDatetime = \Carbon\Carbon::now();
        $pujaCategories = PujaCategory::where('isActive', 1)->paginate(3);

        return view('frontend.pages.index', [
            'token' => $token,
            'banner' => $banner,
            'blogs' => $blogs,
            'productCategory' => $productCategory,
            'astrotalkInNews' => $astrotalkInNews,
            'astrologyVideo' => $astrologyVideo,
            'liveAstrologer' => $liveAstrologer,
            'astrologer' => $astrologer,
            'astromallProduct' => $astromallProduct,
            'currency' => $currency,
            'freekundali' => $freekundali,
            'kundali_matching' => $kundali_matching,
            'blog' => $blog,
            'shop' => $shop,
            'panchang' => $panchang,
            'daily_horoscope' => $daily_horoscope,
            'stories' => $stories,
            'getstoriesbyid' => $getstoriesbyid,
            'puja' => $puja,
            'horosign' => $horosign,
            'Productlist' => $Productlist,
            'Tpanchangs' => $Tpanchangs,
            'geoData' => $geoData,
            'webfaqs' => $webfaqs,
            'getAstrologer' => $astrologer,
            'getAstrologerCategory' => $getAstrologerCategory,
            'sortBy' => $sortBy,
            'astrologerCategoryId' => $astrologerCategoryId,
            'getIntakeForm' => $getIntakeForm,
            'isFreeAvailable' => $isFreeAvailable,
            'searchTerm' => $searchTerm,
            'currency' => $currency,
            'Callsection' => $Callsection,
            'userId' => $userId,
            'walletAmount' => $userWalletAmount,
            'pujaCategories' => $pujaCategories,
        ]);
    }


    private function getTimezoneOffset($timezone)
    {
        $time = new \DateTime('now', new \DateTimeZone($timezone));
        return $time->getOffset() / 3600; // Convert seconds to hours
    }

    public function getAstrologerStories($id)
    {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);


        $stories = AstrologerStory::select('*', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('astrologerId', $id)
            ->orderBy('created_at', 'DESC')
            ->get();


        return response()->json($stories);
    }



    public function viewstory(Request $req)
    {
        try {

            $id = authcheck()['id'] ? authcheck()['id'] : 0;
            // Check if the combination of userId and storyId already exists
            if (DB::table('story_view_counts')->where('userId', $id)->where('storyId', $req->storyId)->exists()) {
                return response()->json(['message' => 'Story already viewed', 'status' => 200], 200);
            }

            // Insert data into story_view_counts table
            $countview = DB::table('story_view_counts')->insert([
                'userId' => $id,
                'storyId' => $req->storyId,
            ]);

            if ($countview) {
                return response()->json(['message' => 'Story Viewed successfully', 'status' => 200], 200);
            } else {
                return response()->json(['error' => 'Failed to insert data', 'status' => 500], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function storeSubscriptionId(Request $request)
    {
        if (authcheck()) {
            $userId = authcheck()['id'];
            // dd($userId);
            // Find the user's device details
            $userDeviceDetails = DB::table('user_device_details')->where('userId', $userId)->first();

            if ($userDeviceDetails) {
                DB::table('user_device_details')
                    ->where('userId', $userId)
                    ->update([
                        'subscription_id_web' => $request->subscription_id_web,
                        'updated_at' => now()
                    ]);
            } else {
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


    public function getStates($countryId)
    {
        $states = DB::table('states')->where('country_id', $countryId)->get();
        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = DB::table('cities')->where('state_id', $stateId)->get();
        return response()->json($cities);
    }


    public function getMyAppointment(Request $request) {}



    public function myAppointment(Request $request)
    {
        if (!authcheck()) {
            return redirect()->route('front.home');
        }

        $userId = authcheck()['id']; // user id

        $appointments = DB::table('call_request_apoinments')
            ->join('callrequest', 'callrequest.id', '=', 'call_request_apoinments.callId')
            ->join('astrologers', 'astrologers.id', '=', 'call_request_apoinments.astrologerId')
            ->where('call_request_apoinments.userId', $userId)
            ->select(
                // ✅ call_request_apoinments se
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

                // ✅ callrequest se
                'callrequest.callStatus',
                'callrequest.IsSchedule',
                'callrequest.channelName',
                'callrequest.call_type',
                'callrequest.totalMin',
                'callrequest.schedule_date',
                'callrequest.schedule_time',

                // ✅ astrologer info
                'astrologers.name as astrologerName',
                'astrologers.profileImage'
            )
            ->orderBy('call_request_apoinments.id', 'DESC')
            ->get();

        return view('frontend.pages.my-appointment', compact('appointments'));
    }




    public function deleteAppointment($id)
    {
        if (!authcheck()) {
            return redirect()->route('front.home')->with('error', 'Unauthorized Access');
        }

        $userId = authcheck()['id'];

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



    public function show($id)
    {
        $news = AstrotalkInNews::findOrFail($id);

        $relatedServices = AstrotalkInNews::take(4)->get(); // Example
        $astrologers = Astrologer::inRandomOrder()->where('isVerified', '1')->take(15)->get();
        $recentBlogs = Blog::where('id', '!=', $id)->latest()->take(5)->get();
        $astrologyVideo = AstrologyVideo::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();

        return view('frontend.pages.newsdetails', compact('news', 'relatedServices', 'astrologers', 'recentBlogs', 'astrologyVideo'));
    }



    public function showIntakeForm(Request $request)
    {
        $userId = Auth::check() ? Auth::id() : null;
        $existingData = null;

        if ($userId) {
            $existingData = Intakeform::where('userId', $userId)->first();
        }

        return view('intakeform', compact('existingData'));
    }

    public function storeOrUpdateIntakeForm(Request $request)
    {
        $userId = Auth::check() ? Auth::id() : null;

        if (!$userId) {
            return redirect()->back()->with('error', 'You must be logged in.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'countryCode' => 'nullable|string|max:10',
            'gender' => 'nullable|string|max:10',
            'birthDate' => 'nullable|date',
            'birthTime' => 'nullable|string|max:20',
            'birthPlace' => 'nullable|string|max:255',
            'maritalStatus' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:100',
            'topicOfConcern' => 'nullable|string|max:255',
        ]);

        $existing = Intakeform::where('userId', $userId)->first();

        if ($existing) {
            $existing->update($validated);
            return redirect()->back()->with('success', 'Form updated successfully!');
        } else {
            $validated['userId'] = $userId;
            Intakeform::create($validated);
            return redirect()->back()->with('success', 'Form submitted successfully!');
        }
    }




    public function randomcall(Request $request)
    {

        if (!authcheck())
            return redirect()->route('front.home');

        $callrequest = DB::table('callrequest')->where('userId', authcheck()['id'])->where('id', $request->callId)->first();
        // dd($callrequest);

        if ($callrequest->callStatus != 'Confirmed')
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        Artisan::call('cache:clear');



        $getAstrologer = Http::withoutVerifying()->post(url('/') . '/api/getAstrologerById', [
            'astrologerId' => $request->astrologerId,
        ])->json();
        // dd($getAstrologer);

        $getSystemFlag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag', [
            'token' => $token,
        ])->json();
        // Convert the response JSON array to a collection
        $recordList = $getSystemFlag['recordList'];

        $agoraAppIdObject = null;
        foreach ($recordList as $item) {
            if ($item['name'] === 'AgoraAppId') {
                $agoraAppIdObject = $item;
                break;
            }
        }
        $agoraAppIdValue = $agoraAppIdObject['value'];


        return view('frontend.pages.callpage', [
            'getAstrologer' => $getAstrologer,
            'callrequest' => $callrequest,
            'agoraAppIdValue' => $agoraAppIdValue,
        ]);
    }


    public function randomgetMyCall(Request $request)
    {
        Artisan::call('cache:clear');

        if (!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getUserById = Http::withoutVerifying()->post(url('/') . '/api/getUserById', [
            'userId' => authcheck()['id'],
            'token' => $token,
        ])->json();

        // dd($getUserById);

        $getProfile = Http::withoutVerifying()->post(url('/') . '/api/getProfile', [
            'token' => $token,

        ])->json();


        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag', [
            'token' => $token,
        ])->json();

        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();






        return view('frontend.pages.my-calls', [
            'getUserById' => $getUserById,
            'getProfile' => $getProfile,
            'currency' => $currency,
        ]);
    }
}
