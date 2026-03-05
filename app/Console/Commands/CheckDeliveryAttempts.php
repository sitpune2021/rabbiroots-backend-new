<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schedule;

class CheckDeliveryAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-delivery-attempts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Order::whereNotNull('delivery_attempt_started_at')
            ->where('status', 'assigned')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, delivery_attempt_started_at, NOW()) >= 10')
            ->update([
                'status' => 'delivery_attempted'
            ]);
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:check-delivery-attempts')->everyMinute();
    }
}
