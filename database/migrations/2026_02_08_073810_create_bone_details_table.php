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
        Schema::create('bone_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bonepost_id');
            $table->string('bone_type');
            $table->enum('body_side', ['Left', 'Right', 'Bilateral']);
            $table->string('specimen_condition')->nullable();
            $table->string('preservation_method')->nullable();
            $table->integer('quantity')->default(1);

            $table->timestamps();

            $table->foreign('bonepost_id')
                ->references('id')
                ->on('bone_posts')
                ->onDelete('cascade');

            $table->index('bonepost_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bone_details');
    }
};
