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
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart colors
            const colors = {
                amber: 'rgb(245, 158, 11)',
                amberTransparent: 'rgba(245, 158, 11, 0.5)',
                blue: 'rgb(59, 130, 246)',
                blueTransparent: 'rgba(59, 130, 246, 0.5)',
                green: 'rgb(16, 185, 129)',
                greenTransparent: 'rgba(16, 185, 129, 0.5)'
            };
            
            // Chart data
            const types = @json($chartData['types']);
            const averages = @json($chartData['averages']);
            const counts = @json($chartData['counts']);
            
            // Average Points Chart
            const averageCtx = document.getElementById('averagePointsChart').getContext('2d');
            const averageChart = new Chart(averageCtx, {
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
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
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
                }
            });
            
            // Tournament Count Chart
            const countCtx = document.getElementById('tournamentCountChart').getContext('2d');
            const countChart = new Chart(countCtx, {
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
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
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
                }
            });
            
            // Skills Radar Chart
            const radarCtx = document.getElementById('skillsRadarChart').getContext('2d');
            const radarChart = new Chart(radarCtx, {
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
        });
    </script>
    @endpush
</x-app-layout>