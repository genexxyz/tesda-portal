<div class="max-w-full mx-auto p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Generate Reports</h1>
        <p class="mt-2 text-gray-600">Configure and generate comprehensive TESDA reports with real-time preview</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Panel: Configuration -->
        <div class="space-y-6">
            <!-- Report Type Selection -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Configuration</h2>
                
                <x-inputs.select-input
                    wire:model.live="selectedAcademicYear"
                    label="Academic Year"
                    required="true"
                    placeholder="Select Academic Year"
                    :options="$academicYears"
                    value-field="id"
                    text-field="formatted_description" />

                <x-inputs.select-input
                    wire:model.live="outputFormat"
                    label="Output Format"
                    required="true"
                    :options="collect($outputFormats)->map(fn($label, $value) => (object)['value' => $value, 'label' => $label])"
                    value-field="value"
                    text-field="label" />
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Filters</h3>
                
                <!-- Campus Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Campuses
                    </label>
                    <div class="border-2 border-gray-400 rounded-md max-h-32 overflow-y-auto bg-white">
                        @foreach($campuses as $campus)
                            <div class="flex items-center p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <input type="checkbox" 
                                       wire:model.live="selectedCampuses"
                                       value="{{ $campus->id }}"
                                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1">
                                    {{ $campus->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if(count($selectedCampuses) > 0)
                        <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded">
                            {{ count($selectedCampuses) }} campus(es) selected
                        </div>
                    @endif
                </div>

                <!-- Course Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Courses
                    </label>
                    <div class="border-2 border-gray-400 rounded-md max-h-32 overflow-y-auto bg-white">
                        @foreach($courses as $course)
                            <div class="flex items-center p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <input type="checkbox" 
                                       wire:model.live="selectedCourses"
                                       value="{{ $course->id }}"
                                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1">
                                    <div class="font-semibold">{{ $course->code }}</div>
                                    <div class="text-gray-500 text-xs">{{ $course->name }}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if(count($selectedCourses) > 0)
                        <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded">
                            {{ count($selectedCourses) }} course(s) selected
                        </div>
                    @endif
                </div>

                <!-- Exam Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Exam Types (Optional)
                    </label>
                    @if(!$selectedAcademicYear)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-md text-center">
                            <p class="text-sm text-gray-500">Please select academic year first</p>
                        </div>
                    @else
                        <div class="border-2 border-gray-400 rounded-md max-h-32 overflow-y-auto bg-white">
                            @forelse($examTypes as $examType)
                                <div class="flex items-center p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                    <input type="checkbox" 
                                           wire:model.live="selectedExamTypes"
                                           value="{{ $examType->id }}"
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1">
                                        <div class="font-semibold">{{ $examType->type }}</div>
                                        <div class="text-gray-500 text-xs">{{ $examType->description }}</div>
                                    </label>
                                </div>
                            @empty
                                <div class="p-3 text-center text-sm text-gray-500">
                                    No exam types found for selected criteria
                                </div>
                            @endforelse
                        </div>
                        @if(count($selectedExamTypes) > 0)
                            <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded">
                                {{ count($selectedExamTypes) }} exam type(s) selected
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Qualification Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Qualifications (Optional)
                    </label>
                    @if(!$selectedAcademicYear)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-md text-center">
                            <p class="text-sm text-gray-500">Please select academic year first</p>
                        </div>
                    @else
                        <div class="border-2 border-gray-400 rounded-md max-h-32 overflow-y-auto bg-white">
                            @forelse($qualifications as $qualification)
                                <div class="flex items-center p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                    <input type="checkbox" 
                                           wire:model.live="selectedQualifications"
                                           value="{{ $qualification->id }}"
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1">
                                        <div class="font-semibold">{{ $qualification->name }}</div>
                                        @if($qualification->level)
                                            <div class="text-gray-500 text-xs">Level {{ $qualification->level }}</div>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="p-3 text-center text-sm text-gray-500">
                                    No qualifications found for selected criteria
                                </div>
                            @endforelse
                        </div>
                        @if(count($selectedQualifications) > 0)
                            <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded">
                                {{ count($selectedQualifications) }} qualification(s) selected
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Assessment Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Specific Assessments (Optional)
                    </label>
                    @if(!$selectedAcademicYear)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-md text-center">
                            <p class="text-sm text-gray-500">
                                Please select academic year first
                            </p>
                        </div>
                    @else
                        <div class="border-2 border-gray-400 rounded-md max-h-32 overflow-y-auto bg-white">
                            @forelse($assessments as $assessment)
                                <div class="flex items-center p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                    <input type="checkbox" 
                                           wire:model.live="selectedAssessments"
                                           value="{{ $assessment->id }}"
                                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1">
                                        <div class="font-semibold">{{ $assessment->exam_type }}</div>
                                        <div class="text-gray-500 text-xs">{{ $assessment->qualification_name }}</div>
                                    </label>
                                </div>
                            @empty
                                <div class="p-3 text-center text-sm text-gray-500">
                                    No assessments found for selected criteria
                                </div>
                            @endforelse
                        </div>
                        @if(count($selectedAssessments) > 0)
                            <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded">
                                {{ count($selectedAssessments) }} assessment(s) selected
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Column Selection -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Report Columns</h3>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($availableColumns as $column => $label)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:click="toggleColumn('{{ $column }}')"
                                   {{ in_array($column, $selectedColumns) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label class="ml-3 text-sm text-gray-900 cursor-pointer">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Generate Button -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <x-buttons.primary-button 
                    wire:click="generateReport"
                    class="w-full justify-center"
                    :disabled="!$selectedAcademicYear || count($selectedColumns) === 0">
                    <x-icon name="download" style="fas" class="w-4 h-4 mr-2" />
                    Generate Report
                </x-buttons.primary-button>
            </div>
        </div>

        <!-- Right Panel: Preview -->
        <div class="space-y-6">
            <!-- Preview Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Real-time Preview</h2>
                    @if($isLoadingPreview)
                        <div class="flex items-center text-blue-600">
                            <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                            <span class="text-sm">Updating...</span>
                        </div>
                    @endif
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <div class="text-xs text-blue-600 font-medium">Estimated Rows</div>
                        <div class="text-lg font-bold text-blue-800">{{ number_format($estimatedRows) }}</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <div class="text-xs text-green-600 font-medium">File Size</div>
                        <div class="text-lg font-bold text-green-800">{{ $estimatedFileSize }}</div>
                    </div>
                </div>

                <!-- Report Info -->
                @if(!empty($previewStats))
                    <div class="text-xs text-gray-600 space-y-1">
                        <div><strong>Report Type:</strong> TESDA Focal Assessment Results</div>
                        <div><strong>Academic Year:</strong> {{ $previewStats['academic_year'] ?? 'N/A' }}</div>
                        <div><strong>Campuses:</strong> {{ $previewStats['campuses_included'] ?? 0 }}</div>
                        <div><strong>Courses:</strong> {{ $previewStats['courses_included'] ?? 0 }}</div>
                        <div><strong>Assessments:</strong> {{ $previewStats['assessments_included'] ?? 0 }}</div>
                        @if($previewStats['assessments_included'] > 1)
                            <div class="text-blue-600 font-medium">📋 Each assessment will generate a separate table/sheet</div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Preview Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Data Preview 
                        @if(count($selectedAssessments) > 0)
                            - {{ count($selectedAssessments) }} Assessment{{ count($selectedAssessments) > 1 ? 's' : '' }} Selected
                            @if(count($selectedAssessments) > 1)
                                <span class="text-xs text-blue-600">(Showing first assessment)</span>
                            @endif
                        @else
                            (First 10 rows)
                        @endif
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    @if(!empty($previewData) && !empty($selectedColumns))
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if(count($selectedAssessments) > 0)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                                    @endif
                                    @foreach($selectedColumns as $column)
                                        <th class="px-6 py-3 {{ $column === 'campus' ? 'text-left' : 'text-center' }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $availableColumns[$column] ?? $column }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($previewData as $index => $row)
                                    <tr>
                                        @if(count($selectedAssessments) > 0)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium text-blue-900">{{ $row['course_name'] ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $row['assessment_name'] ?? 'N/A' }}</div>
                                            </td>
                                        @endif
                                        @foreach($selectedColumns as $column)
                                            <td class="px-6 py-4 whitespace-nowrap {{ $column === 'campus' ? 'text-left text-sm font-medium text-gray-900' : 'text-center text-sm text-gray-900' }}">
                                                @php 
                                                    $value = $row[$column] ?? $this->getPreviewValue($row, $column); 
                                                @endphp
                                                @if($column === 'competent')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $value }}
                                                    </span>
                                                @elseif($column === 'passing_percentage')
                                                    @php $percentage = (int) str_replace('%', '', $value); @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                        {{ $percentage >= 70 ? 'bg-green-100 text-green-800' : 
                                                           ($percentage >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ $value }}
                                                    </span>
                                                @elseif($column === 'not_yet_competent')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $value }}
                                                    </span>
                                                @elseif($column === 'absent')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        {{ $value }}
                                                    </span>
                                                @elseif($column === 'total_students')
                                                    <span class="font-medium">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                                                @else
                                                    {{ is_numeric($value) ? number_format($value) : $value }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center">
                            <x-icon name="table" style="fas" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-sm font-medium text-gray-900 mb-2">No Preview Available</h3>
                            <p class="text-xs text-gray-500">
                                @if(empty($selectedColumns))
                                    Select at least one column to display preview
                                @elseif(empty($selectedAssessments))
                                    Select at least one assessment to see preview
                                @else
                                    Configure your filters to see data preview
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <x-icon name="info-circle" style="fas" class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" />
                    <div class="text-sm text-yellow-800">
                        <h4 class="font-semibold mb-1">Report Structure (Matches View Results)</h4>
                        <ul class="space-y-1 text-xs">
                            @if(count($selectedAssessments) > 0)
                                <li>• <strong>Grouped by Course</strong> → <strong>Assessment</strong> → <strong>Campus Data</strong></li>
                                <li>• Excel: Course sheets → Assessment tables → Campus rows</li>
                                <li>• PDF: Course sections → Assessment tables → Campus rows</li>
                                <li>• Same structure as TESDA Focal "View Results" page</li>
                            @else
                                <li>• Select assessments to see course/assessment structure</li>
                            @endif
                            <li>• Preview shows sample data with exact formatting</li>
                            <li>• File size estimation is approximate</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('preview-loading', () => {
            console.log('Preview loading...');
        });
        
        Livewire.on('preview-updated', () => {
            console.log('Preview updated!');
        });

        Livewire.on('download-report', (event) => {
            if (!event || !event.url) {
                console.error('Invalid download event data:', event);
                return;
            }
            
            const url = event.url;
            const format = event.format || 'excel';
            
            // Show loading indicator
            const button = document.querySelector('[wire\\:click="generateReport"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin w-4 h-4 mr-2"></i> Generating...';
                button.disabled = true;
                
                // Create a temporary anchor element and trigger download
                const link = document.createElement('a');
                link.href = url;
                link.style.display = 'none';
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Reset button after delay
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            } else {
                // Fallback if button not found
                window.open(url, '_blank');
            }
        });
    });
</script>
@endpush
