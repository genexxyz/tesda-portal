<?php
$schoolInfo = app('schoolInfo');
?>
<nav x-data="{ 
    open: window.innerWidth >= 768, 
    userDropdown: false,
    isHovered: false,
    isMobile: window.innerWidth < 768,
    closeDropdown() {
        this.userDropdown = false;
    },
    toggleSidebar() {
        this.open = !this.open;
    },
    handleResize() {
        this.isMobile = window.innerWidth < 768;
        if (window.innerWidth >= 768) {
            this.open = true;
        } else {
            this.open = false;
        }
    }
}" x-init="() => {
    // Handle window resize
    window.addEventListener('resize', () => handleResize());
    
    // Handle Livewire navigation
    document.addEventListener('livewire:navigated', () => {
        // Reset dropdown state on navigation
        userDropdown = false;
    });
}" class="bg-white border-b border-gray-100 relative z-40">

    <style>
        /* Smooth transitions for all elements */
        * {
            box-sizing: border-box;
        }

        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .smooth-transition {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Backdrop blur for mobile overlay */
        .mobile-overlay {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.1);
        }

        /* Prevent body scroll when mobile menu is open */
        body.sidebar-open {
            overflow: hidden;
        }

        /* Main content adjustment */
        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 768px) {
            .sidebar-collapsed .main-content {
                margin-left: 4rem;
                /* 64px */
            }

            .sidebar-expanded .main-content {
                margin-left: 16rem;
                /* 256px */
            }
        }

        /* Smooth icon transitions */
        .icon-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Text fade transitions */
        .text-fade-enter {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
        }

        .text-fade-leave {
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <!-- Top Navigation Bar -->
    <div class="px-4 py-3 flex justify-between items-center transition-all duration-300 ease-in-out"
        x-bind:class="!isMobile && open ? (isHovered ? 'md:ml-64' : 'md:ml-16') : ''">
        <!-- Left side - Burger and Logo -->
        <div class="flex items-center">
            <button @click="toggleSidebar()"
                class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-md smooth-transition md:hidden"
                x-bind:aria-expanded="open" aria-label="Toggle navigation menu" type="button">
                <x-icon name="bars" style="fas" size="lg" class="icon-transition" />
            </button>
            <span class="ml-4 text-xl font-semibold text-primary">TESDA Portal</span>
        </div>

        <!-- Right side - User Info & Dropdown -->
        <div class="relative">

            <button @click="userDropdown = !userDropdown"
                class="flex items-center space-x-3 px-3 py-2 rounded-md hover:bg-gray-50 smooth-transition cursor-pointer"
                x-bind:aria-expanded="userDropdown" aria-haspopup="true" type="button">
                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                <x-icon name="chevron-down" style="fas" size="sm" class="icon-transition"
                    x-bind:class="userDropdown ? 'rotate-180' : ''" />
            </button>

            <!-- User Dropdown Menu -->
            <div x-show="userDropdown" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2" @click.away="closeDropdown()"
                @keydown.escape="closeDropdown()"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-50"
                style="display: none;" role="menu" aria-orientation="vertical">

                <a href="{{route('profile')}}" wire:navigate
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 smooth-transition rounded-md mx-1"
                    role="menuitem" @click="closeDropdown()">
                    <x-icon name="user" style="far" class="mr-2 w-4 h-4" />
                    Profile
                </a>

                <a href="{{route('change-password')}}" wire:navigate
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 smooth-transition rounded-md mx-1"
                    role="menuitem" @click="closeDropdown()">
                    <x-icon name="lock" styles="fas" class="mr-2 w-4 h-4" />
                    Change Password
                </a>

                <hr class="my-1 border-gray-200">

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 smooth-transition rounded-md cursor-pointer"
                        role="menuitem" @click="closeDropdown()">
                        <x-icon name="sign-out-alt" style="fas" class="mr-2 w-4 h-4" />
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay with Backdrop Blur -->
    <div x-show="open && isMobile" x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="toggleSidebar()"
        class="fixed inset-0 bg-gray-900 bg-opacity-20 mobile-overlay z-20" x-init="$watch('open', value => {
             if (isMobile) {
                 document.body.classList.toggle('sidebar-open', value);
             }
         })">
    </div>

    <!-- Sidebar Navigation -->
    <div class="fixed top-0 left-0 h-full bg-primary text-white z-30 transform transition-all duration-300 ease-in-out"
        :class="{
         'w-64': open && (isMobile || isHovered),
         'w-16': open && !isMobile && !isHovered,
         '-translate-x-full': !open
     }" @mouseenter="!isMobile && (isHovered = true)" @mouseleave="!isMobile && (isHovered = false)">

        <!-- Sidebar Header -->
        <div class="p-4 border-b border-primary-700 flex justify-center items-center h-16">
            <img src="{{url($schoolInfo['logo'] ?? 'storage/assets/img/default_logo.png')}}" alt="TESDA Logo"
                class="sidebar-transition rounded-full object-cover"
                x-bind:class="isHovered || isMobile ? 'h-12 w-12' : 'h-8 w-8'">
        </div>

        <!-- Navigation Links -->
        <div class="py-4 overflow-y-auto">
            @php
                $navigationItems = \App\Services\NavigationService::getNavigationForRole(Auth::user()->role->name);
            @endphp

            @foreach($navigationItems as $item)
                <x-partials.navigation-link :label="$item['label']" :route="$item['route']" :icon="$item['icon']"
                    :icon-style="$item['icon_style']" :active-routes="$item['active_routes']" />
            @endforeach
        </div>
    </div>
</nav>