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
        Schema::create('system_activities', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('user_id')->nullable();
            $blueprint->string('type'); // Inventory, Sales, Accounting, etc.
            $blueprint->string('action'); // Created, Updated, Deleted, etc.
            $blueprint->text('description');
            $blueprint->string('color')->default('slate');
            $blueprint->timestamps();

            $blueprint->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_activities');
    }
};
