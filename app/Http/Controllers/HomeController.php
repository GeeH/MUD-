<?php

namespace App\Http\Controllers;

use App\CommandHandler;
use App\Service\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Uuid;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\SyncGrant;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $userId = $this->getUserIdFromSession();
        $token = $this->getToken($userId);
        return view('home', ['userId' => $userId, 'token' => $token]);
    }

    public function command(Request $request, CommandHandler $commandHandler, UserService $userService): RedirectResponse
    {
        $userId = $this->getUserIdFromSession();
        $user = $userService->getUserById($userId);

        $commandHandler->handle($user, $request->post('command'));

        return Redirect::back();
    }

    private function getUserIdFromSession(): string
    {
        if (!Session::exists('userId')) {
            Session::put('userId', Uuid::uuid4());
        }
        return Session::get('userId');
    }

    private function getToken(string $userId)
    {
        $token = new AccessToken(
            config('services.twilio.account'),
            config('services.twilio.sync_api_token'),
            config('services.twilio.sync_secret'),
            3600,
            $userId,
        );

        // Grant access to Sync
        $syncGrant = new SyncGrant();
        $syncGrant->setServiceSid(config('services.twilio.sync_service'));
        $token->addGrant($syncGrant);

        return $token->toJWT();
    }


}
