<div>
    <x-modals.modal-header 
        title="Bulk Import Students"
        subtitle="Import multiple students from Excel/CSV file" />

    <div class="max-h-[80vh] overflow-y-auto">
        <x-modals.modal-body>
            @if(!$isImporting && !$importCompleted)
                <!-- Upload Form -->
                <div class="space-y-6">
                    <!-- Academic Year Selection -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="academic_year_id" 
                                id="academic_year_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <option value="">Select academic year</option>
                            @foreach($academicYears as $academic)
                                <option value="{{ $academic->id }}">
                                    {{ $academic->start_year }} - {{ $academic->end_year }}
                                    @if($academic->is_active)
                                        (Active)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <x-error for="academic_year_id" />
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Import File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               wire:model="file"
                               accept=".xlsx,.xls,.csv"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <x-error for="file" />
                    </div>

                    <!-- Template Download -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Import Template</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Download the template to ensure proper formatting:</p>
                                    <ol class="list-decimal list-inside mt-2 space-y-1">
                                        <li><strong>student_id:</strong> Student ID (e.g., AB12345)</li>
                                        <li><strong>uli:</strong> ULI number (optional but recommended)</li>
                                        <li><strong>last_name:</strong> Student's last name</li>
                                        <li><strong>first_name:</strong> Student's first name</li>
                                        <li><strong>middle_name:</strong> Student's middle name (optional)</li>
                                        <li><strong>course_code:</strong> Course code from your campus</li>
                                    </ol>
                                    
                                </div>
                                <div class="mt-3">
                                    <button type="button" 
                                            wire:click="downloadTemplate"
                                            class="text-blue-600 hover:text-blue-500 font-medium text-sm">
                                        <x-icon name="download" style="fas" class="w-4 h-4 mr-1" />
                                        Download Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($isImporting || $importCompleted)
                <!-- Import Progress and Results -->
                <div class="space-y-6">
                    <!-- Progress Bar -->
                    @if($this->importData['progress'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-900">Import Progress</h3>
                                <span class="text-sm text-gray-600">
                                    {{ $this->importData['progress']['processed_rows'] ?? 0 }} / {{ $this->importData['progress']['total_rows'] ?? 0 }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $this->importData['progress']['percentage'] ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ number_format($this->importData['progress']['percentage'] ?? 0, 1) }}% completed
                            </p>
                        </div>
                    @endif

                    <!-- Results Window -->
                    <div class="border border-gray-200 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900">Import Results</h3>
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            <!-- Success Messages -->
                            @if(!empty($this->importData['success']))
                                @foreach($this->importData['success'] as $success)
                                    <div class="flex items-start p-3 border-b border-gray-100">
                                        <div class="flex-shrink-0">
                                            <x-icon name="check-circle" style="fas" class="w-4 h-4 text-green-500 mt-0.5" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">
                                                <span class="font-medium">Line {{ $success['line'] }}:</span>
                                                {{ $success['message'] }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($success['timestamp'])->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Error Messages -->
                            @if(!empty($this->importData['errors']))
                                @foreach($this->importData['errors'] as $error)
                                    <div class="flex items-start p-3 border-b border-gray-100">
                                        <div class="flex-shrink-0">
                                            <x-icon name="exclamation-circle" style="fas" class="w-4 h-4 text-red-500 mt-0.5" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">
                                                <span class="font-medium">Line {{ $error['line'] }}:</span>
                                                {{ $error['message'] }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($error['timestamp'])->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(empty($this->importData['success']) && empty($this->importData['errors']) && $isImporting)
                                <div class="flex items-center justify-center p-8">
                                    <div class="text-center">
                                        <x-icon name="spinner" style="fas" class="w-8 h-8 text-blue-500 animate-spin mx-auto mb-2" />
                                        <p class="text-sm text-gray-600">Processing import...</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Summary -->
                    @if($importCompleted)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Import Summary</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-green-600 font-medium">
                                        {{ count($this->importData['success']) }} Successful
                                    </span>
                                </div>
                                <div>
                                    <span class="text-red-600 font-medium">
                                        {{ count($this->importData['errors']) }} Failed
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-modals.modal-body>
    </div>

    <x-modals.modal-footer>
        @if(!$isImporting && !$importCompleted)
            <x-buttons.secondary-button 
                wire:click="closeModal"
                :disabled="$isImporting">
                Cancel
            </x-buttons.secondary-button>
            
            <x-buttons.primary-button
                wire:click="startImport"
                :disabled="$isImporting"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="startImport">Start Import</span>
                <span wire:loading wire:target="startImport" class="flex items-center">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                    Starting Import...
                </span>
            </x-buttons.primary-button>
        @elseif($isImporting)
            <div class="flex items-center space-x-4">
                <div class="flex items-center text-sm text-gray-600">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin text-blue-500" />
                    Import in progress... Please wait.
                </div>
                <x-buttons.secondary-button 
                    disabled
                    class="opacity-50 cursor-not-allowed">
                    Please Wait...
                </x-buttons.secondary-button>
            </div>
        @else
            @if($importCompleted)
                <x-buttons.secondary-button 
                    wire:click="resetImport"
                    :disabled="$isImporting">
                    Import Another File
                </x-buttons.secondary-button>
            @endif
            
            <x-buttons.primary-button 
                wire:click="closeModal"
                :disabled="$isImporting">
                Close
            </x-buttons.primary-button>
        @endif
    </x-modals.modal-footer>
</div>

@push('scripts')
<script>
    // Auto-refresh import data every 2 seconds during import
    let refreshInterval;
    
    $wire.on('import-started', () => {
        refreshInterval = setInterval(() => {
            if ($wire.isImporting && !$wire.importCompleted) {
                $wire.$refresh();
            } else {
                clearInterval(refreshInterval);
            }
        }, 2000);
    });

    // Clear interval when modal closes
    $wire.on('modal-closed', () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush