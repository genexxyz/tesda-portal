<div>
    <x-partials.header title="Registrar Dashboard" />

    <div class="mt-6 bg-gray-300 rounded-lg p-6 md:h-58 h-auto">
        <div>
            <p class="font-bold text-gray-500 border-b-2 border-gray-400 mb-6">Overview</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 m-10">
                <!-- TOTAL COURSES (switched position) -->
                <a href="{{ route('registrar.students') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="clipboard-list" style="fas" class="text-blue-500 text-3xl mr-2" />
                        <span class="text-3xl font-bold text-blue-500">{{ $totalCourses ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">TOTAL COURSES</p>
                </a>
                <!-- TOTAL STUDENTS (switched position) -->
                <a href="{{ route('registrar.students') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="users" style="fas" class="text-primary text-3xl mr-2" />
                        <span class="text-3xl font-bold text-primary">{{ $totalStudents ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">TOTAL STUDENTS</p>
                </a>
                <!-- ACTIVE STUDENTS (replaces total assessments) -->
                <a href="{{ route('registrar.students') }}"
                    class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="user-check" style="fas" class="text-green-500 text-3xl mr-2" />
                        <span class="text-3xl font-bold text-green-500">{{ $activeStudents ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">ACTIVE STUDENTS</p>
                </a>
                <!-- DROPPED STUDENTS (replaces campus) -->
                <a href="{{ route('registrar.students') }}" class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition flex flex-col items-center py-6 px-4">
                    <div class="flex items-center justify-center mb-2">
                        <x-icon name="user-times" style="fas" class="text-red-500 text-3xl mr-2" />
                        <span class="text-2xl font-bold text-red-700">{{ $droppedStudents ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 font-semibold">DROPPED</p>
                </a>
            </div>
        </div>
    </div>
    <div class="mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 font-semibold text-lg text-gray-700">
            Course Student Overview
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course Code</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Students</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dropped</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courseStats as $stat)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $stat['code'] }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $stat['name'] }}</td>
                            <td class="px-4 py-2 text-sm text-blue-700 font-bold">{{ $stat['total'] }}</td>
                            <td class="px-4 py-2 text-sm text-green-700 font-bold">{{ $stat['active'] }}</td>
                            <td class="px-4 py-2 text-sm text-red-700 font-bold">{{ $stat['dropped'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-400">No courses found for this campus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>