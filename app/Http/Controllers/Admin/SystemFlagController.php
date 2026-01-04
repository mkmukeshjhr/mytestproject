<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\MstControl;

class SystemFlagController extends Controller
{
    public function getSystemFlag(Request $req)
    {
    // dd('hello systemflag')
    try {
        if (Auth::guard('web')->check()) {
            $flagGroup = DB::table('flaggroup')->whereNull('parentFlagGroupId')->get();

            for ($i = 0; $i < count($flagGroup); $i++) {
                $subGroup = DB::table('flaggroup')
                    ->where('viewenable', 1)
                    ->where('parentFlagGroupId', $flagGroup[$i]->id)
                    ->get();

                if ($subGroup && count($subGroup) > 0) {
                    for ($j = 0; $j < count($subGroup); $j++) {
                        $systemFlag = DB::table('systemflag')
                            ->where('isActive', 1)
                            ->where('flagGroupId', $subGroup[$j]->id)
                            ->get();
                        $subGroup[$j]->systemFlag = $systemFlag;
                    }

                    $flagGroup[$i]->subGroup = $subGroup;

                    $systemFlag = DB::table('systemflag')
                        ->where('flagGroupId', $flagGroup[$i]->id)
                        ->get();
                    $flagGroup[$i]->systemFlag = $systemFlag;
                } else {
                    $systemFlag = DB::table('systemflag')
                        ->where('flagGroupId', $flagGroup[$i]->id)
                        ->get();
                    $flagGroup[$i]->systemFlag = $systemFlag;
                    $flagGroup[$i]->subGroup = [];
                }
            }
            $language = Language::query()->get();
            $mstData = MstControl::query()->get();
            $astroApiCallType = isset($mstData[0]) ? $mstData[0]->astro_api_call_type : null;

            return view('pages.system-flag', compact('flagGroup', 'language', 'astroApiCallType'));
        } else {
            return redirect('/admin/login');
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    }

    public function editSystemFlag(Request $req)
    {
        // dd($req);
        try {
            if (Auth::guard('web')->check()) {
                $flaggroups = $req->input('flaggroups');
                foreach ($flaggroups as $subGroupId => $data) {
                    $isActive = $data['isActive'] ?? 0; // Default to 0 if not present

                    DB::table('flaggroup')
                        ->where('id', '=', $data['id'])
                        ->update(['isActive' => $isActive]);
                }
                foreach ($req->group as $flag) {
                    if (array_key_exists('systemFlag', $flag) && count($flag['systemFlag']) > 0) {
                        foreach ($flag['systemFlag'] as $flagvalue) {
                            if (array_key_exists('value', $flagvalue)) {

                                // --- STORAGE PROVIDER LOGIC START ---
                            if ($flagvalue['name'] === 'storege_provider') {
                                // Reset all providers to isActive = 0
                                DB::table('flaggroup')
                                    ->whereIn('flagGroupName', ['google_bucket', 'aws_bucket', 'digital_ocean'])
                                    ->update(['isActive' => 0]);

                                // Set selected provider to isActive = 1
                                DB::table('flaggroup')
                                    ->where('flagGroupName', $flagvalue['value'])
                                    ->update(['isActive' => 1]);
                            }
                            // --- STORAGE PROVIDER LOGIC END ---
                            
                                if (array_key_exists('valueType', $flagvalue)) {
                                    if ($flagvalue['valueType'] == 'File') {
                                        $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                        $time = Carbon::now()->timestamp;
                                        $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                        $destinationpath = 'public/storage/images/';
                                        $imageName = $flagvalue['name'];
                                        $path = $destinationpath . $imageName . $time . '.png';
                                        File::delete($sysFile->value);
                                        file_put_contents($path, base64_decode($flagvalue['value']));
                                        $flagvalue['value'] = $path;
                                    }
                                    if ($flagvalue['valueType'] == 'MultiSelect') {
                                        $flagvalue['value'] = implode(',', $flagvalue['value']);
                                    }
                                    if ($flagvalue['valueType'] == 'Video') {
                                        // If value is empty (Disabled case), store empty string and skip processing
                                        if (empty($flagvalue['value'])) {
                                            $flagvalue['value'] = "";
                                        } else {
                                            $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $flagvalue['name'];
                                            $path = $destinationpath . $imageName . $time . '.mp4';
                                            File::delete(optional($sysFile)->value); // Use optional() to avoid errors if file doesn't exist
                                            file_put_contents($path, base64_decode($flagvalue['value']));
                                            $flagvalue['value'] = $path;
                                        }
                                    }

                                }
                                $flagData = array(
                                    'value' => $flagvalue['value'],
                                );
                                DB::Table('systemflag')
                                    ->where('name', '=', $flagvalue['name'])
                                    ->update($flagData);
                            }
                        }
                    }
                    if (array_key_exists('subGroup', $flag) && count($flag['subGroup']) > 0) {
                        foreach ($flag['subGroup'] as $flagvalue) {
                            foreach ($flagvalue['systemFlag'] as $sys) {
                                if (array_key_exists('value', $sys)) {
                                    if (array_key_exists('valueType', $sys)) {
                                        if ($sys['valueType'] == 'File') {
                                            $sysFile = DB::Table('systemflag')->where('name', $sys['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $sys['value'] = base64_encode(file_get_contents($sys['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $sys['name'];
                                            $path = $destinationpath . $imageName . $time . '.png';
                                            File::delete($sysFile->value);
                                            file_put_contents($path, base64_decode($sys['value']));
                                            $sys['value'] = $path;
                                        }
                                        if ($sys['valueType'] == 'MultiSelect') {
                                            $sys['value'] = implode(',', $sys['value']);
                                        }
                                        if ($flagvalue['valueType'] == 'Video') {
                                            // If value is empty (Disabled case), store empty string and skip processing
                                            if (empty($flagvalue['value'])) {
                                                $flagvalue['value'] = "";
                                            } else {
                                                $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                                $time = Carbon::now()->timestamp;
                                                $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                                $destinationpath = 'public/storage/images/';
                                                $imageName = $flagvalue['name'];
                                                $path = $destinationpath . $imageName . $time . '.mp4';
                                                File::delete(optional($sysFile)->value); // Use optional() to avoid errors if file doesn't exist
                                                file_put_contents($path, base64_decode($flagvalue['value']));
                                                $flagvalue['value'] = $path;
                                            }
                                        }
                                    }
                                    $flagData = array(
                                        'value' => $sys['value'],
                                    );
                                    DB::Table('systemflag')
                                        ->where('name', '=', $sys['name'])
                                        ->update($flagData);
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => "SystemFlag Update",
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
