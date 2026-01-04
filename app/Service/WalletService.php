<?php

namespace App\Services;

use App\Models\AdminModel\SystemFlag;
use App\Models\Wallet;
use App\Models\RechargeAmount;

class WalletService
{
    public function getWalletByUserId(int $userId)
    {
        return Wallet::where('user_id', $userId)->first();
    }

    public function getRechargeAmounts()
    {
        return RechargeAmount::where('status', 1)->get();
    }

    public function getSystemFlags()
    {
        return SystemFlag::all()->keyBy('name');
    }
}
