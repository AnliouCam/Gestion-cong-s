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
        Schema::create('paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->string('mois', 7); // Format: YYYY-MM
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('retenues', 10, 2)->default(0);
            $table->decimal('montant_total', 10, 2);
            $table->date('date_paiement')->nullable();
            $table->enum('mode_paiement', ['cash', 'virement']);
            $table->enum('statut', ['paye', 'non_paye'])->default('non_paye');
            $table->string('reference_paiement')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('enregistre_par_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paies');
    }
};
