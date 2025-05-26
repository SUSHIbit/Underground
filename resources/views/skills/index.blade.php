<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-400 leading-tight">
            {{ __('Tournament Skills') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-amber-800/20 overflow-hidden shadow-lg rounded-lg">
                <div class="p-4 sm:p-6 text-gray-200">
                    <h3 class="text-lg font-medium mb-6 text-amber-400">Your Tournament Skills</h3>
                    
                    <!-- Tournament Skills Overview -->
                    <div class="bg-gray-900/50 p-4 sm:p-6 rounded-lg border border-amber-800/20 mb-8">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Skills Overview</h4>
                        
                        <!-- Mobile Layout - Stacked Cards -->
                        <div class="block lg:hidden space-y-4">
                            @foreach($tournamentTypes as $type => $label)
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20">
                                    <h5 class="text-sm font-medium text-amber-400 mb-3">{{ $label }}</h5>
                                    
                                    @if(isset($stats[$type]))
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="text-center">
                                                <p class="text-xs text-gray-400">Avg Points</p>
                                                <p class="text-lg font-bold text-amber-500">{{ $stats[$type]['average'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-xs text-gray-400">Tournaments</p>
                                                <p class="text-lg font-bold text-amber-500">{{ $stats[$type]['count'] }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 text-center">No data available</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Layout - Grid -->
                        <div class="hidden lg:grid grid-cols-5 gap-4">
                            @foreach($tournamentTypes as $type => $label)
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20">
                                    <h5 class="text-sm font-medium text-amber-400 mb-2">{{ $label }}</h5>
                                    
                                    @if(isset($stats[$type]))
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-400">Avg Points:</span>
                                                <span class="text-sm font-medium">{{ $stats[$type]['average'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-400">Tournaments:</span>
                                                <span class="text-sm font-medium">{{ $stats[$type]['count'] }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 mt-2">No data available</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Chart Section -->
                    <div class="bg-gray-900/50 p-4 sm:p-6 rounded-lg border border-amber-800/20">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Skills Chart</h4>
                        
                        <!-- Mobile Charts - Stacked Vertically -->
                        <div class="block lg:hidden space-y-6">
                            <!-- Average Points Chart -->
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Average Points per Tournament Type</h5>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 h-48">
                                    <canvas id="averagePointsChartMobile"></canvas>
                                </div>
                            </div>
                            
                            <!-- Tournament Count Chart -->
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Tournament Participation Count</h5>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 h-48">
                                    <canvas id="tournamentCountChartMobile"></canvas>
                                </div>
                            </div>
                            
                            <!-- Skills Radar Chart -->
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Skills Radar</h5>
                                <div class="bg-gray-800 p-3 rounded-lg border border-amber-800/20 h-48 max-w-sm mx-auto">
                                    <canvas id="skillsRadarChartMobile"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Charts - Side by Side -->
                        <div class="hidden lg:block">
                            <div class="grid grid-cols-2 gap-6 mb-8">
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
                            <div>
                                <h5 class="text-sm font-medium text-amber-400 mb-2">Skills Radar</h5>
                                <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 h-64 max-w-lg mx-auto">
                                    <canvas id="skillsRadarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Points System Reference -->
                    <div class="mt-8 bg-gray-900/50 p-4 sm:p-6 rounded-lg border border-amber-800/20">
                        <h4 class="text-md font-medium mb-4 text-amber-400">Tournament Points System</h4>
                        
                        <!-- Mobile Layout - Cards -->
                        <div class="block lg:hidden space-y-3">
                            <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                <span class="text-sm">Winner (1st Place)</span>
                                <span class="text-sm text-amber-400 font-bold">20 points</span>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                <span class="text-sm">2nd Place</span>
                                <span class="text-sm text-amber-400 font-bold">15 points</span>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                <span class="text-sm">3rd Place</span>
                                <span class="text-sm text-amber-400 font-bold">10 points</span>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg border border-amber-800/20 flex justify-between items-center">
                                <span class="text-sm">Participation</span>
                                <span class="text-sm text-amber-400 font-bold">5 points</span>
                            </div>
                        </div>

                        <!-- Desktop Layout - Table -->
                        <div class="hidden lg:block overflow-x-auto bg-gray-800 rounded-lg border border-amber-800/20">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-amber-800/20">
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Placement</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-400 uppercase">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap text-sm">Winner (1st Place)</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-amber-400">20</td>
                                    </tr>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap text-sm">2nd Place</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-amber-400">15</td>
                                    </tr>
                                    <tr class="border-b border-amber-800/20">
                                        <td class="py-3 px-4 whitespace-nowrap text-sm">3rd Place</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-amber-400">10</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm">Participation</td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-amber-400">5</td>
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
            
            // Mobile chart options
            const mobileOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 8 },
                            maxRotation: 45
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 10 }
                        }
                    }
                }
            };

            // Desktop chart options
            const desktopOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 12 }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: { size: 12 }
                        }
                    }
                }
            };
            
            // Mobile Charts
            const averagePointsMobileElement = document.getElementById('averagePointsChartMobile');
            if (averagePointsMobileElement) {
                new Chart(averagePointsMobileElement, {
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
                        ...mobileOptions,
                        scales: {
                            ...mobileOptions.scales,
                            y: { ...mobileOptions.scales.y, max: 20 }
                        }
                    }
                });
            }
            
            const countMobileElement = document.getElementById('tournamentCountChartMobile');
            if (countMobileElement) {
                new Chart(countMobileElement, {
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
                        ...mobileOptions,
                        scales: {
                            ...mobileOptions.scales,
                            y: {
                                ...mobileOptions.scales.y,
                                ticks: { ...mobileOptions.scales.y.ticks, stepSize: 1 }
                            }
                        }
                    }
                });
            }

            const radarMobileElement = document.getElementById('skillsRadarChartMobile');
            if (radarMobileElement) {
                new Chart(radarMobileElement, {
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
                        elements: { line: { borderWidth: 2 } },
                        scales: {
                            r: {
                                angleLines: { color: 'rgba(255, 255, 255, 0.2)' },
                                grid: { color: 'rgba(255, 255, 255, 0.2)' },
                                pointLabels: {
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 8 }
                                },
                                ticks: {
                                    backdropColor: 'transparent',
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 8 }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            }

            // Desktop Charts
            const averagePointsElement = document.getElementById('averagePointsChart');
            if (averagePointsElement) {
                new Chart(averagePointsElement, {
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
                        ...desktopOptions,
                        scales: {
                            ...desktopOptions.scales,
                            y: { ...desktopOptions.scales.y, max: 20 }
                        }
                    }
                });
            }
            
            const countChartElement = document.getElementById('tournamentCountChart');
            if (countChartElement) {
                new Chart(countChartElement, {
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
                        ...desktopOptions,
                        scales: {
                            ...desktopOptions.scales,
                            y: {
                                ...desktopOptions.scales.y,
                                ticks: { ...desktopOptions.scales.y.ticks, stepSize: 1 }
                            }
                        }
                    }
                });
            }
            
            const radarChartElement = document.getElementById('skillsRadarChart');
            if (radarChartElement) {
                new Chart(radarChartElement, {
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
                        elements: { line: { borderWidth: 3 } },
                        scales: {
                            r: {
                                angleLines: { color: 'rgba(255, 255, 255, 0.2)' },
                                grid: { color: 'rgba(255, 255, 255, 0.2)' },
                                pointLabels: {
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 10 }
                                },
                                ticks: {
                                    backdropColor: 'transparent',
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 10 }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>