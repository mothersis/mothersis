<?php 
require_once 'includes/header.php';
require_once 'includes/Dashboard.php';

try {
    // Initialize dashboard
    $dashboard = new Dashboard();

    // Get dashboard data with error handling
    try {
        $totalUsers = $dashboard->getTotalUsers();
        $monthlyRevenue = $dashboard->getMonthlyRevenue();
        $activeSubscriptions = $dashboard->getActiveSubscriptions();
        $pendingTickets = $dashboard->getPendingTickets();
        $recentActivities = $dashboard->getRecentActivity();
        $monthlyGrowth = $dashboard->getMonthlyGrowth();
        $packageDistribution = $dashboard->getPackageDistribution();
        $revenueHistory = $dashboard->getRevenueHistory();
        $ticketsByPriority = $dashboard->getTicketsByPriority();
    } catch (Exception $e) {
        error_log("Dashboard data fetch error: " . $e->getMessage());
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                There was an error loading some dashboard data. Please try again later.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        // Set default values
        $totalUsers = $monthlyRevenue = $activeSubscriptions = $pendingTickets = 0;
        $recentActivities = $packageDistribution = $revenueHistory = $ticketsByPriority = [];
        $monthlyGrowth = ['percentage' => 0, 'current' => 0, 'previous' => 0];
    }
    ?>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
            <p class="mb-0 text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
                <i class="bi bi-lightning-charge me-2"></i> Quick Actions
            </button>
            <button class="btn btn-success btn-sm d-flex align-items-center" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-2"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase fw-semibold small mb-2">Total Users</div>
                            <h3 class="mb-0"><?php echo number_format($totalUsers); ?></h3>
                            <?php if ($monthlyGrowth['percentage'] != 0): ?>
                            <small class="text-<?php echo $monthlyGrowth['percentage'] > 0 ? 'success' : 'danger'; ?>">
                                <i class="bi bi-arrow-<?php echo $monthlyGrowth['percentage'] > 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo abs($monthlyGrowth['percentage']); ?>% from last month
                            </small>
                            <?php endif; ?>
                        </div>
                        <div class="icon-shape bg-primary text-white rounded-3 p-3">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <a href="users.php" class="text-decoration-none text-primary d-flex align-items-center justify-content-center">
                        View Details <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase fw-semibold small mb-2">Monthly Revenue</div>
                            <h3 class="mb-0"><?php echo CURRENCY_SYMBOL . number_format($monthlyRevenue, 2); ?></h3>
                            <?php 
                            if (!empty($revenueHistory)) {
                                $lastMonth = end($revenueHistory);
                                $prevMonth = prev($revenueHistory);
                                if ($prevMonth && $prevMonth['total'] > 0) {
                                    $growth = (($lastMonth['total'] - $prevMonth['total']) / $prevMonth['total']) * 100;
                                    echo '<small class="text-' . ($growth > 0 ? 'success' : 'danger') . '">
                                        <i class="bi bi-arrow-' . ($growth > 0 ? 'up' : 'down') . '"></i>
                                        ' . abs(round($growth, 1)) . '% from last month
                                    </small>';
                                }
                            }
                            ?>
                        </div>
                        <div class="icon-shape bg-success text-white rounded-3 p-3">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <a href="revenue.php" class="text-decoration-none text-success d-flex align-items-center justify-content-center">
                        View Details <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase fw-semibold small mb-2">Active Subscriptions</div>
                            <h3 class="mb-0"><?php echo number_format($activeSubscriptions); ?></h3>
                            <small class="text-muted">Active package users</small>
                        </div>
                        <div class="icon-shape bg-info text-white rounded-3 p-3">
                            <i class="bi bi-box"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <a href="subscriptions.php" class="text-decoration-none text-info d-flex align-items-center justify-content-center">
                        View Details <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Tickets Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase fw-semibold small mb-2">Pending Tickets</div>
                            <h3 class="mb-0"><?php echo number_format($pendingTickets); ?></h3>
                            <small class="text-muted">Requires attention</small>
                        </div>
                        <div class="icon-shape bg-warning text-white rounded-3 p-3">
                            <i class="bi bi-ticket-detailed"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <a href="tickets.php" class="text-decoration-none text-warning d-flex align-items-center justify-content-center">
                        View Details <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Revenue Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Package Distribution -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Package Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="packageChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Tickets -->
    <div class="row g-4">
        <!-- Recent Activity Table -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <a href="activity-logs.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">User</th>
                                    <th class="border-0">Action</th>
                                    <th class="border-0">Timestamp</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivities as $activity): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-primary-subtle rounded-circle">
                                                <?php echo strtoupper(substr($activity['username'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($activity['username'] ?? 'Unknown'); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($activity['timestamp'])); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'secondary';
                                        $statusIcon = 'circle';
                                        if ($activity['status'] == 'success') {
                                            $statusClass = 'success';
                                            $statusIcon = 'check-circle';
                                        } elseif ($activity['status'] == 'pending') {
                                            $statusClass = 'warning';
                                            $statusIcon = 'clock';
                                        } elseif ($activity['status'] == 'error') {
                                            $statusClass = 'danger';
                                            $statusIcon = 'x-circle';
                                        }
                                        ?>
                                        <div class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> px-2 py-1">
                                            <i class="bi bi-<?php echo $statusIcon; ?>-fill me-1"></i>
                                            <?php echo ucfirst(htmlspecialchars($activity['status'])); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Tickets Summary -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Support Tickets Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="ticketsChart" height="200"></canvas>
                    <div class="mt-4">
                        <?php foreach ($ticketsByPriority as $priority): ?>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-<?php echo $priority['priority'] == 'high' ? 'danger' : 
                                    ($priority['priority'] == 'medium' ? 'warning' : 'success'); ?> me-2"></span>
                                <span class="text-muted"><?php echo ucfirst($priority['priority']); ?> Priority</span>
                            </div>
                            <span class="fw-bold"><?php echo $priority['count']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Modal -->
    <div class="modal fade" id="quickActionsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Quick Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="customers.php?action=add" class="card text-center border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body">
                                    <i class="bi bi-person-plus display-6 text-primary mb-2"></i>
                                    <h6 class="mb-0">Add Customer</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="packages.php?action=add" class="card text-center border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body">
                                    <i class="bi bi-box-seam display-6 text-success mb-2"></i>
                                    <h6 class="mb-0">Create Package</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="tickets.php?action=new" class="card text-center border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body">
                                    <i class="bi bi-ticket display-6 text-warning mb-2"></i>
                                    <h6 class="mb-0">New Ticket</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="reports.php" class="card text-center border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body">
                                    <i class="bi bi-file-earmark-text display-6 text-info mb-2"></i>
                                    <h6 class="mb-0">Generate Report</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Charts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($revenueHistory, 'month')); ?>,
                    datasets: [{
                        label: 'Revenue',
                        data: <?php echo json_encode(array_column($revenueHistory, 'total')); ?>,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '<?php echo CURRENCY_SYMBOL; ?>' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Package Distribution Chart
            const packageCtx = document.getElementById('packageChart').getContext('2d');
            new Chart(packageCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($packageDistribution, 'name')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($packageDistribution, 'total')); ?>,
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Tickets Chart
            const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
            new Chart(ticketsCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_column($ticketsByPriority, 'priority')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($ticketsByPriority, 'count')); ?>,
                        backgroundColor: ['#dc3545', '#ffc107', '#198754']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>

    <?php
} catch (Exception $e) {
    error_log("Critical dashboard error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>A critical error occurred. Please contact the system administrator.</div>";
}

include 'includes/footer.php';
?>
