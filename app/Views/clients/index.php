<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
$q = $filters['q'] ?? '';
$active = $filters['active'] ?? '1';
$client_type = $filters['client_type'] ?? '';
?>

<link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/sales.css?v=2">

<div class="sales-dashboard">

    <!-- Filters -->
    <div class="sales-filters">
        <div class="sales-filters-left">
            <h1 class="sales-title">Clients</h1>
            <div class="sales-filter-buttons">
                <button class="filter-btn <?= $active === '' ? 'active' : '' ?>" onclick="window.location.href='<?= $baseUrl ?>/clients?client_type=<?= urlencode($client_type) ?>&q=<?= urlencode($q) ?>'" data-active="">All</button>
                <button class="filter-btn <?= $active === '1' ? 'active' : '' ?>" onclick="window.location.href='<?= $baseUrl ?>/clients?active=1&client_type=<?= urlencode($client_type) ?>&q=<?= urlencode($q) ?>'" data-active="1">Active</button>
                <button class="filter-btn <?= $active === '0' ? 'active' : '' ?>" onclick="window.location.href='<?= $baseUrl ?>/clients?active=0&client_type=<?= urlencode($client_type) ?>&q=<?= urlencode($q) ?>'" data-active="0">Inactive</button>
            </div>
        </div>
        <div class="sales-filters-right">
            <button class="btn btn-primary" id="addClientBtn" onclick="window.location.href='<?= $baseUrl ?>/clients/create'">Add Client</button>
        </div>
    </div>

    <!-- Search and Type Filter -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body">
            <form class="row g-2" method="get" action="">
                <input type="hidden" name="active" value="<?= htmlspecialchars($active) ?>">
                <div class="col-12 col-md-6">
                    <input type="text"
                           name="q"
                           value="<?= htmlspecialchars($q) ?>"
                           class="form-control form-control-sm"
                           placeholder="Search name, business, phone, email">
                </div>
                <div class="col-12 col-md-4">
                    <select name="client_type" class="form-select form-select-sm">
                        <option value="" <?= $client_type === '' ? 'selected' : '' ?>>All types</option>
                        <option value="client" <?= $client_type === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="realtor" <?= $client_type === 'realtor' ? 'selected' : '' ?>>Realtor</option>
                        <option value="business" <?= $client_type === 'business' ? 'selected' : '' ?>>Business</option>
                        <option value="other" <?= $client_type === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-sm btn-secondary w-100" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients list -->
    <?php if (!$clients): ?>
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-0">No clients found.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name/Business</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <?php
                                $name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
                                if ($client['business_name']) {
                                    $name = $client['business_name'] . ($name ? " ({$name})" : '');
                                }
                                $cityState = trim(($client['city'] ?? '') . ', ' . ($client['state'] ?? ''), ', ');
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>" class="fw-semibold text-decoration-none">
                                            <?= htmlspecialchars($name ?: 'Unnamed client') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars(format_phone($client['phone'] ?? '')) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($cityState) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($client['client_type'] ?? '') ?></span></td>
                                    <td style="text-align: right;">
                                        <a href="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>/edit" class="btn btn-sm btn-light">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_page > 1): ?>
            <nav class="mt-3">
                <ul class="pagination pagination-sm mb-0">
                    <?php
                    $current = $current ?? 1;
                    $queryBase = $_GET;
                    ?>
                    <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                        <?php
                        $queryBase['page'] = max(1, $current - 1);
                        ?>
                        <a class="page-link" href="?<?= http_build_query($queryBase) ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $total_page; $p++): ?>
                        <?php $queryBase['page'] = $p; ?>
                        <li class="page-item <?= $p === $current ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query($queryBase) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $current >= $total_page ? 'disabled' : '' ?>">
                        <?php
                        $queryBase['page'] = min($total_page, $current + 1);
                        ?>
                        <a class="page-link" href="?<?= http_build_query($queryBase) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</div>
