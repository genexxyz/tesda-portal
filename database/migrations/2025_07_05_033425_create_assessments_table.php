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
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['isa', 'mandatory'])->default('isa');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('competency_types', function (Blueprint $table) {
            $table->id();
            $table->string('code'); //BKP, SMAW, FBS
            $table->string('name');
            $table->string('level'); // NC I, NC II, NC III, NC IV
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('qualification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Competent, Not Yet Competent, Dropped, Absent
            $table->string('description')->nullable();
            $table->timestamps();
        });


        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained('exam_types')->restrictOnDelete();
            $table->foreignId('competency_type_id')->constrained('competency_types')->restrictOnDelete();
            $table->foreignId('campus_id')->constrained('campuses')->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academics')->restrictOnDelete();

            $table->string('assessment_center');
            $table->foreignId('assessor_id')->constrained('assessors')->restrictOnDelete();
            $table->date('assessment_date');
            $table->timestamps();
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('qualification_type_id')->constrained('qualification_types')->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
