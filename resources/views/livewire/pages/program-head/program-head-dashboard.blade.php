<div>
    <x-partials.header title="Program Head Dashboard" />

    <div class="mt-6 bg-gray-300 rounded-lg p-6 md:h-58 h-auto">
        <div>
            <p class="font-bold text-gray-500 border-b-2 border-gray-400 mb-6">Overview</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 m-10">
                <a href="{{ route('program-head.students') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="users" style="fas" class="text-primary text-3xl mr-2" />
                        <span class="text-3xl font-bold text-primary">{{ $totalStudents ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">TOTAL STUDENTS</p>
                </a>
                <a href="{{ route('program-head.students') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="clipboard-list" style="fas" class="text-blue-500 text-3xl mr-2" />
                        <span class="text-3xl font-bold text-blue-500">{{ $totalCourses ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">TOTAL COURSES</p>
                </a>
                <a href="{{ route('program-head.assessments') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="clipboard-list" style="fas" class="text-green-500 text-3xl mr-2" />
                        <span class="text-3xl font-bold text-green-500">{{ $totalAssessments ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">TOTAL ASSESSMENTS</p>
                </a>
                <div class="bg-white rounded-xl border border-gray-200 shadow flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="building" style="fas" class="text-yellow-500 text-3xl mr-2" />
                        <span class="text-2xl font-bold text-yellow-700">{{ $campus ?? 'NO CAMPUS' }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">CAMPUS</p>
                </div>
            </div>
        </div>
        <!-- Upcoming Assessments section remains unchanged -->
        <div class="mt-6">
            <div>
                <p class="text-black text-lg flex items-center font-semibold mb-2">
                    <x-icon name="calendar" style="far" class="text-primary mr-2" />
                    Upcoming Assessments
                </p>
                <div>
                    <div class="flex overflow-x-auto gap-6 m-10 pb-2">
                        @forelse($upcomingAssessments as $assessment)
                            <div class="min-w-[320px] max-w-xs bg-white p-6 rounded-xl shadow-lg border border-blue-100 flex flex-col items-start gap-4 hover:shadow-xl transition">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white text-xs font-bold
                                    {{ ($assessment['type'] ?? '') === 'ISA' ? 'bg-blue-500' : (($assessment['type'] ?? '') === 'MANDATORY' ? 'bg-red-500' : 'bg-gray-400') }}">
                                    <x-icon name="clipboard-list" style="fas" class="text-white" />
                                    {{ $assessment['type'] ?? 'N/A' }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <x-icon name="calendar-day" style="fas" class="text-green-500" />
                                    <span class="font-semibold text-sm text-gray-700">{{ $assessment['date'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-icon name="book" style="fas" class="text-blue-400" />
                                    <span class="font-semibold text-sm text-blue-900">{{ $assessment['course'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-icon name="certificate" style="fas" class="text-yellow-500" />
                                    <span class="text-sm text-yellow-800">{{ $assessment['qualification'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center text-gray-500 py-8">
                                <x-icon name="calendar-times" style="fas" class="text-3xl mb-2 text-gray-400" />
                                <p>No upcoming assessments found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>