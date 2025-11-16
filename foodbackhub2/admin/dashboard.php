<?php

require_once 'auth_check.php';

include_once __DIR__ . '/../db.php';




$total = $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE status='Pending'")->fetch_assoc()['count'];
$resolved = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE status='Resolved'")->fetch_assoc()['count'];
$avgRating = $conn->query("SELECT AVG(rating) as avg FROM feedback")->fetch_assoc()['avg'];


$ratingDist = $conn->query("
    SELECT rating, COUNT(*) as count 
    FROM feedback 
    GROUP BY rating 
    ORDER BY rating DESC
");


$ratingByDay = $conn->query("
    SELECT meal_day, AVG(rating) as avg_rating, COUNT(*) as total_feedback
    FROM feedback 
    GROUP BY meal_day 
    ORDER BY FIELD(meal_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
");


$ratingByMealTime = $conn->query("
    SELECT meal_time, AVG(rating) as avg_rating, COUNT(*) as total_feedback
    FROM feedback 
    GROUP BY meal_time 
    ORDER BY FIELD(meal_time, 'Breakfast', 'Lunch', 'Dinner')
");


$res = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT 200");


$worstRated = $conn->query("
    SELECT meal_day, meal_time, AVG(rating) as avg_rating, COUNT(*) as count
    FROM feedback
    GROUP BY meal_day, meal_time
    HAVING COUNT(*) >= 2
    ORDER BY avg_rating ASC
    LIMIT 5
");


$bestRated = $conn->query("
    SELECT meal_day, meal_time, AVG(rating) as avg_rating, COUNT(*) as count
    FROM feedback
    GROUP BY meal_day, meal_time
    HAVING COUNT(*) >= 2
    ORDER BY avg_rating DESC
    LIMIT 5
");


$ratingDistData = [];
while($row = $ratingDist->fetch_assoc()) {
    $ratingDistData[] = $row;
}

$dayRatingData = [];
while($row = $ratingByDay->fetch_assoc()) {
    $dayRatingData[] = $row;
}

$mealTimeData = [];
while($row = $ratingByMealTime->fetch_assoc()) {
    $mealTimeData[] = $row;
}

$worstRatedData = [];
while($row = $worstRated->fetch_assoc()) {
    $worstRatedData[] = $row;
}

$bestRatedData = [];
while($row = $bestRated->fetch_assoc()) {
    $bestRatedData[] = $row;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard — Food-back Hub</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>

<div class="admin-wrapper">

    <header class="admin-header">
        <div class="header-content">
            <div>
                <h1>🎯 Admin Dashboard</h1>
                <p>Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
            </div>
            <div class="header-actions">
                <a href="manage_menu.php" class="btn-menu">
                    <span>🍽️</span>
                    <span>Manage Menu</span>
                </a>

                <a href="../index.html" class="btn-secondary">
                    <span>🏠</span> Main Site
                </a>
                <a href="logout.php" class="btn-logout">
                    <span>🚪</span> Logout
                </a>
            </div>
        </div>
    </header>



  
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <h3><?= $total ?></h3>
                <p>Total Feedback</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <h3><?= $pending ?></h3>
                <p>Pending</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?= $resolved ?></h3>
                <p>Resolved</p>
            </div>
        </div>

        <div class="stat-card highlight-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-info">
                <h3><?= number_format($avgRating, 2) ?>/5</h3>
                <p>Average Rating</p>
            </div>
        </div>
    </div>

    <div class="charts-section">
  

        <div class="chart-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>📊 Rating Distribution</h3>
                    <p>How students rated the food</p>
                </div>
                <canvas id="ratingPieChart"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>📈 Average Rating by Day</h3>
                    <p>Weekly performance trends</p>
                </div>
                <canvas id="dayRatingChart"></canvas>
            </div>
        </div>

    
        <div class="chart-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>🍽️ Rating by Meal Time</h3>
                    <p>Performance across meal types</p>
                </div>
                <canvas id="mealTimeChart"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>🏆 Best & Worst Rated Meals</h3>
                    <p>Top and bottom performers</p>
                </div>
                <div class="rating-lists">
                    <div class="best-rated-list">
                        <h4>✅ Best Rated</h4>
                        <?php foreach($bestRatedData as $meal): ?>
                        <div class="meal-rating-item best">
                            <div class="meal-info">
                                <strong><?= htmlspecialchars($meal['meal_day']) ?> - <?= htmlspecialchars($meal['meal_time']) ?></strong>
                                <small><?= $meal['count'] ?> reviews</small>
                            </div>
                            <div class="meal-rating">
                                <span class="rating-badge best"><?= number_format($meal['avg_rating'], 1) ?> ⭐</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="worst-rated-list">
                        <h4>⚠️ Needs Improvement</h4>
                        <?php foreach($worstRatedData as $meal): ?>
                        <div class="meal-rating-item worst">
                            <div class="meal-info">
                                <strong><?= htmlspecialchars($meal['meal_day']) ?> - <?= htmlspecialchars($meal['meal_time']) ?></strong>
                                <small><?= $meal['count'] ?> reviews</small>
                            </div>
                            <div class="meal-rating">
                                <span class="rating-badge worst"><?= number_format($meal['avg_rating'], 1) ?> ⭐</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="table-card">
        <div class="table-header">
            <h2>📋 Recent Feedback</h2>
        </div>

        <div class="table-wrap">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Meal</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Photo</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($res && $res->num_rows > 0): ?>
                    <?php while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><span class="id-badge">#<?= htmlspecialchars($row['id']) ?></span></td>

                        <td>
                            <div class="student-info">
                                <strong><?= htmlspecialchars($row['name'] ?? '—') ?></strong>
                                <small><?= htmlspecialchars($row['student_id'] ?? 'N/A') ?></small>
                            </div>
                        </td>

                        <td>
                            <div class="meal-info">
                                <strong><?= htmlspecialchars($row['meal_day']) ?></strong>
                                <small><?= htmlspecialchars($row['meal_time']) ?></small>
                            </div>
                        </td>

                        <td>
                            <div class="rating-display">
                                <?php 
                                $rating = intval($row['rating']);
                                for($i = 1; $i <= 5; $i++): 
                                ?>
                                    <span class="<?= $i <= $rating ? 'star-filled' : 'star-empty' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </td>

                        <td>
                            <div class="comment-text">
                                <?= nl2br(htmlspecialchars(substr($row['comment'], 0, 80))) ?>
                                <?= strlen($row['comment']) > 80 ? '...' : '' ?>
                            </div>
                        </td>

                        <td>
                            <?php if (!empty($row['image_path'])): ?>
                                <a href="../<?= htmlspecialchars($row['image_path']) ?>" target="_blank" class="photo-link">
                                    📷 View
                                </a>
                            <?php else: ?>
                                <span class="no-photo">—</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>

                        <td>
                            <form method="post" action="update_status.php" class="status-form">
                                <input type="hidden" name="id" value="<?= intval($row['id']) ?>">
                                
                                <select name="status" class="status-select">
                                    <option value="Pending" <?= $row['status']=='Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Viewed" <?= $row['status']=='Viewed' ? 'selected' : '' ?>>Viewed</option>
                                    <option value="Resolved" <?= $row['status']=='Resolved' ? 'selected' : '' ?>>Resolved</option>
                                </select>

                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>No feedback submitted yet</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>

const ratingDistribution = <?= json_encode($ratingDistData) ?>;
const dayRatings = <?= json_encode($dayRatingData) ?>;
const mealTimeRatings = <?= json_encode($mealTimeData) ?>;


const chartColors = {
    primary: '#ff6b6b',
    secondary: '#4ecdc4',
    success: '#10b981',
    warning: '#f59e0b',
    info: '#3b82f6',
    purple: '#8b5cf6',
    pink: '#ec4899'
};


const ratingLabels = ratingDistribution.map(item => `${item.rating} Stars`);
const ratingCounts = ratingDistribution.map(item => parseInt(item.count));

new Chart(document.getElementById('ratingPieChart'), {
    type: 'doughnut',
    data: {
        labels: ratingLabels,
        datasets: [{
            data: ratingCounts,
            backgroundColor: [
                chartColors.success,   
                chartColors.info,      
                chartColors.warning,   
                chartColors.primary,   
                '#ef4444'              
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12, family: 'Poppins' }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                    }
                }
            }
        }
    }
});


const dayLabels = dayRatings.map(item => item.meal_day);
const dayAvgRatings = dayRatings.map(item => parseFloat(item.avg_rating).toFixed(2));
const dayFeedbackCounts = dayRatings.map(item => parseInt(item.total_feedback));

new Chart(document.getElementById('dayRatingChart'), {
    type: 'line',
    data: {
        labels: dayLabels,
        datasets: [{
            label: 'Average Rating',
            data: dayAvgRatings,
            borderColor: chartColors.primary,
            backgroundColor: 'rgba(255, 107, 107, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: chartColors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 5,
                ticks: {
                    stepSize: 0.5,
                    font: { family: 'Poppins' }
                },
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                ticks: { font: { family: 'Poppins' } },
                grid: { display: false }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    afterLabel: function(context) {
                        const index = context.dataIndex;
                        return `Total Reviews: ${dayFeedbackCounts[index]}`;
                    }
                }
            }
        }
    }
});


const mealLabels = mealTimeRatings.map(item => item.meal_time);
const mealAvgRatings = mealTimeRatings.map(item => parseFloat(item.avg_rating).toFixed(2));

new Chart(document.getElementById('mealTimeChart'), {
    type: 'bar',
    data: {
        labels: mealLabels,
        datasets: [{
            label: 'Average Rating',
            data: mealAvgRatings,
            backgroundColor: [
                'rgba(255, 107, 107, 0.8)',
                'rgba(78, 205, 196, 0.8)',
                'rgba(139, 92, 246, 0.8)'
            ],
            borderColor: [
                chartColors.primary,
                chartColors.secondary,
                chartColors.purple
            ],
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 5,
                ticks: {
                    stepSize: 0.5,
                    font: { family: 'Poppins' }
                },
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                ticks: { font: { family: 'Poppins', size: 13 } },
                grid: { display: false }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

</body>
</html>
