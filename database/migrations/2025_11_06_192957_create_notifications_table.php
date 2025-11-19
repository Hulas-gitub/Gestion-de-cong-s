<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->unsignedBigInteger('user_id');
            $table->string('titre', 255);
            $table->text('message');
            $table->enum('type_notification', ['info', 'success', 'warning', 'error'])->default('info');
            $table->boolean('lu')->default(false);
            $table->string('url_action', 500)->nullable();
            $table->text('document_info')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_notif_user_id');

            $table->foreign('user_id', 'fk_notifications_user')
                  ->references('id_user')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
