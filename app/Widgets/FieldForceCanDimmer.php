<?php

namespace App\Widgets;

use App\Models\FieldForce;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class FieldForceCanDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = FieldForce::with('order_deliveries')->has('order_deliveries')->count();
        $string = 'Field Forces Can Use SFA';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-group',
            'title'  => "{$count} {$string}",
            'text'   => __($count . ' Field Forces are currently able to order and Delivery through the SFA'),
            'button' => [
                'text' => __('View Field Forces'),
                'link' => route('can-order-delivery'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Voyager::model('User'));
    }
}
