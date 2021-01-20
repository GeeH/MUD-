<?php

namespace App\Http\Controllers;

use App\Service\RoomService;
use Twilio\Rest\Sync\V1\ServiceContext;

class SetupController extends Controller
{
    public function __construct()
    {
    }

    public function __invoke(ServiceContext $serviceContext)
    {

    }
}
