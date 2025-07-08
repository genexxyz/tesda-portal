<div>
    <x-modals.modal-header 
        title="Manage Course Assignments"
        subtitle="Assign courses to {{ $qualificationType->name }}" />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <!-- Qualification Info -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Qualification Type:</h4>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <x-icon name="certificate" style="fas" class="w-4 h-4 text-green-600" />
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $qualificationType->code }}</div>
                            <div class="text-xs text-gray-600">{{ $qualificationType->name }}</div>
                        </div>
                    </div>
                </div>

                <!-- Course Assignment Section -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900">Course Assignment</h4>
                        <div class="text-sm text-gray-500">
                            {{ count($selectedCourses) }} of {{ $courses->count() }} selected
                        </div>
                    </div>

                    <!-- Select All / Clear All -->
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="button" 
                                    wire:click="selectAll"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Select All
                            </button>
                            <button type="button" 
                                    wire:click="clearAll"
                                    class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Course List -->
                    <div class="space-y-2 max-h-80 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50">
                        @forelse($courses as $course)
                            <label class="flex items-center space-x-3 p-3 hover:bg-white rounded-md cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       wire:model.live="selectedCourses" 
                                       value="{{ $course->id }}"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $course->code }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                {{ $course->name }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($course->campuses && $course->campuses->isNotEmpty())
                                                <div class="flex space-x-1">
                                                    @foreach($course->campuses->take(3) as $campus)
                                                        <div class="w-2 h-2 rounded-full" 
                                                             style="background-color: {{ $campus->color }}"
                                                             title="{{ $campus->name }}"></div>
                                                    @endforeach
                                                    @if($course->campuses->count() > 3)
                                                        <span class="text-xs text-gray-500">+{{ $course->campuses->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No courses available</p>
                        @endforelse
                    </div>

                    @error('selectedCourses')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Note:</strong> Courses assigned to this qualification type will be available for assessments and certifications.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>
            
            <x-buttons.primary-button
                type="submit"
                wire:loading.attr="disabled">
                Update Assignments
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
