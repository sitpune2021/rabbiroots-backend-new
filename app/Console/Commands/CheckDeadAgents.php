<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDeadAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-dead-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check offline delivery agents';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $orders = \App\Models\Order::where('status', 'out_for_delivery')
            ->whereNotNull('agent_id')
            ->get();

        foreach ($orders as $order) {

            $deadAgent = \App\Models\User::find($order->agent_id);

            if (!$deadAgent || !$deadAgent->deliveryAgent) {
                continue;
            }

            $deliveryData = $deadAgent->deliveryAgent;

            // Agent is dead if offline
            if ($deliveryData->is_online == 1) {
                continue; // Agent still active
            }

            // Find next available agent
            $newAgent = \App\Models\User::where('role', 3)
                ->where('id', '!=', $deadAgent->id)
                ->whereHas('deliveryAgent', function ($q) {
                    $q->where('is_online', 1)
                        ->where('is_available', 1);
                })
                ->first();

            if (!$newAgent) {
                Log::info('No available agent found');
                continue;
            }

            // Apply penalty
            $order->update([
                'reassigned_from' => $deadAgent->id,
                'penalty_applied' => 1,
                'reassign_count' => $order->reassign_count + 1,
                'dead_detected_at' => now(),
                'agent_id' => $newAgent->id,
                'status' => 'assigned'
            ]);

            // Update old agent
            $deadAgent->deliveryAgent->update([
                'current_order_id' => null,
                'is_available' => 0,
                'dead_phone_incidents' => $deadAgent->deliveryAgent->dead_phone_incidents + 1
            ]);

            // Update new agent
            $newAgent->deliveryAgent->update([
                'current_order_id' => $order->id,
                'is_available' => 0
            ]);

            Log::info('Order reassigned successfully', [
                'order_id' => $order->id,
                'from' => $deadAgent->id,
                'to' => $newAgent->id
            ]);
        }
    }
}
