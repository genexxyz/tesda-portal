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
        Schema::create('school_info', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('tag_line')->nullable();
            $table->timestamps();
        });

        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->integer('number')->unique()->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->foreignId('campus_id')->constrained('campuses')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('academics', function (Blueprint $table) {
            $table->id();
$table->year('start_year');
            $table->year('end_year');
            $table->string('semester');
            $table->boolean('is_active')->default(false);
            $table->boolean('status')->default(true);
            $table->string('description')->nullable();
            
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_info');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('academics');
    }
};
