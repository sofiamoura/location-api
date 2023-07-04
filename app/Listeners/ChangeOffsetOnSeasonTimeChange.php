<?php

namespace App\Listeners;

use App\Events\SeasonTimeChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ChangeOffsetOnSeasonTimeChange
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SeasonTimeChanged $event): void
    {
        if ($event->isDST) {
            DB::connection('locations_db')->update('UPDATE city SET timezone_code = ?, timezone_offset = ? WHERE id = ?', [$timezone_code, $timezone_offset, $id]);            
        } else {
            DB::connection('locations_db')->update('UPDATE city SET timezone_code = ?, timezone_offset = ? WHERE id = ?', [$timezone_code, $timezone_offset, $id]);            
        }
    }
}
