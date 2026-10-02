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
        // Every repeatable piece of portfolio content (projects, jobs, education, ...) is one row.
        Schema::create('portfolie', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->json('data');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        // Page texts are stored in settings and can be longer than 255 characters.
        Schema::table('settings', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolie');

        Schema::table('settings', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
        });
    }
};
