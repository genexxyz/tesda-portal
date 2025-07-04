Primary Button

<x-primary-button type="submit" wire:click="save">
    Save Changes
</x-primary-button>

<x-primary-button href="/dashboard">
    Go to Dashboard
</x-primary-button>

<x-primary-button disabled>
    Processing...
</x-primary-button>




Basic link
<x-link href="/dashboard">
    Go to Dashboard
</x-link>

{{-- External link with icon --}}
<x-link href="https://example.com" external target="_blank">
    Visit Example Site
</x-link>

{{-- Different colors --}}
<x-link href="#" color="secondary">Secondary Link</x-link>
<x-link href="#" color="success">Success Link</x-link>
<x-link href="#" color="danger">Danger Link</x-link>

{{-- Disabled state --}}
<x-link href="#" disabled>
    Disabled Link
</x-link>

{{-- With custom classes --}}
<x-link href="#" class="text-lg underline">
    Custom Styled Link
</x-link>




Text Input
<x-text-input wire:model="email" label="Email" type="email" id="email" placeholder="Enter your email" required
    autofocus />



Password Input
<x-inputs.password-input wire:model="password" label="Password" id="password" placeholder="Enter your password"
    required />



Icon
<x-icon name="circle-notch" size="lg" color="blue-600" spin class="mr-2" />