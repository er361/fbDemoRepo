<?php

namespace App\Listeners;

use App\Events\Logout;
use App\Models\JwtToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveToken
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
     * @param Logout $event
     * @return void
     */
    public function handle(Logout $event)
    {
        //
        JwtToken::whereUserId($event->user_id)->delete();
    }
}
