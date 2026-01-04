<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\ProfileService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $userService;
    protected $profileService;
    protected $walletService;

    public function __construct(
        UserService $userService,
        ProfileService $profileService,
        WalletService $walletService
    ) {
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->walletService = $walletService;
    }

    public function getMyWallet(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('front.home');
        }

        $user = $this->userService->getAuthUser();
        $profile = $this->profileService->getByUserId($user->id);
        $wallet = $this->walletService->getWalletByUserId($user->id);

        $flags = $this->walletService->getSystemFlags();
        $currency = $flags['currencySymbol'] ?? null;

        return view('frontend.pages.my-wallet', compact(
            'user',
            'profile',
            'wallet',
            'currency'
        ));
    }

    public function walletRecharge(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('front.home');
        }

        $user = $this->userService->getAuthUser();
        $profile = $this->profileService->getByUserId($user->id);

        $rechargeAmounts = $this->walletService->getRechargeAmounts();
        $selectedamount = $rechargeAmounts->first();

        $flags = $this->walletService->getSystemFlags();
        $gstvalue = $flags['Gst'] ?? null;
        $currency = $flags['currencySymbol'] ?? null;

        return view('frontend.pages.wallet-recharge', compact(
            'rechargeAmounts',
            'gstvalue',
            'currency',
            'selectedamount',
            'profile'
        ));
    }
}
