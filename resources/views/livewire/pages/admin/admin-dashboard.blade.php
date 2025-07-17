<div>
    <x-partials.header title="Admin Dashboard" />

    <div class="max-w-7xl mx-auto flex md:flex-row flex-col-reverse items-center justify-center gap-3">
        <div class="mt-6 bg-gray-300 rounded-lg p-6 w-2/3 md:h-58 h-auto">
            <div>
                <p class="font-bold text-gray-400  border-b-2 border-gray-400">Overview</p>
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 m-10">
                        <a href="{{ route('admin.students') }}"
                            class="rounded-lg border-1 border-gray-600 hover:shadow-lg cursor-pointer">
                            <h3 class="text-2xl font-semibold text-center m-3 mt-5 text-primary"><x-icon name="users"
                                    style="fas" class="mr-4 text-black" />{{ $totalStudents ?? 00 }}</h3>
                            <p class="text-xs text-center mb-3">TOTAL STUDENTS</p>
                        </a>
                        <a href="{{ route('admin.registrars') }}"
                            class="rounded-lg border-1 border-gray-600 hover:shadow-lg cursor-pointer">
                            <h3 class="text-2xl font-semibold text-center m-3 mt-5 text-primary"><x-icon
                                    name="user-graduate" style="fas"
                                    class="mr-4 text-black" />{{ $totalRegistrars ?? 00 }}
                            </h3>
                            <p class="text-xs text-center mb-3">TOTAL REGISTRARS</p>
                        </a>
                        <a href="{{ route('admin.program-heads') }}"
                            class="rounded-lg border-1 border-gray-600 hover:shadow-lg cursor-pointer">
                            <h3 class="text-2xl font-semibold text-center m-3 mt-5 text-primary"><x-icon name="user-tie"
                                    style="fas" class="mr-4 text-black" />{{ $totalProgramHeads ?? 00 }}</h3>
                            <p class="text-xs text-center mb-3">TOTAL PROGRAM HEADS</p>
                        </a>
                        <a href="{{ route('admin.campuses') }}"
                            class="rounded-lg border-1 border-gray-600 hover:shadow-lg cursor-pointer">
                            <h3 class="text-2xl font-semibold text-center m-3 mt-5 text-primary"><x-icon name="building"
                                    style="fas" class="mr-4 text-black" />{{ $totalCampuses ?? 00 }}</h3>
                            <p class="text-xs text-center mb-3">TOTAL CAMPUSES</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-6 bg-gray-300 rounded-lg py-12 md:w-1/3 w-auto h-58">
            <div class="flex flex-row items-center justify-center gap-3">
                <div>

                    <p class="text-black text-lg"><x-icon name="calendar" style="far"
                            class="text-primary mr-2" />{{ $date }}</p>
                    <p class="text-black text-lg"><x-icon name="clock" style="far"
                            class="text-primary mr-2" />{{ $time }}</p>
                </div>
                <div>
                    <p class="w-25 h-25 rounded-full border-12 border-primary flex items-center justify-center">
                        {{ $totalUsers ?? 00 }}</p>
                    <p>Total Users</p>
                </div>
            </div>

        </div>
    </div>



    <div class="max-w-7xl mx-auto flex md:flex-row flex-col items-center justify-center gap-3">
        <div class="mt-6 bg-gray-300 rounded-lg p-6 w-2/3">
            <div class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button wire:click="setActiveTab('campuses')"
                            class="{{ $activeTab === 'campuses' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <x-icon name="building" style="fas" class="w-5 h-5 mr-2" />
                                Campuses
                            </div>
                        </button>

                        <button wire:click="setActiveTab('courses')"
                            class="{{ $activeTab === 'courses' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <x-icon name="book" style="fas" class="w-5 h-5 mr-2" />
                                Courses
                            </div>
                        </button>
                        <button wire:click="setActiveTab('qualifications')"
                            class="{{ $activeTab === 'qualifications' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                            <div class="flex items-center">
                                <x-icon name="building" style="fas" class="w-5 h-5 mr-2" />
                                Qualifications
                            </div>
                        </button>
                    </nav>
                </div>
            </div>

            <div class="mt-6">
                @if ($activeTab === 'campuses')
                    
                        @foreach ($campuses as $campus)
                            <ul>
                                <li class="flex items-center bg-gray-300">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 mr-3"
                                        style="background-color: {{ $campus->color }}"></div>
                                    {{ $campus->name }}
                                </li>
                            </ul>
                        @endforeach
                @elseif($activeTab === 'courses')

                    @foreach ($courses as  $course)
                        <ul>
                            <li class="flex items-center bg-gray-300">
                                <div class="w-4 h-4 rounded-full border border-gray-300 mr-3"></div>
                                {{ $course->name }}
                            </li>
                        </ul>
                    @endforeach
                @elseif($activeTab === 'qualifications')
                    <livewire:pages.admin.assessment.assessors />
                @endif
            </div>

        </div>
        <div class="mt-6 bg-gray-300 rounded-lg py-12 md:w-1/3 w-auto h-58"></div>
    </div>
</div>
