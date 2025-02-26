<?php

// database/migrations/xxxx_xx_xx_create_grades_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('mark')->check('mark >= 0 AND mark <= 100'); // Range 0-100
            $table->string('letter_grade')->nullable(); // e.g., A, B, C
            $table->timestamps();

            // Optional: Index for faster queries
            $table->index(['student_id', 'subject_id']);
        });

        // Add trigger or logic to auto-set letter_grade (MySQL example)
        DB::statement('
            CREATE TRIGGER set_letter_grade BEFORE INSERT ON grades
            FOR EACH ROW
            BEGIN
                SET NEW.letter_grade = CASE
                    WHEN NEW.mark >= 90 THEN "A"
                    WHEN NEW.mark >= 80 THEN "B"
                    WHEN NEW.mark >= 70 THEN "C"
                    WHEN NEW.mark >= 60 THEN "D"
                    ELSE "F"
                END;
            END;
        ');

        DB::statement('
            CREATE TRIGGER update_letter_grade BEFORE UPDATE ON grades
            FOR EACH ROW
            BEGIN
                SET NEW.letter_grade = CASE
                    WHEN NEW.mark >= 90 THEN "A"
                    WHEN NEW.mark >= 80 THEN "B"
                    WHEN NEW.mark >= 70 THEN "C"
                    WHEN NEW.mark >= 60 THEN "D"
                    ELSE "F"
                END;
            END;
        ');
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS set_letter_grade');
        DB::statement('DROP TRIGGER IF EXISTS update_letter_grade');
        Schema::dropIfExists('grades');
    }
};
