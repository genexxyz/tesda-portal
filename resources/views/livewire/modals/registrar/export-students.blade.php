<div>
    <x-modals.modal-header 
        title="Export Students to Excel"
        subtitle="Configure your student data export settings" />

    <div class="max-h-[80vh] overflow-y-auto">
        <x-modals.modal-body>
            <form wire:submit="exportStudents">
                <!-- Academic Year Selection -->
                <x-inputs.select-input
                    wire:model="selectedAcademicYear"
                    id="academicYear"
                    label="Academic Year"
                    placeholder="Select Academic Year"
                    required="true"
                    :options="$academicYears"
                    value-field="id"
                    text-field="formatted_description" />

                <!-- Course Selection -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Select Courses
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <button type="button" 
                                    wire:click="selectAllCourses"
                                    class="text-xs text-primary hover:text-primary/80 font-medium">
                                Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button type="button" 
                                    wire:click="deselectAllCourses"
                                    class="text-xs text-gray-600 hover:text-gray-800 font-medium">
                                Clear All
                            </button>
                        </div>
                    </div>
                    
                    <div class="border-2 border-gray-400 rounded-md max-h-40 overflow-y-auto bg-white">
                        @foreach($courses as $course)
                            <div class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <input type="checkbox" 
                                       wire:click="toggleCourse({{ $course->id }})"
                                       {{ in_array($course->id, $selectedCourses) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-900 cursor-pointer flex-1"
                                       wire:click="toggleCourse({{ $course->id }})">
                                    <div class="font-semibold">{{ $course->code }}</div>
                                    <div class="text-gray-500 text-xs">{{ $course->name }}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($selectedCourses) > 0)
                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-xs text-green-700">
                            <span class="font-semibold">{{ count($selectedCourses) }} course(s) selected:</span>
                            {{ $selectedCoursesNames }}
                        </div>
                    @endif
                    
                    @error('selectedCourses')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Include Results Option -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input wire:model="includeResults" 
                                   id="includeResults" 
                                   type="checkbox" 
                                   class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="includeResults" class="font-semibold text-gray-700">
                                Include Assessment Results
                            </label>
                            <p class="text-gray-500 text-xs mt-1">
                                When enabled, the export will include assessment results for each student. 
                                Only students who are assigned to assessments will show assessment details.
                                Students not assigned to an assessment will show "Not Assigned".
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Export Format Info -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="info-circle" style="fas" class="w-5 h-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold mb-2">Export Format</h4>
                            <ul class="space-y-1 text-green-700 text-xs">
                                    <li>• <strong>Multiple sheets:</strong> One sheet per selected course</li>
                                    <li>• <strong>Student data:</strong> ID, ULI, Name, Email, Status</li>
                                    @if($includeResults)
                                        <li>• <strong>Assessment results:</strong> Assessor, Center, Date, and Competency Type for assigned assessments only</li>
                                        <li>• <strong>Color coding:</strong> ISA (Green), MANDATORY (Red), Competency results with status colors</li>
                                    @endif
                                    <li>• <strong>File format:</strong> Excel (.xlsx)</li>
                                </ul>
                        </div>
                    </div>
                </div>
            </form>
        </x-modals.modal-body>
    </div>

    <x-modals.modal-footer>
        <div class="flex items-center justify-end space-x-3">
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>
            
            <x-buttons.primary-button 
                wire:click="exportStudents"
                wire:loading.attr="disabled"
                :disabled="count($selectedCourses) === 0 || !$selectedAcademicYear">
                <span wire:loading.remove wire:target="exportStudents">
                    <x-icon name="download" style="fas" class="w-4 h-4 mr-2" />
                    Export to Excel
                </span>
                <span wire:loading wire:target="exportStudents">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                    Exporting...
                </span>
            </x-buttons.primary-button>
        </div>
    </x-modals.modal-footer>
</div>
