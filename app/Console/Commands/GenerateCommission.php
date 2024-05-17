<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateCommission extends Command
{
    protected $signature = 'generate:commission-and-users {user_id : The ID of the user}';
    protected $description = 'Generate commission table entries and random users for a user ID';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $referringUser = User::find($userId);

        if (!$referringUser) {
            $this->error('Referring user not found with ID: ' . $userId);
            return;
        }

        $numUsers = $this->ask('How many users do you want to register? (Default: 10)', 10);

        for ($i = 1; $i <= $numUsers; $i++) {
            $user = new User();
            $user->name =  'User_' . Str::random(5);
            $user->email = Str::random(10) . '@example.com';
            $user->password = bcrypt(Str::random(8));
            $user->referral_code = Str::random(6); // generate unique referral code
            $user->state_id = User::STATE_ACTIVE;
            $user->role_id = User::ROLE_USER;
            if ($referringUser) {
                $user->referred_by = $referringUser->id;
            }

            // Use database transaction
            DB::beginTransaction();

            try {
                if ($user->save()) {
                    $commission = new Commission();
                    $commission->user_id = $user->id;
                    $commission->initial_amount = Commission::INITIAL_AMOUNT;
                    $commission->amount = Commission::INITIAL_AMOUNT;

                    if ($user->referred_by) {
                        $commissionEarned = $user->commission->amount * Commission::COMMISSION_RATE;
                        $user->commission->update([
                            'amount' => $user->commission->amount + $commissionEarned,
                            'commission_percentage' => Commission::DIRECT_COMMISSION_PERCENTAGE,
                            'total_referrals' => $user->commission->total_referrals + 1
                        ]);
                    }

                    $commission->save();

                    // Commit transaction
                    DB::commit();

                    $this->info('User registered successfully with commission');
                } else {
                    throw new \Exception('User not saved');
                }
            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();
                $this->error('User registration failed: ' . $e->getMessage());
            }
        }

        $this->info('Commission table entries and random users generated successfully for user ID: ' . $userId);
    }
}
