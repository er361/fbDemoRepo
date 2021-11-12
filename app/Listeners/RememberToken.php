<?php

namespace App\Listeners;

use App\Events\Login;
use App\Models\JwtToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RememberToken
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(Login $event)
    {
        //
        JwtToken::updateOrCreate([
            'user_id' => $event->user_id,
            'token' => $event->token
        ]);
    }
}
