<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('party_id')->comment('customer id');
            $table->unsignedInteger('user_id')->comment('operator id');
            $table->enum('payment_type', ['cash', 'bank']);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat', 10, 2)->default(0)->comment('percentage');
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->comment('flat/percentage');
            $table->decimal('tendered', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->decimal('customer_balance', 10, 2)->default(0)->comment('Customer balance after completing sale');
            $table->boolean('adjust_to_customer_balance')->comment('Status of adjust to customer balance. True for adjust to customer balance. False for not adjust to customer balance.');
            $table->boolean('delivered')->comment('Delivery status of sale');
            $table->unsignedInteger('salesman_id')->comment('Salesman id of user table');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('cash_id')->nullable();
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('party_id')
                ->references('id')
                ->on('parties')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('cash_id')
                ->references('id')
                ->on('cashes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('salesman_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');

            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
