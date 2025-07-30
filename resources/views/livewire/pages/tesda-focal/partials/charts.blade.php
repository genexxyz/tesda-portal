<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-lg mb-4">ISA</h3>
            <div class="flex">
                <div class="w-1/2">
                    <h4 class="font-semibold">Campuses</h4>
                    <ul>
    @foreach($isaCampuses as $campus => $percentage)
        @php
            $color = $isaColors[$loop->index] ?? '#3b82f6';
        @endphp
        <li class="flex items-center mb-1 text-sm">
            <span class="inline-block w-3 h-3 rounded-full mr-2" style="background: {{ $color }};"></span>
            <span>{{ $campus }}: <span class="font-semibold">{{ $percentage }}%</span></span>
        </li>
    @endforeach
</ul>
                </div>
                <div class="w-1/2">
                    <h4 class="font-semibold">Pass Rate</h4>
                    <canvas id="isa-pie-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-lg mb-4">Mandatory</h3>
            <div class="flex">
                <div class="w-1/2">
                    <h4 class="font-semibold">Campuses</h4>
                    <ul>
    @foreach($mandatoryCampuses as $campus => $percentage)
        @php
            $color = $mandatoryColors[$loop->index] ?? '#6366f1';
        @endphp
        <li class="flex items-center mb-1 text-sm">
            <span class="inline-block w-3 h-3 rounded-full mr-2" style="background: {{ $color }};"></span>
            <span>{{ $campus }}: <span class="font-semibold">{{ $percentage }}%</span></span>
        </li>
    @endforeach
</ul>
                </div>
                <div class="w-1/2">
                    <h4 class="font-semibold">Pass Rate</h4>
                    <canvas id="mandatory-pie-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', function () {
    // ISA Bar Chart
    const isaData = {
        labels: {!! json_encode(array_keys($isaCampuses)) !!},
        datasets: [{
            label: 'ISA Pass Rate (%)',
            data: {!! json_encode(array_values($isaCampuses)) !!},
            backgroundColor: {!! json_encode($isaColors) !!},
            maxBarThickness: 24,
        }]
    };
    new Chart(document.getElementById('isa-pie-chart'), {
        type: 'bar',
        data: isaData,
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: '%' }
                }
            }
        }
    });

    // Mandatory Bar Chart
    const mandatoryData = {
        labels: {!! json_encode(array_keys($mandatoryCampuses)) !!},
        datasets: [{
            label: 'Mandatory Pass Rate (%)',
            data: {!! json_encode(array_values($mandatoryCampuses)) !!},
            backgroundColor: {!! json_encode($mandatoryColors) !!},
            maxBarThickness: 24,
        }]
    };
    new Chart(document.getElementById('mandatory-pie-chart'), {
        type: 'bar',
        data: mandatoryData,
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: '%' }
                }
            }
        }
    });
    });
</script>