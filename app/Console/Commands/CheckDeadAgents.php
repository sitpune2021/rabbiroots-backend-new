<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
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
        $deadAgents = User::where('role', 3)
            ->whereHas('deliveryAgent', function ($q) {
                $q->where('is_online', 0);
            })
            ->whereHas('assignedOrders', function ($q) {
                $q->where('status', 'out_for_delivery');
            })
            ->get();

        foreach ($deadAgents as $agent) {

            $order = Order::where('agent_id', $agent->id)
                ->where('status', 'out_for_delivery')
                ->first();

            if (!$order) continue;

            // Penalize agent
            $order->update([
                'penalty_applied' => 1,
                'reassigned_from' => $agent->id
            ]);

            // Find next available agent
            $newAgent = User::where('role', 3)
                ->where('id', '!=', $agent->id)
                ->whereHas('deliveryAgent', function ($q) {
                    $q->where('is_online', 1)
                        ->where('is_available', 1);
                })
                ->first();

            if ($newAgent) {
                $order->update([
                    'agent_id' => $newAgent->id,
                    'status' => 'assigned'
                ]);

                Log::info('Order reassigned', [
                    'order_id' => $order->id,
                    'new_agent' => $newAgent->id
                ]);
            }
        }
    }
}
