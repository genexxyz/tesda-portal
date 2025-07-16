<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TESDA Focal Assessment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .course-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .course-title {
            background-color: #f0f8ff;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
            font-weight: bold;
            font-size: 16px;
            color: #1e40af;
        }
        .assessment-table {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .assessment-title {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 10px;
        }
        .campus-cell {
            text-align: left;
            font-weight: bold;
        }
        .competent {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .not-competent {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }
        .absent {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
        }
        .passing-high {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .passing-medium {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
        }
        .passing-low {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 5px;
            background: white;
        }
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TESDA Focal Assessment Results Report</h1>
        <p><strong>Academic Year:</strong> {{ $academicYear }}</p>
        <p><strong>Generated on:</strong> {{ $generatedAt }}</p>
    </div>

    @foreach($reportData as $courseName => $courseData)
        <div class="course-section">
            <div class="course-title">{{ $courseName }}</div>
            
            @foreach($courseData as $assessmentName => $assessmentData)
                <div class="assessment-table">
                    <div class="assessment-title">{{ $assessmentName }}</div>
                    
                    <table>
                        <thead>
                            <tr>
                                @foreach($selectedColumns as $column)
                                    <th>{{ $this->getColumnLabel($column) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessmentData['campus_data'] as $campusData)
                                <tr>
                                    @foreach($selectedColumns as $column)
                                        @php
                                            $value = $campusData[$column] ?? '';
                                            $cellClass = '';
                                            
                                            if ($column === 'campus') {
                                                $cellClass = 'campus-cell';
                                            } elseif ($column === 'competent') {
                                                $cellClass = 'competent';
                                            } elseif ($column === 'not_yet_competent') {
                                                $cellClass = 'not-competent';
                                            } elseif ($column === 'absent') {
                                                $cellClass = 'absent';
                                            } elseif ($column === 'passing_percentage') {
                                                $percentage = (float) str_replace('%', '', $value);
                                                if ($percentage >= 70) {
                                                    $cellClass = 'passing-high';
                                                } elseif ($percentage >= 50) {
                                                    $cellClass = 'passing-medium';
                                                } else {
                                                    $cellClass = 'passing-low';
                                                }
                                            }
                                        @endphp
                                        <td class="{{ $cellClass }}">
                                            @if(is_numeric($value) && $column !== 'passing_percentage')
                                                {{ number_format($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        TESDA Focal Assessment Results Report - Generated {{ $generatedAt }}
    </div>

    @php
        function getColumnLabel($column) {
            $labels = [
                'campus' => 'Campus',
                'total_assessed' => 'Total Assessed',
                'competent' => 'Competent',
                'passing_percentage' => '% of Passing',
                'not_yet_competent' => 'Not Yet Competent',
                'absent' => 'No Assessment Yet / Absent',
                'total_students' => 'Total Students'
            ];
            return $labels[$column] ?? $column;
        }
    @endphp
</body>
</html>
