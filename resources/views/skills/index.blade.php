<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Skills') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-6 text-gray-200">
                    <h3 class="text-lg font-medium mb-6 text-amber-400">Your Tournament Skills</h3>
                    
                    <!-- Tournament Skills Overview -->
                    <div class="bg-gray-900/50 p-6 rounded-lg border border-amber-800/20 mb-8">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Skills Overview</h4>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
                            @foreach($tournamentTypes as $type => $label)
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20">
                                    <h5 class="text-sm font-medium text-amber-400 mb-2">{{ $label }}</h5>
                                    
                                    @if(isset($stats[$type]))
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs text-gray-400">Average Points:</span>
                                            <span class="text-sm font-medium">{{ $stats[$type]['average'] }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-400">Tournaments:</span>
                                            <span class="text-sm font-medium">{{ $stats[$type]['count'] }}</span>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 mt-2">No data available</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Chart Section -->
                    <div class="bg-gray-900/50 p-6 rounded-lg border border-amber-800/20">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Skills Chart</h4>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Average Points Chart -->
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Average Points per Tournament Type</h5>
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 h-64">
                                    <canvas id="averagePointsChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Tournament Count Chart -->
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Tournament Participation Count</h5>
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 h-64">
                                    <canvas id="tournamentCountChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Radar Chart for Skills -->
                        <div class="mt-8">
                            <h5 class="text-sm font-medium text-amber-400 mb-2">Skills Radar</h5>
                            <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 h-64 max-w-lg mx-auto">
                                <canvas id="skillsRadarChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Points System Reference -->
                    <div class="mt-8 bg-gray-900/50 p-6 rounded-lg border border-amber-800/20">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Tournament Points System</h4>
                        
                        <div class="overflow-x-auto bg-gray-800 rounded-lg border border-amber-800/20">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-amber-800/20">
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Placement</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap">Winner (1st)</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-amber-400">20</td>
                                    </tr>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap">2nd Place</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-amber-400">15</td>
                                    </tr>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap">3rd Place</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-amber-400">10</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 whitespace-nowrap">Participation</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-amber-400">5</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure we load Chart.js properly first
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded. Please check your script includes.');
                return;
            }
            
            // Chart data
            const types = @json(array_values($tournamentTypes));
            const averages = [];
            const counts = [];
            
            // Populate data arrays from PHP stats
            @foreach($tournamentTypes as $type => $label)
                averages.push({{ isset($stats[$type]) ? $stats[$type]['average'] : 0 }});
                counts.push({{ isset($stats[$type]) ? $stats[$type]['count'] : 0 }});
            @endforeach
            
            // Chart colors
            const colors = {
                amber: 'rgb(245, 158, 11)',
                amberTransparent: 'rgba(245, 158, 11, 0.5)',
                blue: 'rgb(59, 130, 246)',
                blueTransparent: 'rgba(59, 130, 246, 0.5)',
                green: 'rgb(16, 185, 129)',
                greenTransparent: 'rgba(16, 185, 129, 0.5)'
            };
            
            // Common chart options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        }
                    }
                }
            };
            
            // Average Points Chart
            const averagePointsElement = document.getElementById('averagePointsChart');
            if (averagePointsElement) {
                const averageChart = new Chart(averagePointsElement, {
                    type: 'bar',
                    data: {
                        labels: types,
                        datasets: [{
                            label: 'Average Points',
                            data: averages,
                            backgroundColor: colors.amberTransparent,
                            borderColor: colors.amber,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            ...commonOptions.scales,
                            y: {
                                ...commonOptions.scales.y,
                                max: 20
                            }
                        }
                    }
                });
            } else {
                console.error('Average Points Chart canvas element not found');
            }
            
            // Tournament Count Chart
            const countChartElement = document.getElementById('tournamentCountChart');
            if (countChartElement) {
                const countChart = new Chart(countChartElement, {
                    type: 'bar',
                    data: {
                        labels: types,
                        datasets: [{
                            label: 'Tournaments Participated',
                            data: counts,
                            backgroundColor: colors.blueTransparent,
                            borderColor: colors.blue,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            ...commonOptions.scales,
                            y: {
                                ...commonOptions.scales.y,
                                ticks: {
                                    ...commonOptions.scales.y.ticks,
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Tournament Count Chart canvas element not found');
            }
            
            // Skills Radar Chart
            const radarChartElement = document.getElementById('skillsRadarChart');
            if (radarChartElement) {
                const radarChart = new Chart(radarChartElement, {
                    type: 'radar',
                    data: {
                        labels: types,
                        datasets: [{
                            label: 'Average Points',
                            data: averages,
                            backgroundColor: colors.greenTransparent,
                            borderColor: colors.green,
                            pointBackgroundColor: colors.green,
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: colors.green,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        elements: {
                            line: {
                                borderWidth: 3
                            }
                        },
                        scales: {
                            r: {
                                angleLines: {
                                    color: 'rgba(255, 255, 255, 0.2)'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.2)'
                                },
                                pointLabels: {
                                    color: 'rgba(255, 255, 255, 0.7)'
                                },
                                ticks: {
                                    backdropColor: 'transparent',
                                    color: 'rgba(255, 255, 255, 0.7)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgba(255, 255, 255, 0.7)'
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Skills Radar Chart canvas element not found');
            }
        });
    </script>
</x-app-layout>