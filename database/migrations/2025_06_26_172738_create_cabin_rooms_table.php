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
        Schema::create('cabin_rooms', function (Blueprint $table) {
            $table->string('id_cabin');
            $table->foreign('id_cabin')->references('id_cabin')->on('cabins');
            $table->string('id_room', 10)->primary();
            $table->string('typeroom', 100);
            $table->text('description');
            $table->double('price');
            $table->json('room_photos')->nullable()->comment('Array of room photo URLs or paths');
            $table->integer('max_guests');
            $table->integer('slot_room');
            $table->boolean('status')->default(true)->comment('true=Available, false=Not Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabin_rooms');
    }
};
