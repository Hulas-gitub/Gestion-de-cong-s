<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_conges', function (Blueprint $table) {
            $table->id('id_demande');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_conge_id');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nb_jours');
            $table->text('motif')->nullable();
            $table->enum('statut', ['En attente', 'Approuvé', 'Refusé'])->default('En attente');
            $table->text('commentaire_refus')->nullable();
            $table->unsignedBigInteger('validateur_id')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->text('document_justificatif')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_user_id');
            $table->index('type_conge_id', 'idx_type_conge_id');
            $table->index('validateur_id', 'idx_validateur_id');

            $table->foreign('user_id', 'fk_demande_user')
                  ->references('id_user')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('type_conge_id', 'fk_demande_type')
                  ->references('id_type')->on('types_conges')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('validateur_id', 'fk_demande_validateur')
                  ->references('id_user')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_conges');
    }
};
