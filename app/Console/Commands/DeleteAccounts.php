<?php

namespace App\Console\Commands;

use App\Models\AccountDeletion;
use Illuminate\Console\Command;

class DeleteAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete accounts after scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = AccountDeletion::where('scheduled_delete_at', '<=', now())
            ->where('is_processed', false)
            ->get();

        foreach ($users as $deletion) {

            $user = $deletion->user;

            if ($user && !$user->trashed()) {
                $user->delete(); // soft delete
            }

            $deletion->update([
                'is_processed' => true
            ]);
        }
         $this->info('Accounts deleted successfully');
    }
}
