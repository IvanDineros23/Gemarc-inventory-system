<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderFieldsToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('dr_number')->nullable()->after('id');
            $table->string('customer')->nullable()->after('dr_number');
            $table->dateTime('dr_date')->nullable()->after('customer');
            $table->string('part_number')->nullable()->after('product_id');
            $table->string('item_name')->nullable()->after('part_number');
            $table->text('item_description')->nullable()->after('item_name');
            $table->decimal('unit_cost', 12, 2)->nullable()->after('qty');
            $table->string('unit')->nullable()->after('unit_cost');
            $table->string('currency', 10)->nullable()->after('unit');
            $table->string('intended_to')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'dr_number','customer','dr_date','part_number','item_name','item_description','unit_cost','unit','currency','intended_to'
            ]);
        });
    }
}
