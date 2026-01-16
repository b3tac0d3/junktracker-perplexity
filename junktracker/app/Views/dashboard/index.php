<?php
/**
 * @var array $stats - Dashboard statistics from controller
 * Expected keys: mtd_income, ytd_income, jobs_snapshot, pipeline, recent_sales
 */
?>

<div class="dashboard-container" style="max-width: 75%; margin: 0 auto; padding: 1.5rem 0;">
    
    <!-- Quick Stats Row -->
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        
        <!-- MTD Income Card -->
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">MTD Income</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-primary mb-3">
                        $<?php echo number_format($stats['mtd_income']['total'] ?? 0, 2); ?>
                    </h3>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1">
                            <span class="fw-bold">Shop:</span> 
                            $<?php echo number_format($stats['mtd_income']['shop'] ?? 0, 2); ?>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold">eBay:</span> 
                            $<?php echo number_format($stats['mtd_income']['ebay_gross'] ?? 0, 2); ?> gross / 
                            $<?php echo number_format($stats['mtd_income']['ebay_net'] ?? 0, 2); ?> net
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold">Scrap:</span> 
                            $<?php echo number_format($stats['mtd_income']['scrap'] ?? 0, 2); ?>
                        </li>
                        <li>
                            <span class="fw-bold">Jobs:</span> 
                            $<?php echo number_format($stats['mtd_income']['jobs_gross'] ?? 0, 2); ?> gross / 
                            $<?php echo number_format($stats['mtd_income']['jobs_net'] ?? 0, 2); ?> net
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- YTD Income Card -->
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">YTD Income</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-success mb-3">
                        $<?php echo number_format($stats['ytd_income']['total'] ?? 0, 2); ?>
                    </h3>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1">
                            <span class="fw-bold">Shop:</span> 
                            $<?php echo number_format($stats['ytd_income']['shop'] ?? 0, 2); ?>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold">eBay:</span> 
                            $<?php echo number_format($stats['ytd_income']['ebay_gross'] ?? 0, 2); ?> gross / 
                            $<?php echo number_format($stats['ytd_income']['ebay_net'] ?? 0, 2); ?> net
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold">Scrap:</span> 
                            $<?php echo number_format($stats['ytd_income']['scrap'] ?? 0, 2); ?>
                        </li>
                        <li>
                            <span class="fw-bold">Jobs:</span> 
                            $<?php echo number_format($stats['ytd_income']['jobs_gross'] ?? 0, 2); ?> gross / 
                            $<?php echo number_format($stats['ytd_income']['jobs_net'] ?? 0, 2); ?> net
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Jobs Snapshot Card -->
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Jobs Snapshot</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 g-2">
                        <div class="col">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="h4 mb-0 text-primary">
                                    <?php echo $stats['jobs_snapshot']['quoted'] ?? 0; ?>
                                </div>
                                <small class="text-muted">Quoted</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="h4 mb-0 text-warning">
                                    <?php echo $stats['jobs_snapshot']['active'] ?? 0; ?>
                                </div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="h4 mb-0 text-success">
                                    <?php echo $stats['jobs_snapshot']['completed_mtd'] ?? 0; ?>
                                </div>
                                <small class="text-muted">Completed MTD</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="h4 mb-0 text-success">
                                    <?php echo $stats['jobs_snapshot']['completed_ytd'] ?? 0; ?>
                                </div>
                                <small class="text-muted">Completed YTD</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline & Recent Activity Row -->
    <div class="row">
        
        <!-- Pipeline Card -->
        <div class="col-12 col-lg-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Pipeline</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span>Prospective clients (not quoted)</span>
                        <span class="badge bg-secondary">
                            <?php echo $stats['pipeline']['prospective'] ?? 0; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span>Quoted waiting follow-up</span>
                        <span class="badge bg-warning text-dark">
                            <?php echo $stats['pipeline']['quoted_waiting'] ?? 0; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span>Waiting on estimate emails</span>
                        <span class="badge bg-info">
                            <?php echo $stats['pipeline']['waiting_estimates'] ?? 0; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Waiting on invoices</span>
                        <span class="badge bg-danger">
                            <?php echo $stats['pipeline']['waiting_invoices'] ?? 0; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sales Card -->
        <div class="col-12 col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Sales</h5>
                    <a href="<?= rtrim($this->config['app']['base_url'], '/') ?>/sales" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recent_sales'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($stats['recent_sales'], 0, 5) as $sale): ?>
                                <a href="<?= rtrim($this->config['app']['base_url'], '/') ?>/sales/<?php echo $sale['id']; ?>" 
                                   class="list-group-item list-group-item-action p-2 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small text-truncate" style="max-width: 150px;">
                                                <?php echo htmlspecialchars($sale['name']); ?>
                                            </div>
                                            <small class="text-muted">
                                                <span class="badge badge-sm bg-secondary">
                                                    <?php echo ucfirst($sale['type']); ?>
                                                </span>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="small fw-bold text-success">
                                                $<?php echo number_format($sale['gross_amount'], 2); ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php 
                                                    $date = new DateTime($sale['created_at']);
                                                    echo $date->format('m/d');
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0 small">No recent sales yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= rtrim($this->config['app']['base_url'], '/') ?>/sales" class="btn btn-primary">
                            View All Sales
                        </a>
                        <a href="<?= rtrim($this->config['app']['base_url'], '/') ?>/jobs" class="btn btn-info">
                            Manage Jobs
                        </a>
                        <a href="<?= rtrim($this->config['app']['base_url'], '/') ?>/clients" class="btn btn-secondary">
                            View Clients
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
