<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
$isEdit  = ($mode ?? '') === 'edit';
$action  = $isEdit && !empty($client['id'])
    ? $baseUrl . '/clients/' . (int)$client['id']
    : $baseUrl . '/clients';

$val = function(string $field) use ($client) {
    return htmlspecialchars($client[$field] ?? '', ENT_QUOTES, 'UTF-8');
};
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="h5 mb-0"><?= $isEdit ? 'Edit Client' : 'Add Client' ?></h1>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger small">
                        <ul class="mb-0">
                            <?php foreach ($errors as $msg): ?>
                                <li><?= htmlspecialchars($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= $action ?>">
                    <div class="mb-2">
                        <label class="form-label">First name</label>
                        <input type="text" name="first_name" class="form-control form-control-sm" value="<?= $val('first_name') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Last name</label>
                        <input type="text" name="last_name" class="form-control form-control-sm" value="<?= $val('last_name') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Business name</label>
                        <input type="text" name="business_name" class="form-control form-control-sm" value="<?= $val('business_name') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="<?= $val('phone') ?>">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="can_text" id="canTextSwitch"
                               <?= !empty($client['can_text']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="canTextSwitch">Can text this number</label>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= $val('email') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Address 1</label>
                        <input type="text" name="address_1" class="form-control form-control-sm" value="<?= $val('address_1') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Address 2</label>
                        <input type="text" name="address_2" class="form-control form-control-sm" value="<?= $val('address_2') ?>">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control form-control-sm" value="<?= $val('city') ?>">
                        </div>
                        <div class="col-4 col-md-2">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control form-control-sm" value="<?= $val('state') ?>">
                        </div>
                        <div class="col-8 col-md-4">
                            <label class="form-label">Zip</label>
                            <input type="text" name="zip" class="form-control form-control-sm" value="<?= $val('zip') ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Client type</label>
                        <?php $ct = $client['client_type'] ?? 'client'; ?>
                        <select name="client_type" class="form-select form-select-sm">
                            <option value="client"   <?= $ct === 'client'   ? 'selected' : '' ?>>Client</option>
                            <option value="realtor"  <?= $ct === 'realtor'  ? 'selected' : '' ?>>Realtor</option>
                            <option value="business" <?= $ct === 'business' ? 'selected' : '' ?>>Business</option>
                            <option value="other"    <?= $ct === 'other'    ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control form-control-sm" rows="3"><?= htmlspecialchars($client['note'] ?? '') ?></textarea>
                    </div>

                    <?php if ($isEdit): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="active" id="activeCheck"
                                   <?= !isset($client['active']) || $client['active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activeCheck">Active</label>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="<?= $baseUrl ?>/clients" class="btn btn-sm btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <?= $isEdit ? 'Save changes' : 'Create client' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
