<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TESDA Focal Table Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            position: absolute;
            top: 0;
            width: 700px;
            height: auto;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            font-size: 9px;
        }
        .campus-name {
            text-align: left;
            font-weight: bold;
        }
        .competent {
            background-color: #d4edda;
            color: #155724;
        }
        .not-competent {
            background-color: #f8d7da;
            color: #721c24;
        }
        .absent {
            background-color: #fff3cd;
            color: #856404;
        }
        .passing-high {
            background-color: #d4edda;
            color: #155724;
        }
        .passing-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        .passing-low {
            background-color: #f8d7da;
            color: #721c24;
        }
        .course-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .assessment-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            color: #374151;
        }
        .totals-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path($schoolInfo->header_img ?? 'storage/assets/img/default_logo.png')}}" alt="">
        <div class="title">TESDA Focal Assessment Results</div>
        <div class="subtitle">Table Export Report</div>
        <div class="subtitle">Academic Year: {{ $academicYear }}</div>
        <div class="subtitle">Generated on: {{ $generatedAt }}</div>
    </div>

    <div class="info-section">
        @if(isset($filters['export_type']) && $filters['export_type'] === 'specific_table')
            <div class="info-row">
                <span class="info-label">Course:</span>
                <span>{{ $filters['courseName'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Assessment:</span>
                <span>{{ $filters['examType'] }} - {{ $filters['courseCode'] }} {{ $filters['qualificationName'] }} {{ $filters['qualificationLevel'] }}</span>
            </div>
        @else
            <div class="info-row">
                <span class="info-label">Course:</span>
                <span>{{ $filters['courseName'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Campus:</span>
                <span>{{ $filters['campusName'] }}</span>
            </div>
        @endif
    </div>

    @if(empty($data))
        <div style="padding: 20px; text-align: center; color: red;">
            <h3>No Data Found</h3>
            <p>Debug Information:</p>
            <p><strong>Filters:</strong> {{ json_encode($filters) }}</p>
            <p><strong>Academic Year:</strong> {{ $academicYear }}</p>
        </div>
    @else
        @foreach($data as $courseName => $courseData)
            <div class="course-title">{{ $courseName }}</div>
            
            <!-- Debug: Show course data structure -->
            <!-- Course data count: {{ count($courseData) }} -->
            
            @foreach($courseData as $assessmentKey => $assessmentData)
                <div class="assessment-title">{{ $assessmentKey }}</div>
                
                <!-- Debug: Show assessment data structure -->
                <!-- Campus data count: {{ count($assessmentData['campus_data'] ?? []) }} -->
                
                @if(isset($assessmentData['campus_data']) && count($assessmentData['campus_data']) > 0)
                    <table>
                        <thead>
                            <tr>
                                @if(isset($filters['export_type']) && $filters['export_type'] === 'specific_table')
                                    <th>Campus</th>
                                @else
                                    <th>Assessment / Exam Type</th>
                                @endif
                                <th>Total Assessed</th>
                                <th>Competent</th>
                                <th>% of Passing</th>
                                <th>Not Yet Competent</th>
                                <th>No Assessment Yet / Absent</th>
                                <th>Total Students</th>
                            </tr>
                        </thead>                    <tbody>
                        @foreach($assessmentData['campus_data'] as $row)
                            <tr @if($row['campus'] === 'TOTAL') class="totals-row" @endif>
                                @if(isset($filters['export_type']) && $filters['export_type'] === 'specific_table')
                                    <td class="campus-name">{{ $row['campus'] ?? 'N/A' }}</td>
                                @else
                                    <td class="campus-name">
                                        @if($row['campus'] === 'TOTAL')
                                            TOTAL
                                        @else
                                            {{ $assessmentData['assessment_info']['exam_type'] ?? 'N/A' }} - {{ $assessmentData['assessment_info']['course_code'] ?? 'N/A' }}<br>
                                            <small>{{ $assessmentData['assessment_info']['qualification_name'] ?? 'N/A' }} {{ $assessmentData['assessment_info']['qualification_level'] ?? '' }}</small>
                                        @endif
                                    </td>
                                @endif
                                <td>{{ number_format($row['total_assessed'] ?? 0) }}</td>
                                <td class="competent">{{ number_format($row['competent'] ?? 0) }}</td>
                                <td class="{{ ($row['passing_percentage'] ?? 0) >= 70 ? 'passing-high' : (($row['passing_percentage'] ?? 0) >= 50 ? 'passing-medium' : 'passing-low') }}">
                                    {{ $row['passing_percentage'] ?? 0 }}%
                                </td>
                                <td class="not-competent">{{ number_format($row['not_yet_competent'] ?? 0) }}</td>
                                <td class="absent">{{ number_format($row['absent'] ?? 0) }}</td>
                                <td>{{ number_format($row['total_students'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                @else
                    <p>No data available for this assessment.</p>
                    <!-- Debug: Assessment data structure -->
                    <p style="font-size: 8px; color: gray;">{{ json_encode($assessmentData) }}</p>
                @endif
            @endforeach
        @endforeach
    @endif

    <div class="footer">
        <p>Report generated by TESDA Portal System</p>
        <p>{{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
