<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('referral_code')->unique();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->integer('role_id')->nullable();
            $table->integer('state_id')->nullable(); // Define as unsigned integer
            $table->integer('type_id')->nullable();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->integer('initial_amount');
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->integer('total_referrals')->default(0);
            $table->integer('state_id')->default(0); // Define as unsigned integer
            $table->integer('type_id')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('referrals');
    }
};
