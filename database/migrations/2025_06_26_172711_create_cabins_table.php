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
        Schema::create('cabins', function (Blueprint $table) {
            $table->string('id_cabin', 10)->primary();
            $table->string('name', 100);
            $table->text('description');
            $table->string('province');
            $table->string('regency');
            $table->string('location_address');
            $table->json('cabin_photos')->nullable()->comment('Array of cabin photo URLs or paths');
            $table->boolean('status')->default(true)->comment('true=Available, false=Not Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabins');
    }
};
