<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (User::where('role_id', User::ROLE_ADMIN)->exists()) {
            $this->command->error('Already exist');
            return false;
        }
        try {
            DB::beginTransaction();

            $model = new User();
            $model->name = 'Admin';
            $model->email = 'admin@example.com';
            $model->password = Hash::make('admin@123');
            $model->referral_code = Str::random(6);
            $model->state_id = User::STATE_ACTIVE; // Assuming you have a constant like STATE_ACTIVE in the Admin model
            $model->role_id = User::ROLE_ADMIN; // Assuming you have a constant like ROLE_ADMIN in the Admin model
            $model->save();

            // Create commission entry for the admin
            $commission = new Commission();
            $commission->user_id = $model->id;
            $commission->initial_amount = Commission::INITIAL_AMOUNT;
            $commission->amount = Commission::INITIAL_AMOUNT;
            $commission->save();

            DB::commit();

            $this->command->info('Admin user created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create admin user: ' . $e->getMessage());
        }
    }
}
