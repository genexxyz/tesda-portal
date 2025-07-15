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
                    <x-inputs.select-input
                        wire:model="academic_year_id"
                        id="academic_year_id"
                        label="Academic Year"
                        placeholder="Select Academic Year"
                        :required="true"
                        :options="$academicYears"
                        value-field="id"
                        text-field="formatted_description" />

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Import File
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="file" 
                               wire:model="file"
                               accept=".xlsx,.xls,.csv"
                               class="block w-full text-sm text-gray-900 border-2 border-gray-400 rounded-md cursor-pointer bg-white focus:border-primary focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/80">
                        <p class="mt-1 text-xs text-gray-500">Supported formats: .xlsx, .xls, .csv (Max: 10MB)</p>
                        @error('file')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Template Download -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <x-icon name="info-circle" style="fas" class="h-5 w-5 text-blue-600 mr-2 mt-0.5" />
                            <div class="text-sm text-blue-800">
                                <h4 class="font-semibold mb-2">Import Template</h4>
                                <p class="text-blue-700 text-xs mb-3">Download the template to ensure proper formatting:</p>
                                <ul class="space-y-1 text-blue-700 text-xs mb-3">
                                    <li>• <strong>Email:</strong> Must be unique and valid (Required)</li>
                                    <li>• <strong>First Name:</strong> Student's first name (Required)</li>
                                    <li>• <strong>Last Name:</strong> Student's last name (Required)</li>
                                    <li>• <strong>Middle Name:</strong> Student's middle name (Optional)</li>
                                    <li>• <strong>Student ID:</strong> Will be auto-generated if empty (Optional)</li>
                                    <li>• <strong>ULI:</strong> Unique Learner Identifier (Optional)</li>
                                    <li>• <strong>Status:</strong> active, inactive, or dropped (Required)</li>
                                </ul>
                                <x-buttons.secondary-button
                                    wire:click="downloadTemplate"
                                    class="text-xs">
                                    <x-icon name="download" style="fas" class="w-3 h-3 mr-1" />
                                    Download Template
                                </x-buttons.secondary-button>
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
            <div class="flex items-center justify-end space-x-3">
                <x-buttons.secondary-button wire:click="closeModal">
                    Cancel
                </x-buttons.secondary-button>
                
                <x-buttons.primary-button
                    wire:click="startImport"
                    wire:loading.attr="disabled"
                    :disabled="!$file || !$academic_year_id">
                    <span wire:loading.remove wire:target="startImport">
                        <x-icon name="upload" style="fas" class="w-4 h-4 mr-2" />
                        Start Import
                    </span>
                    <span wire:loading wire:target="startImport" class="flex items-center">
                        <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                        Starting Import...
                    </span>
                </x-buttons.primary-button>
            </div>
        @elseif($isImporting)
            <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-600">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin text-primary" />
                    Import in progress... Please wait.
                </div>
                <x-buttons.secondary-button disabled>
                    Please Wait...
                </x-buttons.secondary-button>
            </div>
        @else
            <div class="flex items-center justify-end space-x-3">
                @if($importCompleted)
                    <x-buttons.secondary-button wire:click="resetImport">
                        <x-icon name="refresh" style="fas" class="w-4 h-4 mr-2" />
                        Import Another File
                    </x-buttons.secondary-button>
                @endif
                
                <x-buttons.primary-button wire:click="closeModal">
                    <x-icon name="check" style="fas" class="w-4 h-4 mr-2" />
                    Close
                </x-buttons.primary-button>
            </div>
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