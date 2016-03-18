<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\BookingService;

class BookingController extends Controller
{

    /**
     *
     * @var mixed null | App\Http\Services\IndexService
     */
    protected $service = null;

    public function __construct()
    {
        $this->service = new BookingService();
        $this->middleware('userfromtoken', ['only' => [
                'bookAPackage'
        ]]);
    }

    public function bookAPackage(Request $request)
    {
        try {
            $inputs = $request->all();
            $this->service->bookPacakge($inputs);
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

}