<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
$q           = $filters['q'] ?? '';
$active      = $filters['active'] ?? '1';
$client_type = $filters['client_type'] ?? '';
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <h1 class="h5 mb-2 mb-md-0">Clients</h1>
            <a href="<?= $baseUrl ?>/clients/create" class="btn btn-sm btn-primary">Add Client</a>
        </div>
    </div>
    <div class="card-body">

        <!-- Filters/search -->
        <form class="row g-2 mb-3" method="get" action="">
            <div class="col-12 col-md-4">
                <input type="text"
                       name="q"
                       value="<?= htmlspecialchars($q) ?>"
                       class="form-control form-control-sm"
                       placeholder="Search name, business, phone, email">
            </div>
            <div class="col-6 col-md-2">
                <select name="active" class="form-select form-select-sm">
                    <option value="" <?= $active === '' ? 'selected' : '' ?>>All</option>
                    <option value="1" <?= $active === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $active === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="client_type" class="form-select form-select-sm">
                    <option value="" <?= $client_type === '' ? 'selected' : '' ?>>All types</option>
                    <option value="client" <?= $client_type === 'client' ? 'selected' : '' ?>>Client</option>
                    <option value="realtor" <?= $client_type === 'realtor' ? 'selected' : '' ?>>Realtor</option>
                    <option value="business" <?= $client_type === 'business' ? 'selected' : '' ?>>Business</option>
                    <option value="other" <?= $client_type === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-sm btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
        </form>

        <!-- Clients list -->
        <?php if (!$clients): ?>
            <p class="text-muted mb-0">No clients found.</p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($clients as $client): ?>
                    <?php
                    $name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
                    if ($client['business_name']) {
                        $name = $client['business_name'] . ($name ? " ({$name})" : '');
                    }
                    $cityState = trim(($client['city'] ?? '') . ', ' . ($client['state'] ?? ' '), ', ');
                    ?>
                    <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="mb-2 mb-md-0">
                            <a href="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>" class="fw-semibold text-decoration-none">
                                <?= htmlspecialchars($name ?: 'Unnamed client') ?>
                            </a>
                            <div class="text-muted small">
                                <?php if ($client['phone']): ?>
                                    <td><?= htmlspecialchars(format_phone($client['phone'] ?? '')) ?></td>
                                <?php endif; ?>
                                <?php if ($cityState): ?>
                                    · <?= htmlspecialchars($cityState) ?>
                                <?php endif; ?>
                                <?php if ($client['client_type']): ?>
                                    · <?= htmlspecialchars($client['client_type']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        </div>
                    </div>

                <?php endforeach; ?>
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
</div>
