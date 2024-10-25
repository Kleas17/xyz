<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToTracksTable extends Migration
{
    public function up()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained();
        });
    }

    public function down()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
