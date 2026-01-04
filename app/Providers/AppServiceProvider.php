<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Page;
use App\Models\AdminModel\SystemFlag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.simple-tailwind');

        View::composer('*', function ($view) {
            $professionTitle = DB::table('systemflag')
                ->where('name', 'professionTitle')
                ->select('value')
                ->first();
            $professionTitle = $professionTitle ? $professionTitle->value : 'Partner';

            $appname = DB::table('systemflag')
            ->where('name', 'AppName')
            ->select('value')
            ->first();
           $appname = $appname ? $appname->value : 'Astroway';

            // Share the data with the view
            $view->with([
                'professionTitle' => $professionTitle,
                'appname' => $appname
            ]);
            $footerPages = Page::where('isActive', 1)->get();
            $view->with('footerPages', $footerPages);

            $coinIcon = systemflag('coinIcon');
            $walletType = strtolower(systemflag('walletType'));
        
            // Share them with all Blade views
            View::share('coinIcon', $coinIcon);
            View::share('walletType', $walletType);

        });
    }
}
