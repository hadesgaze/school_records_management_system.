<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\LogActivity;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        LogActivity::add("Logged in: " . $event->user->name, "Authentication");
    }
}
