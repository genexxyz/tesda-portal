<div class="mt-6 max-w-7xl mx-auto">
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-inputs.filter-select
            wire:model.live="examTypeFilter"
            placeholder="All Exam Types"
            icon="clipboard-list"
            :options="$examTypes"
        />
        <x-inputs.filter-select 
                    id="courseFilter"
                    wire:model.live="courseFilter"
                    placeholder="All Courses"
                    icon="graduation-cap"
                    :options="$courses" />
        <x-inputs.filter-select 
                    id="qualificationFilter"
                    wire:model.live="qualificationFilter"
                    placeholder="{{ $courseFilter ? 'All Qualification Types' : 'Select Course First' }}"
                    icon="award"
                    :options="$qualificationTypes"
                    textField="description"
                    :disabled="!$courseFilter" />
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            @foreach($overviewData as $group)
    <tr class="bg-gray-200 font-bold">
        <td colspan="6">
            {{ $group['course'] }} - {{ $group['qualification_type'] }} - {{ $group['exam_type'] }}
        </td>
    </tr>
    @foreach($group['campuses'] as $campus => $campusData)
        <tr>
            <td>{{ $campus }}</td>
            <td>{{ $group['course'] }}</td>
            <td>{{ $group['qualification_type'] }}</td>
            <td>{{ $group['exam_type'] }}</td>
            <td>{{ $campusData['total_students'] }}</td>
            <td>{{ $campusData['pass_rate'] }}%</td>
        </tr>
    @endforeach
    <tr class="bg-gray-100 font-semibold">
        <td>TOTAL</td>
        <td>{{ $group['course'] }}</td>
        <td>{{ $group['qualification_type'] }}</td>
        <td>{{ $group['exam_type'] }}</td>
        <td>{{ $group['totals']['total_students'] }}</td>
        <td>{{ $group['totals']['pass_rate'] }}%</td>
    </tr>
@endforeach
            </tbody>
        </table>
</div>
