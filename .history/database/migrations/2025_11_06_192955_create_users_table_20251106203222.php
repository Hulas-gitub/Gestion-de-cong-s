<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('telephone', 20)->nullable();
            $table->string('profession', 100)->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->string('matricule', 20)->unique();
            $table->date('date_embauche');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('departement_id')->nullable();
            $table->integer('solde_conges_annuel')->default(25);
            $table->integer('conges_pris')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('role_id', 'idx_role_id');
            $table->index('departement_id', 'idx_departement_id');
            $table->index('email', 'idx_email');

            $table->foreign('role_id', 'fk_users_role')
                  ->references('id_role')->on('roles')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('departement_id', 'fk_users_departement')
                  ->references('id_departement')->on('departements')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        // Ajouter la clé étrangère pour chef_departement après création de users
        Schema::table('departements', function (Blueprint $table) {
            $table->foreign('chef_departement_id', 'fk_departement_chef')
                  ->references('id_user')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('departements', function (Blueprint $table) {
            $table->dropForeign('fk_departement_chef');
        });

        Schema::dropIfExists('users');
    }
};
