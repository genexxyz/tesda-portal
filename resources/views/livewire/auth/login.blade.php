<div class="min-h-100 md:min-h-screen flex flex-col sm:justify-center items-center pt-6 md:pt-0 px-4 h-20 bg-accent rounded-md mb-20 md:mb-0">
    <div>
        <div class="flex justify-center">
            <h1 class="text-2xl font-bold">LOGIN</h1>
        </div>
        <div class="w-80 mt-15 ">
            <form wire:submit="login">
                <x-inputs.text-input 
                    wire:model="email" 
                    label="Email" 
                    type="email" 
                    id="email"
                    placeholder="Enter your email..." 
                    required 
                    autofocus 
                />
                <x-inputs.password-input 
                    wire:model="password" 
                    label="Password" 
                    id="password"
                    placeholder="Enter your password..." 
                    required 
                />
                <div class="w-full flex justify-end mb-5">
                    <x-buttons.link 
                        href="#" 
                        color="secondary" 
                        class="text-xs font-semibold"
                    >
                        Forgot your password?
                    </x-buttons.link>
                </div>

                <x-buttons.primary-button type="submit">
                    Login
                </x-buttons.primary-button>
            </form>
        </div>
    </div>
</div>