<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use App\Hotel;

class HotelComposer
{
    public function compose(View $view)
    {
        $hotel = Hotel::all();
        $view->with('hotel', $hotel);
    }
}
