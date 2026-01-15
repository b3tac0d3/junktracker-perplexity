<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
$name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
if (!empty($client['business_name'])) {
    $name = $client['business_name'] . ($name ? " ({$name})" : '');
}
$cityState = trim(($client['city'] ?? '') . ', ' . ($client['state'] ?? ' '), ', ');
?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">

        <!-- Top actions -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
            <div class="mb-2 mb-sm-0">
                <a href="<?= $baseUrl ?>/jobs/create?client_id=<?= (int)$client['id'] ?>" class="btn btn-sm btn-primary me-2">
                    Add Job
                </a>
                <a href="<?= $baseUrl ?>/clients" class="btn btn-sm btn-outline-secondary">
                    Back to Clients
                </a>
            </div>
        </div>

        <!-- Client info card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h5 mb-0"><?= htmlspecialchars($name ?: 'Unnamed client') ?></h1>
                        <div class="small text-muted">
                            <?= $client['active'] ? 'Active' : 'Inactive' ?>
                            <?php if (!empty($client['client_type'])): ?>
                                · <?= htmlspecialchars($client['client_type']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                            Edit
                        </a>
                        <form method="post"
                              action="<?= $baseUrl ?>/clients/<?= (int)$client['id'] ?>/deactivate"
                              onsubmit="return confirm('Deactivate this client?');">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Deactivate
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body small">
                <div class="mb-2">
                    <strong>Phone:</strong>
                    <?= htmlspecialchars(format_phone($client['phone'] ?? '')) ?>
                    <?php if (!empty($client['can_text'])): ?>
                        · <span class="text-success">OK to text</span>
                    <?php endif; ?>
                </div>

                <div class="mb-2">
                    <strong>Email:</strong>
                    <?= htmlspecialchars($client['email'] ?? '') ?>
                </div>

                <div class="mb-2">
                    <strong>Address:</strong><br>
                    <?= htmlspecialchars(trim(($client['address_1'] ?? '') . ' ' . ($client['address_2'] ?? ''))) ?><br>
                    <?= htmlspecialchars($cityState) ?>
                    <?php if (!empty($client['zip'])): ?>
                        <?= ' ' . htmlspecialchars($client['zip']) ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($client['note'])): ?>
                    <div class="mb-2">
                        <strong>Note:</strong><br>
                        <?= nl2br(htmlspecialchars($client['note'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Placeholder for per-client jobs & money summary -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">Jobs & Money (client summary)</div>
            <div class="card-body small">
                <p class="text-muted mb-0">
                    Summary (won/active/pending/lost jobs, totals, pipeline, last 10 jobs) will be implemented here next.
                </p>
            </div>
        </div>
    </div>
</div>