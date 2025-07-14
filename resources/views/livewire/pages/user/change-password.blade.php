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
                <!-- Success/Error Messages -->
                @if (session()->has('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-icon name="check-circle" style="fas" class="w-5 h-5 text-green-600 mr-2" />
                            <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <x-icon name="exclamation-circle" style="fas" class="w-5 h-5 text-red-600 mr-2" />
                            <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

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
                    
                    <!-- Password Requirements -->
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-start">
                            <x-icon name="info-circle" style="fas" class="h-4 w-4 text-blue-600 mr-2 mt-0.5" />
                            <div>
                                <h4 class="text-xs font-medium text-blue-800 mb-2">Password Requirements</h4>
                                <ul class="text-xs text-blue-700 space-y-1">
                                    <li class="flex items-center {{ strlen($new_password) >= 8 ? 'text-green-600' : '' }}">
                                        <x-icon name="{{ strlen($new_password) >= 8 ? 'check' : 'circle' }}" style="fas" class="h-3 w-3 mr-1" />
                                        At least 8 characters
                                    </li>
                                    <li class="flex items-center {{ preg_match('/[A-Za-z]/', $new_password) ? 'text-green-600' : '' }}">
                                        <x-icon name="{{ preg_match('/[A-Za-z]/', $new_password) ? 'check' : 'circle' }}" style="fas" class="h-3 w-3 mr-1" />
                                        Contains letters
                                    </li>
                                    <li class="flex items-center {{ preg_match('/[0-9]/', $new_password) ? 'text-green-600' : '' }}">
                                        <x-icon name="{{ preg_match('/[0-9]/', $new_password) ? 'check' : 'circle' }}" style="fas" class="h-3 w-3 mr-1" />
                                        Contains numbers
                                    </li>
                                    <li class="flex items-center {{ preg_match('/[^A-Za-z0-9]/', $new_password) ? 'text-green-600' : '' }}">
                                        <x-icon name="{{ preg_match('/[^A-Za-z0-9]/', $new_password) ? 'check' : 'circle' }}" style="fas" class="h-3 w-3 mr-1" />
                                        Contains special characters
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                        wire:loading.attr="disabled"
                        class="px-6 py-2">
                        <span wire:loading.remove class="flex items-center">
                            <x-icon name="save" style="fas" class="w-4 h-4 mr-2" />
                            Change Password
                        </span>
                        <span wire:loading class="flex items-center">
                            <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                            Updating...
                        </span>
                    </x-buttons.primary-button>
                </div>
            </form>
        </div>

            <!-- Security Information Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <x-icon name="shield-alt" style="fas" class="w-5 h-5 mr-2 text-green-600" />
                        Account Security
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Security Tips</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <x-icon name="check-circle" style="fas" class="h-4 w-4 text-green-500 mr-2 mt-0.5" />
                                    Use a unique password for this account
                                </li>
                                <li class="flex items-start">
                                    <x-icon name="check-circle" style="fas" class="h-4 w-4 text-green-500 mr-2 mt-0.5" />
                                    Change your password regularly
                                </li>
                                <li class="flex items-start">
                                    <x-icon name="check-circle" style="fas" class="h-4 w-4 text-green-500 mr-2 mt-0.5" />
                                    Don't share your password with others
                                </li>
                                <li class="flex items-start">
                                    <x-icon name="check-circle" style="fas" class="h-4 w-4 text-green-500 mr-2 mt-0.5" />
                                    Use a password manager when possible
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Account Information</h4>
                            <dl class="text-sm text-gray-600 space-y-2">
                                <div class="flex justify-between">
                                    <dt>Last Updated:</dt>
                                    <dd class="font-medium">{{ Auth::user()->updated_at->diffForHumans() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Account Status:</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                            {{ Auth::user()->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(Auth::user()->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Role:</dt>
                                    <dd class="font-medium">{{ Auth::user()->role?->name ?? 'No role' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
