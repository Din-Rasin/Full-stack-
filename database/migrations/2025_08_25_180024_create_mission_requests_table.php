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
        Schema::create('mission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests');
            $table->string('destination');
            $table->string('purpose');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('transportation_mode')->nullable();
            $table->text('accommodation_details')->nullable();
            $table->boolean('budget_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_requests');
    }
};
