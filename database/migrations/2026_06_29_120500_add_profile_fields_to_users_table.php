<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('favorite_posts_public');
            $table->string('location', 100)->nullable()->after('avatar_path');
            $table->string('website')->nullable()->after('location');
            $table->date('birthdate')->nullable()->after('website');
            $table->text('bio')->nullable()->after('birthdate');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'location', 'website', 'birthdate', 'bio']);
        });
    }
};
