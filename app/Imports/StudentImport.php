<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Academic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentImport extends Import
{
    private $academicYearId;
    private $registrarCampusId;

    public function __construct($key, $academicYearId = null)
    {
        parent::__construct($key);
        $this->academicYearId = $academicYearId;
        $this->registrarCampusId = Auth::user()->campus_id;
    }

    public function processRow($row)
    {
        try {
            // Cache progress for real-time updates
            $this->updateProgress($row);

            // Validate required fields
            if (empty($row['student_id'])) {
                $this->pushError("Student ID is required");
                return;
            }

            if (empty($row['last_name']) || empty($row['first_name'])) {
                $this->pushError("Last name and first name are required");
                return;
            }

            if (empty($row['course_code'])) {
                $this->pushError("Course code is required");
                return;
            }

            // Clean and validate student ID format
            $studentId = strtoupper(trim($row['student_id']));
            if (!preg_match('/^[A-Z]{2}\d{8}$/', $studentId)) {
                $this->pushError("Invalid student ID format. Expected format: AB12345678");
                return;
            }

            // Check if student ID already exists
            if (Student::where('student_id', $studentId)->exists()) {
                $this->pushError("Student ID {$studentId} already exists");
                return;
            }

            // Find course by code in registrar's campus using pivot table
            $course = Course::where('code', trim($row['course_code']))
                           ->whereHas('campuses', function($query) {
                               $query->where('campuses.id', $this->registrarCampusId);
                           })
                           ->first();

            if (!$course) {
                $this->pushError("Course code '{$row['course_code']}' not found in your campus");
                return;
            }

            // Generate email
            $email = strtolower($studentId) . '@bpc.edu.ph';

            // Check if email already exists
            if (User::where('email', $email)->exists()) {
                $this->pushError("Email {$email} already exists");
                return;
            }

            // Get academic year
            $academicYear = $this->academicYearId 
                ? Academic::find($this->academicYearId)
                : Academic::where('is_active', true)->first();

            if (!$academicYear) {
                $this->pushError("No active academic year found");
                return;
            }

            // Create user with proper name formatting
            $user = User::create([
                'email' => $email,
                'last_name' => $this->formatName(trim($row['last_name'])),
                'first_name' => $this->formatName(trim($row['first_name'])),
                'middle_name' => !empty($row['middle_name']) ? $this->formatName(trim($row['middle_name'])) : null,
                'campus_id' => $this->registrarCampusId,
                'role_id' => 5, // Student role
                'status' => 'active',
                'password' => Hash::make('password'), // Default password
            ]);

            // Create student
            Student::create([
                'user_id' => $user->id,
                'student_id' => $studentId,
                'uli' => !empty($row['uli']) ? trim($row['uli']) : null,
                'course_id' => $course->id,
                'academic_year_id' => $academicYear->id,
            ]);

            $this->pushSuccess("Student {$studentId} imported successfully");

        } catch (\Exception $e) {
            $this->pushError("Unexpected error: " . $e->getMessage());
        }
    }

    /**
     * Format name to properly capitalize each word
     * Examples:
     * - "MARIA CLARA" → "Maria Clara"
     * - "JOSE MIGUEL" → "Jose Miguel"
     * - "MC DONALD" → "McDonald"
     * - "MARY-JANE" → "Mary-Jane"
     */
    private function formatName($name)
    {
        if (empty($name)) {
            return null;
        }

        // Convert to lowercase first
        $name = strtolower($name);
        
        // Split by spaces and capitalize each word
        $words = explode(' ', $name);
        $formattedWords = [];
        
        foreach ($words as $word) {
            if (empty($word)) {
                continue;
            }
            
            // Handle special cases for common name prefixes/suffixes
            $word = $this->handleSpecialNameCases($word);
            
            // Capitalize first letter
            $formattedWords[] = ucfirst($word);
        }
        
        return implode(' ', $formattedWords);
    }

    /**
     * Handle special cases in names like "mc", "o'", etc.
     */
    private function handleSpecialNameCases($word)
    {
        // Special prefixes that have specific capitalization
        $specialPrefixes = [
            'mc' => 'Mc',      // McDonald → McDonald
            'mac' => 'Mac',    // MacArthur → MacArthur
            'o\'' => 'O\'',    // o'brien → O'Brien
        ];
        
        // Check for special prefixes
        foreach ($specialPrefixes as $prefix => $replacement) {
            if (str_starts_with($word, $prefix)) {
                return $replacement . substr($word, strlen($prefix));
            }
        }
        
        // Handle hyphenated names
        if (str_contains($word, '-')) {
            $parts = explode('-', $word);
            $capitalizedParts = array_map(function($part) {
                return ucfirst($part);
            }, $parts);
            return implode('-', $capitalizedParts);
        }
        
        return $word;
    }

    private function updateProgress($row)
    {
        $progressKey = $this->key . '_progress';
        $progressData = [
            'current_row' => $this->current,
            'current_student' => $row['student_id'] ?? 'Unknown',
            'processing' => true,
            'timestamp' => now()->toISOString()
        ];
        Cache::put($progressKey, $progressData, now()->addMinutes(10));
    }
}