<?php

namespace App\Providers;

use App\Charts\FieldForceChart;
use App\Charts\FieldForceChartAreaWise;
use App\Models\FieldForce;
use App\Models\User;
use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
     * @param Charts $charts
     * @return void
     */
    public function boot(Charts $charts)
    {
        //
       $user = User::where('role_id', 1)->first();
       Auth::loginUsingId($user->id);

//        $charts->register([
//            FieldForceChart::class,
//            FieldForceChartAreaWise::class
//        ]);
    }


}
