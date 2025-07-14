<div>
    <x-modals.modal-header 
        title="Bulk Import Students"
        subtitle="Import multiple students from Excel/CSV file." />

    <form wire:submit="import">
        <x-modals.modal-body>
            <div class="space-y-6">
                <!-- Import Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-600 mr-2 mt-0.5" />
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">Import Instructions</h3>
                            <ul class="text-sm text-blue-700 mt-2 space-y-1">
                                <li>• Use the provided template to format your data correctly</li>
                                <li>• Supported formats: Excel (.xlsx, .xls) and CSV (.csv)</li>
                                <li>• Maximum file size: 10MB</li>
                                <li>• Ensure email addresses are unique across the system</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Download Template -->
                <div class="text-center">
                    <button type="button" 
                            wire:click="downloadTemplate"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-icon name="download" style="fas" class="w-4 h-4 mr-2" />
                        Download Import Template
                    </button>
                </div>

                <!-- Default Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Default Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Default Campus -->
                        <x-inputs.select-input 
                            id="default_campus_id"
                            wire:model="default_campus_id"
                            label="Default Campus"
                            placeholder="Select default campus"
                            :options="$campuses"
                            value-field="id"
                            text-field="name"
                            help="This will be used for students without campus specified in the file"
                            required />

                        <!-- Default Academic Year -->
                        <x-inputs.select-input 
                            id="default_academic_year_id"
                            wire:model="default_academic_year_id"
                            label="Default Academic Year"
                            placeholder="Select default academic year"
                            :options="$academicYears"
                            value-field="id"
                            text-field="formatted_description"
                            help="This will be used for students without academic year specified in the file"
                            required />
                    </div>
                </div>

                <!-- File Upload -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Upload File</h4>
                    <div class="space-y-4">
                        <!-- File Input -->
                        <x-inputs.file-input 
                            id="file"
                            wire:model="file"
                            label="Select File"
                            accept=".xlsx,.xls,.csv"
                            help="Choose an Excel or CSV file containing student data"
                            required />

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="file" class="text-sm text-gray-600">
                            <div class="flex items-center">
                                <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                                Processing file...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <div class="flex justify-end space-x-3">
                <x-buttons.secondary-button wire:click="$dispatch('closeModal')" type="button">
                    Cancel
                </x-buttons.secondary-button>
                <x-buttons.primary-button type="submit" :disabled="!$file">
                    <x-icon name="upload" style="fas" class="w-4 h-4 mr-2" />
                    Import Students
                </x-buttons.primary-button>
            </div>
        </x-modals.modal-footer>
    </form>
</div>
