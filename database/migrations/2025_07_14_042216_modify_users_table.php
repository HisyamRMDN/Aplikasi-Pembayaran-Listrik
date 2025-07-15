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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'id_user');
            $table->string('username', 50)->unique()->after('id_user');
            $table->string('nama_admin', 100)->after('password');
            $table->unsignedBigInteger('id_level')->after('nama_admin');
            $table->foreign('id_level')->references('id_level')->on('levels');
            $table->dropColumn(['name', 'email', 'email_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
            $table->dropForeign(['id_level']);
            $table->dropColumn(['username', 'nama_admin', 'id_level']);
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
