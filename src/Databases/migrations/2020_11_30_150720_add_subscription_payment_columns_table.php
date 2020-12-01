<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionPaymentColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('payment.subscription_table'), function(Blueprint $table)
        {
            $table->integer('user_id');
            $table->tinyInteger('payment_active')->default(0);
            $table->string('payment_subscription_id')->nullable();
            $table->tinyInteger('payment_free')->default(0);
            $table->string('payment_plan', 25)->nullable();
            $table->integer('payment_amount')->default(0);
            $table->string('payment_interval')->nullable();
            $table->integer('payment_quantity')->default(0);
            $table->string('payment_card')->nullable();
            $table->timestamp('payment_trial_ends_at')->nullable();
            $table->timestamp('payment_subscription_ends_at')->nullable();
            $table->text('payment_subscription_discounts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('payment.subscription_table'), function(Blueprint $table)
        {
            $table->dropColumn(
                'payment_active', 'payment_subscription_id', 'payment_free', 'payment_plan', 'payment_amount',
                'payment_interval', 'payment_quantity', 'payment_card', 'payment_trial_ends_at',
                'payment_subscription_ends_at', 'payment_subscription_discounts'
            );
        });
    }
}
