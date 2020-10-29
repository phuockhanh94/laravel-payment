<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerPaymentColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('payment.customer_table'), function(Blueprint $table)
        {
            $table->string('payment_id')->nullable();
            $table->text('payment_cards')->nullable();
            $table->text('payment_discounts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('payment.customer_table'), function(Blueprint $table)
        {
            $table->dropColumn('payment_id', 'payment_cards', 'payment_discounts');
        });
    }
}
