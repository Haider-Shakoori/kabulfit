<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('commerce:expire-checkouts')->everyFiveMinutes()->withoutOverlapping();
