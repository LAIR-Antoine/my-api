// Retrieve data from the global chartData object
var daysOfWeek = window.chartData.pastWeekActivities.dates;
var swim = window.chartData.pastWeekActivities[0];
var bike = window.chartData.pastWeekActivities[1];
var run = window.chartData.pastWeekActivities[2];

var sumPerDay = [];
for (let i = 0; i < swim.length; i++) {
    sumPerDay.push(swim[i] + bike[i] + run[i]);
}
var maxDayLast = Math.ceil(Math.max(...sumPerDay));

var daysOfWeekActual = window.chartData.curWeekAct.dates;
var swimActual = window.chartData.curWeekAct[0];
var bikeActual = window.chartData.curWeekAct[1];
var runActual = window.chartData.curWeekAct[2];

var sumPerDayActual = [];
for (let i = 0; i < swimActual.length; i++) {
    sumPerDayActual.push(swimActual[i] + bikeActual[i] + runActual[i]);
}
var maxDayActual = Math.ceil(Math.max(...sumPerDayActual));
var maxDay = Math.max(maxDayLast, maxDayActual);

// Chart 1: Last Week
var ctx = document.getElementById('sportTimeChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: daysOfWeek,
        datasets: [
            { label: 'Natation', data: swim, backgroundColor: 'rgba(69, 148, 209, 1)', stack: 'stack1' },
            { label: 'Vélo', data: bike, backgroundColor: 'rgba(100, 217, 208, 1)', stack: 'stack1' },
            { label: 'Course', data: run, backgroundColor: 'rgba(255, 196, 0, 1)', stack: 'stack1' }
        ]
    },
    options: {
        scales: {
            x: { stacked: true, ticks: { autoSkip: false, maxRotation: 0, minRotation: 0 }},
            y: { stacked: true, max: maxDay, beginAtZero: true, ticks: { stepSize: 1, callback: value => value + 'h' }}
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: context => {
                        let datasetLabel = context.dataset.label || '';
                        let value = context.raw;
                        let hours = Math.floor(value);
                        let minutes = ('0' + Math.floor((value - hours) * 60)).slice(-2);
                        return `${datasetLabel}: ${hours}h${minutes}'`;
                    }
                }
            }
        }
    }
});

// Chart 2: Current Week
var ctx2 = document.getElementById('sportTimeChart2').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: daysOfWeekActual,
        datasets: [
            { label: 'Natation', data: swimActual, backgroundColor: 'rgba(69, 148, 209, 1)', stack: 'stack1' },
            { label: 'Vélo', data: bikeActual, backgroundColor: 'rgba(100, 217, 208, 1)', stack: 'stack1' },
            { label: 'Course', data: runActual, backgroundColor: 'rgba(255, 196, 0, 1)', stack: 'stack1' }
        ]
    },
    options: {
        scales: {
            x: { stacked: true, ticks: { autoSkip: false, maxRotation: 0, minRotation: 0 }},
            y: { stacked: true, max: maxDay, beginAtZero: true, ticks: { stepSize: 1, callback: value => value + 'h' }}
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: context => {
                        let datasetLabel = context.dataset.label || '';
                        let value = context.raw;
                        let hours = Math.floor(value);
                        let minutes = ('0' + Math.floor((value - hours) * 60)).slice(-2);
                        return `${datasetLabel}: ${hours}h${minutes}'`;
                    }
                }
            }
        }
    }
});

// Chart 3: Last 5 Weeks
var weekOfYear = window.chartData.fiveLastWeeks.dates;
var swimWeek = window.chartData.fiveLastWeeks[0];
var bikeWeek = window.chartData.fiveLastWeeks[1];
var runWeek = window.chartData.fiveLastWeeks[2];

