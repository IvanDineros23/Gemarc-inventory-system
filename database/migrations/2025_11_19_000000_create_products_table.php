<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('part_number')->nullable();
            $table->string('inventory_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('supplier')->nullable();
            $table->string('fo_number')->nullable();
            $table->date('date_received')->nullable();
            $table->integer('qty_received')->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->integer('beginning_inventory')->nullable();
            $table->integer('ending_inventory')->nullable();
            $table->decimal('total', 14, 2)->nullable();
            $table->string('unit')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
