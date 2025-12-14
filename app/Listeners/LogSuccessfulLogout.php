<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Helpers\LogActivity;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        LogActivity::add("Logged out: " . $event->user->name, "Authentication");
    }
}
