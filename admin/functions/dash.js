function openNotification() {
    document.getElementById("notification").classList.toggle("shownotification");
    console.log("not clicked")
}




function toggleNav() {
    var sidenav = document.getElementById("mySidenav");
    var main = document.getElementById("main");


    if (sidenav.style.width === "200px" || sidenav.style.width === "") {
        sidenav.style.width = "0px";
        main.style.marginLeft = "0px";
        main.style.width = "100vw"
        sidenav.style.left = "-20px"
    } else {
        sidenav.style.width = "200px";
        main.style.marginLeft = "200px";
        main.style.width = "calc(100vw - 200px)"
        sidenav.style.left = "0px"
    }
}




const ctx = document.getElementById('lineChart');

// Create gradient
const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(74, 222, 128, 0.4)');
gradient.addColorStop(1, 'rgba(74, 222, 128, 0.05)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Value',
            data: [8000, 7500, 8500, 5000, 10000, 12000, 7000, 10000, 9500, 8500, 9000, 11500],
            borderColor: '#4ade80',
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointBackgroundColor: '#4ade80',
            pointHoverBackgroundColor: '#4ade80',
            pointBorderColor: '#fff',
            pointHoverBorderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return '₦' + context.raw.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#e5e5e5',
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        return '₦' + value / 1000 + 'k';
                    },
                    stepSize: 2000
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
