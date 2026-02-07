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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->nullable()->unique();
            $table->string('telephone')->nullable();
            $table->string('poste');
            $table->decimal('salaire_mensuel', 10, 2);
            $table->date('date_embauche');
            $table->date('date_sortie')->nullable();
            $table->enum('statut', ['actif', 'suspendu', 'sorti'])->default('actif');
            $table->integer('solde_conges_annuel')->default(30);
            $table->foreignId('equipe_id')->nullable()->constrained('equipes')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
