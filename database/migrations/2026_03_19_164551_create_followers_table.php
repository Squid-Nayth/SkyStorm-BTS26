<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            // L'utilisateur qui suit
            $table->foreignId('follower_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // L'utilisateur qui est suivi
            $table->foreignId('following_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            // PK un user ne peut pas suivre 2x la même personne
            $table->primary(['follower_id', 'following_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
