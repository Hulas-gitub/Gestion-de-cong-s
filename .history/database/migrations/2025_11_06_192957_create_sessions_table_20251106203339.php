<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id('id_session');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_token', 128)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('expires_at')->nullable();
            $table->json('data')->nullable();

            $table->index('user_id', 'idx_session_user_id');
            $table->index('session_token', 'idx_session_token');
            $table->index('last_activity', 'idx_last_activity');

            $table->foreign('user_id', 'fk_session_user')
                  ->references('id_user')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
