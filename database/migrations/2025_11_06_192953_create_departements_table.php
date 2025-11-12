<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departements', function (Blueprint $table) {
            $table->id('id_departement');
            $table->string('nom_departement', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('chef_departement_id')->nullable();
            $table->string('couleur_calendrier', 7)->default('#3b82f6');
            $table->boolean('actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departements');
    }
};
