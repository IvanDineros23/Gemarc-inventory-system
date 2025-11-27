<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'is_consignment')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_consignment')->default(false)->after('image_path');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'is_consignment')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('is_consignment');
            });
        }
    }
};
