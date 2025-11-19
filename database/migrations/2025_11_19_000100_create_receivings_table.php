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
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('fo_number')->nullable();
            $table->date('date_received')->nullable();
            $table->integer('qty_received')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->integer('beginning_inventory')->nullable();
            $table->integer('ending_inventory')->nullable();
            $table->string('details_file_path')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
