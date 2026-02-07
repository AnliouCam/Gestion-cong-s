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
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->enum('type', ['annuel', 'maladie', 'exceptionnel']);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nb_jours')->default(0);
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->foreignId('valide_par_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
