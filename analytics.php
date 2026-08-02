<?php
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$total_users = $total_bookings = $total_revenue = 0;
$movie_labels = '["No Data"]';
$movie_counts = '[0]';
$dates = '["No Data"]';
$revenue = '[0]';

// Total Users
try {
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (Exception $e) {}

// Total Bookings
try {
    $total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
} catch (Exception $e) {}

// Total Revenue
try {
    $total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings")->fetchColumn();
} catch (Exception $e) {}

// Top 5 Movies by Bookings - Left Chart
try {
    $movie_data = $pdo->query("SELECT m.title, COUNT(b.id) as total_bookings
                                FROM bookings b
                                JOIN shows s ON b.show_id = s.id
                                JOIN movies m ON s.movie_id = m.id
                                GROUP BY m.id, m.title
                                ORDER BY total_bookings DESC
                                LIMIT 5")->fetchAll();

    if(!empty($movie_data)){
        $movie_labels = json_encode(array_column($movie_data, 'title'));
        $movie_counts = json_encode(array_column($movie_data, 'total_bookings'));
    }
} catch (Exception $e) {}

// Revenue per Movie - Right Chart
try {
    $revenue_per_movie = $pdo->query("SELECT m.title, COALESCE(SUM(b.total_amount), 0) AS revenue
                                      FROM movies m
                                      INNER JOIN shows s ON s.movie_id = m.id
                                      INNER JOIN bookings b ON b.show_id = s.id
                                      GROUP BY m.id, m.title
                                      HAVING SUM(b.total_amount) > 0
                                      ORDER BY revenue DESC")->fetchAll();

    if(!empty($revenue_per_movie)){
        $movie_titles_revenue = json_encode(array_column($revenue_per_movie, 'title'));
        $movie_revenue = json_encode(array_column($revenue_per_movie, 'revenue'));
    } else {
        $movie_titles_revenue = '["No Data"]';
        $movie_revenue = '[0]';
    }
} catch (Exception $e) {
    $movie_titles_revenue = '["No Data"]';
    $movie_revenue = '[0]';
}

// Last 7 Days Revenue - Optional
try {
    $last_7_days = $pdo->query("SELECT DATE(booking_date) as date, SUM(total_amount) as revenue
                                FROM bookings
                                WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                GROUP BY DATE(booking_date)
                                ORDER BY date ASC")->fetchAll();

    if(!empty($last_7_days)){
        $dates = json_encode(array_column($last_7_days, 'date'));
        $revenue = json_encode(array_column($last_7_days, 'revenue'));
    }
} catch (Exception $e) {}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0a0e27;
            color: white;
            padding: 20px;
        }
       .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
       .header h1 { font-size: 28px; }
       .btn {
            background: #00ff88;
            color: #0a0e27;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
       .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
       .card {
            background: #1a1f3a;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #2a2f4a;
        }
       .card h2 {
            font-size: 36px;
            color: #00ff88;
            margin-bottom: 5px;
        }
       .card p { color: #aaa; font-size: 14px; }
       .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
       .chart-card {
            background: #1a1f3a;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #2a2f4a;
        }
       .chart-card h3 {
            color: #00ff88;
            margin-bottom: 20px;
            font-size: 18px;
        }
       .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>📊 Analytics Dashboard</h1>
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="card">
            <h2><?php echo $total_users;?></h2>
            <p>Total Users</p>
        </div>
        <div class="card">
            <h2><?php echo $total_bookings;?></h2>
            <p>Total Bookings</p>
        </div>
        <div class="card">
            <h2>₹<?php echo number_format($total_revenue);?></h2>
            <p>Total Revenue</p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-grid">
        <!-- LEFT CHART -->
        <div class="chart-card">
            <h3>Top 5 Movies by Bookings</h3>
            <div class="chart-container">
                <canvas id="bookingsChart"></canvas>
            </div>
        </div>

        <!-- RIGHT CHART -->
        <div class="chart-card">
            <h3>Revenue by Movie</h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

<script>
// LEFT CHART - Bookings
new Chart(document.getElementById('bookingsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo $movie_labels;?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo $movie_counts;?>,
            backgroundColor: '#00ff88',
            borderColor: '#00cc66',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: 'white' },
                grid: { color: '#2a2f4a' }
            },
            x: {
                ticks: { color: 'white' },
                grid: { display: false }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

// RIGHT CHART - Revenue
new Chart(document.getElementById('revenueChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo $movie_titles_revenue;?>,
        datasets: [{
            label: 'Revenue ₹',
            data: <?php echo $movie_revenue;?>,
            backgroundColor: ['#00ff88', '#ff4444', '#ffcc00', '#00ccff', '#9966ff'],
            borderColor: '#0a0e27',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { display: false },
            y: { display: false }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: 'white',
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: '#1a1f3a',
                titleColor: '#00ff88',
                bodyColor: 'white',
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percent = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ₹' + context.parsed + ' (' + percent + '%)';
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>