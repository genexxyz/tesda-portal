<div>
    <x-partials.header title="Change Password" breadcrumb="Change Password" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="max-w-2xl mx-auto">
            <!-- Change Password Card -->
            <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <x-icon name="lock" style="fas" class="w-5 h-5 mr-2 text-blue-600" />
                    Change Your Password
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    Update your password to keep your account secure. Make sure your new password is strong and unique.
                </p>
            </div>

            <form wire:submit="changePassword" class="p-6">
                

                <!-- Current Password -->
                <x-inputs.password-input 
                    id="current_password"
                    wire:model="current_password"
                    label="Current Password"
                    placeholder="Enter your current password"
                    autocomplete="current-password" />

                <!-- New Password -->
                <div class="mb-6">
                    <x-inputs.password-input 
                        id="new_password"
                        wire:model="new_password"
                        label="New Password"
                        placeholder="Enter your new password"
                        autocomplete="new-password" />
                    
                    
                </div>

                <!-- Confirm New Password -->
                <div class="mb-6">
                    <x-inputs.password-input 
                        id="new_password_confirmation"
                        wire:model="new_password_confirmation"
                        label="Confirm New Password"
                        placeholder="Confirm your new password"
                        autocomplete="new-password" />
                    
                    <!-- Password Match Indicator -->
                    @if($new_password && $new_password_confirmation)
                        <div class="mt-2 flex items-center text-xs">
                            @if($new_password === $new_password_confirmation)
                                <x-icon name="check-circle" style="fas" class="w-4 h-4 text-green-500 mr-1" />
                                <span class="text-green-600">Passwords match</span>
                            @else
                                <x-icon name="times-circle" style="fas" class="w-4 h-4 text-red-500 mr-1" />
                                <span class="text-red-600">Passwords do not match</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-4">
                    <button 
                        type="button"
                        wire:click="resetForm"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-icon name="times" style="fas" class="w-4 h-4 mr-2" />
                        Reset Form
                    </button>
                    
                    <x-buttons.primary-button 
                        type="submit"
                        class="px-6 py-2">
                        <span class="flex items-center">
                            <x-icon name="save" style="fas" class="w-4 h-4 mr-2" />
                            Change Password
                        </span>
                        
                    </x-buttons.primary-button>
                </div>
            </form>
        </div>

            
        </div>
    </div>
</div>
