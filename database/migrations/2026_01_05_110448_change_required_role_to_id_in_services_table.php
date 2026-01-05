<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('required_role');
            $table->foreignId('required_role_id')->nullable()->after('is_active')->constrained('roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['required_role_id']);
            $table->dropColumn('required_role_id');
            $table->string('required_role')->nullable();
        });
    }
};
