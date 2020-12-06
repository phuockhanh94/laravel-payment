<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionPaymentColumnsTable extends Migration
{
    /**
     * Name table
     *
     * @var undefined
     */
    private $tableName = null;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getTable();

        // Check exist table
        if (Schema::hasTable($this->tableName)) {
            Schema::table($this->tableName, function(Blueprint $table) {
                $this->table($table);
            });
        } else {
            Schema::create($this->tableName, function(Blueprint $table) {
                $table->bigIncrements('id');
                $this->table($table);
            });
        }
    }

    public function table($table)
    {
        $table->integer('user_id');
        $table->tinyInteger('payment_active')->default(0);
        $table->string('payment_subscription_id')->nullable();
        $table->tinyInteger('payment_free')->default(0);
        $table->string('payment_plan')->nullable();
        $table->integer('payment_amount')->default(0);
        $table->string('payment_interval')->nullable();
        $table->integer('payment_quantity')->default(0);
        $table->string('payment_card')->nullable();
        $table->timestamp('payment_trial_ends_at')->nullable();
        $table->timestamp('payment_subscription_ends_at')->nullable();
        $table->text('payment_subscription_discounts')->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getTable();

        // Check create new table or add colummn for exist table
        if (count(Schema::getColumnListing($this->tableName)) == 13) {
            Schema::dropIfExists($this->tableName);
        } else {
            Schema::table($this->tableName, function(Blueprint $table)
            {
                $table->dropColumn(
                    'user_id', 'payment_active', 'payment_subscription_id', 'payment_free', 'payment_plan', 'payment_amount',
                    'payment_interval', 'payment_quantity', 'payment_card', 'payment_trial_ends_at',
                    'payment_subscription_ends_at', 'payment_subscription_discounts'
                );
            });
        }
    }

    public function getTable()
    {
        $this->tableName = config('payment.subscription_table');
    }

    // public function table(Blueprint $table)
    // {

    // }
}
