<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateAgentIncentives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-agent-incentives';

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
        $agents = \App\Models\User::where('role', 3)
            ->whereHas('deliveryAgent')
            ->get();

        foreach ($agents as $agent) {

            $deliveryData = $agent->deliveryAgent;

            $completedOrders = \App\Models\Order::where('agent_id', $agent->id)
                ->where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            Log::info('completedOrders', [

                'completedOrders' => $completedOrders,
                'agent_id' => $agent->id
            ]);

            if ($completedOrders == 0) {
                continue;
            }

            $deadIncidents = $deliveryData->dead_phone_incidents;
            Log::info('deadIncidents', [
                'agent_id' => $agent->id,

                'deadIncidents' => $deadIncidents
            ]);
            $deadPercentage = ($deadIncidents / $completedOrders) * 100;
     
            if ($deadPercentage < 5) {

                // Give ₹500 bonus
                $deliveryData->increment('wallet_balance', 500);

                Log::info('Incentive Given', [
                    'agent_id' => $agent->id,
                    'bonus' => 500,
                    'dead_percentage' => $deadPercentage
                ]);
            }
        }
    }
}
