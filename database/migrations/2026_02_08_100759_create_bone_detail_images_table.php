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
        Schema::create('bone_detail_images', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bone_detail_id');
            $table->string('images');

            $table->timestamps();
            $table->foreign('bone_detail_id')
                  ->references('id')
                  ->on('bone_details')
                  ->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bone_detail_images');
    }
};
