<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            if (!Schema::hasColumn('prices', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            if (Schema::hasColumn('prices', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