var ctx3 = document.getElementById('sportTimeChart3').getContext('2d');
new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: weekOfYear,
        datasets: [
            { barThickness: 30, label: 'Natation', data: swimWeek, backgroundColor: 'rgba(69, 148, 209, 1)', stack: 'stack1' },
            { barThickness: 30, label: 'Vélo', data: bikeWeek, backgroundColor: 'rgba(100, 217, 208, 1)', stack: 'stack1' },
            { barThickness: 30, label: 'Course', data: runWeek, backgroundColor: 'rgba(255, 196, 0, 1)', stack: 'stack1' }
        ]
    },
    options: {
        scales: {
            x: { stacked: true, ticks: { autoSkip: false, maxRotation: 0, minRotation: 0 }},
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 2, callback: value => value + 'h' }}
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: context => {
                        let datasetLabel = context.dataset.label || '';
                        let value = context.raw;
                        let hours = Math.floor(value);
                        let minutes = ('0' + Math.floor((value - hours) * 60)).slice(-2);
                        return `${datasetLabel}: ${hours}h${minutes}'`;
                    }
                }
            }
        }
    }
});


var swimLastYearValues = Object.values(window.chartData.swimLastYear);
var swimThisYearValues = Object.values(window.chartData.swimThisYear);

var maxSwimLastYear = Math.max(...swimLastYearValues);
var maxSwimThisYear = Math.max(...swimThisYearValues);
var overallSwimMax = Math.max(maxSwimLastYear, maxSwimThisYear);
var swimMax = Math.ceil(overallSwimMax / 5) * 5;

var ctx4 = document.getElementById('sportTimeChart4').getContext('2d');
var chart4 = new Chart(ctx4, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Natation - 2024',
            data: window.chartData.swimLastYear,
            backgroundColor: 'rgba(69, 148, 209, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: swimMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});

var ctx5 = document.getElementById('sportTimeChart5').getContext('2d');
var chart5 = new Chart(ctx5, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Natation - 2025',
            data: window.chartData.swimThisYear,
            backgroundColor: 'rgba(69, 148, 209, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: swimMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});

var bikeLastYearValues = Object.values(window.chartData.bikeLastYear);
var bikeThisYearValues = Object.values(window.chartData.bikeThisYear);

var maxBikeLastYear = Math.max(...bikeLastYearValues);
var maxBikeThisYear = Math.max(...bikeThisYearValues);
var overallBikeMax = Math.max(maxBikeLastYear, maxBikeThisYear);
var bikeMax = Math.ceil(overallBikeMax / 100) * 100;

var ctx6 = document.getElementById('sportTimeChart6').getContext('2d');
var chart6 = new Chart(ctx6, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Vélo - 2024',
            data: window.chartData.bikeLastYear,
            backgroundColor: 'rgba(100, 217, 208, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: bikeMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});

var ctx7 = document.getElementById('sportTimeChart7').getContext('2d');
var chart7 = new Chart(ctx7, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Vélo - 2025',
            data: window.chartData.bikeThisYear,
            backgroundColor: 'rgba(100, 217, 208, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: bikeMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});

var runLastYearValues = Object.values(window.chartData.runLastYear);
var runThisYearValues = Object.values(window.chartData.runThisYear);

var maxRunLastYear = Math.max(...runLastYearValues);
var maxRunThisYear = Math.max(...runThisYearValues);
var overallRunMax = Math.max(maxRunLastYear, maxRunThisYear);
var runMax = Math.ceil(overallRunMax / 50) * 50;

var ctx8 = document.getElementById('sportTimeChart8').getContext('2d');
var chart8 = new Chart(ctx8, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Course - 2024',
            data: window.chartData.runLastYear,
            backgroundColor: 'rgba(255, 196, 0, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: runMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});



var ctx9 = document.getElementById('sportTimeChart9').getContext('2d');
var chart9 = new Chart(ctx9, {
    type: 'bar',
    data: {
        //labels: monthOfYear,
        datasets: [{
            barThickness: 20,
            label: 'Course - 2025',
            data: window.chartData.runThisYear,
            backgroundColor: 'rgba(255, 196, 0, 1)',
            stack: 'stack1' // Assign a stack name for Sport 1

        }
        ]
    },
    options: {
        scales: {
            x: {
                stacked: true,
                ticks: {
                    autoSkip: false, // Prevent automatic label skipping
                    maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                    minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: runMax,
                ticks: {
                    callback: function(value) {
                        return value + ' km';
                    }
                }
            }
        }
    }
});
