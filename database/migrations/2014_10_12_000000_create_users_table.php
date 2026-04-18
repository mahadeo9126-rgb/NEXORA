<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(0);
            $table->unsignedBigInteger('sponsor_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('position', ['left', 'right'])->nullable();
            $table->string('wallet_address')->unique();
            $table->tinyInteger('rub_rank')->default(0); // 0-7
            $table->decimal('withdrawable_balance', 18, 4)->default(0);
            $table->decimal('shopping_credit', 18, 4)->default(0);
            $table->timestamp('last_subscription_at')->nullable();
            $table->enum('placement_pref', ['extreme_left', 'left', 'right', 'extreme_right'])->default('extreme_left');
            $table->timestamp('withdrawal_locked_until')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
