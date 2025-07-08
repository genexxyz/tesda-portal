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
            $table->enum('type', ['ISA', 'MANDATORY'])->default('ISA');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('competency_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Competent, Not Yet Competent, Dropped, Absent
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('qualification_types', function (Blueprint $table) {
            
            $table->id();
            $table->string('code'); //BKP, SMAW, FBS
            $table->string('name'); // e.g., Food and Beverage Services NC II, Shielded Metal Arc Welding NC I
            $table->enum('level', ['NC I', 'NC II', 'NC III', 'NC IV'])->default('NC II'); // NC I, NC II, NC III, NC IV
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['code', 'level'], 'unique_code_level');
            
            // Add unique constraint for name + level combination
            $table->unique(['name', 'level'], 'unique_name_level');
        });

//Create pivot table for course and qualification
        Schema::create('course_qualification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignId('qualification_type_id')->constrained('qualification_types')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('assessment_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->timestamps();
        });

        Schema::create('assessors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('assessor_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessor_id')->constrained('assessors')->restrictOnDelete();
            $table->foreignId('assessment_center_id')->constrained('assessment_centers')->restrictOnDelete();
            $table->timestamps();
        });


        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained('exam_types')->restrictOnDelete();
            $table->foreignId('qualification_type_id')->constrained('qualification_types')->restrictOnDelete();
            $table->foreignId('campus_id')->constrained('campuses')->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academics')->restrictOnDelete();

            $table->foreignId('assessment_center_id')->constrained('assessment_centers')->restrictOnDelete();
            $table->foreignId('assessor_id')->constrained('assessors')->restrictOnDelete();
            $table->date('assessment_date');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('status');
        });


        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('competency_type_id')->nullable()->constrained('competency_types')->restrictOnDelete(); //will be used to store the result of the assessment (Competent, Not Yet Competent, Dropped, Absent) after the assessment date
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'student_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('competency_types');
        Schema::dropIfExists('qualification_types');
        Schema::dropIfExists('course_qualification');
        Schema::dropIfExists('assessment_centers');
        Schema::dropIfExists('assessors');
        Schema::dropIfExists('assessor_centers');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('results');
    }
};
