<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_conges', function (Blueprint $table) {
            $table->id('id_type');
            $table->string('nom_type', 100)->unique();
            $table->string('couleur_calendrier', 7);
            $table->integer('duree_max_jours')->nullable();
            $table->boolean('necessite_justificatif')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_conges');
    }
};
