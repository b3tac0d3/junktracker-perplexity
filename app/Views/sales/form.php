<?php
/** @var array $sale */
/** @var string $mode */
/** @var array $errors */

$baseUrl = rtrim($this->config['app']['base_url'], '/');
$isEdit = ($mode === 'edit');
$formAction = $isEdit ? "$baseUrl/sales/{$sale['id']}/update" : "$baseUrl/sales/store";
?>

<!-- Wrapper container for consistent layout -->
<div style="max-width: 900px; margin: 0 auto; padding: 1.5rem 0;">

    <!-- Back Button -->
    <div style="margin-bottom: 1rem;">
        <a href="<?= $baseUrl ?>/sales" class="btn btn-light">
            ← Back to Sales
        </a>
    </div>

    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 600; color: var(--color-text-primary); margin: 0;">
            <?= $isEdit ? 'Edit Sale' : 'Add New Sale' ?>
        </h1>
    </div>

    <!-- Form Card -->
    <div class="card" style="background: var(--color-bg-elevated); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-md);">
        
        <?php if (!empty($errors)): ?>
            <div style="background: #fee; border: 1px solid #fcc; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem;">
                <h3 style="color: #c00; font-size: 1rem; font-weight: 600; margin: 0 0 0.5rem 0;">Please fix the following errors:</h3>
                <ul style="margin: 0; padding-left: 1.5rem; color: #c00;">
                    <?php foreach ($errors as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $formAction ?>">
            
            <!-- Sale Date -->
            <div style="margin-bottom: 1.5rem;">
                <label for="sale_date" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Sale Date <span style="color: #c00;">*</span>
                </label>
                <input 
                    type="date" 
                    id="sale_date" 
                    name="sale_date" 
                    value="<?= htmlspecialchars($sale['sale_date'] ?? date('Y-m-d')) ?>"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary);"
                    class="<?= isset($errors['sale_date']) ? 'error' : '' ?>"
                >
            </div>

            <!-- Amount -->
            <div style="margin-bottom: 1.5rem;">
                <label for="amount" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Amount <span style="color: #c00;">*</span>
                </label>
                <input 
                    type="number" 
                    id="amount" 
                    name="amount" 
                    value="<?= htmlspecialchars($sale['amount'] ?? '') ?>"
                    step="0.01"
                    min="0"
                    required
                    placeholder="Enter sale amount"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary);"
                    class="<?= isset($errors['amount']) ? 'error' : '' ?>"
                >
            </div>

            <!-- Status -->
            <div style="margin-bottom: 1.5rem;">
                <label for="status" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Status <span style="color: #c00;">*</span>
                </label>
                <select 
                    id="status" 
                    name="status" 
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary);"
                    class="<?= isset($errors['status']) ? 'error' : '' ?>"
                >
                    <?php 
                    $currentStatus = $sale['status'] ?? 'pending';
                    $statuses = ['pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                    foreach ($statuses as $value => $label): 
                    ?>
                        <option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Source -->
            <div style="margin-bottom: 1.5rem;">
                <label for="source" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Source <span style="color: #c00;">*</span>
                </label>
                <select 
                    id="source" 
                    name="source" 
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary);"
                    class="<?= isset($errors['source']) ? 'error' : '' ?>"
                >
                    <?php 
                    $currentSource = $sale['source'] ?? 'shop';
                    $sources = ['shop' => 'Shop', 'online' => 'Online', 'phone' => 'Phone', 'email' => 'Email', 'referral' => 'Referral'];
                    foreach ($sources as $value => $label): 
                    ?>
                        <option value="<?= $value ?>" <?= $currentSource === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Client ID (Optional) -->
            <div style="margin-bottom: 1.5rem;">
                <label for="client_id" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Client ID <span style="color: var(--color-text-secondary); font-weight: normal; font-size: 0.875rem;">(Optional)</span>
                </label>
                <input 
                    type="number" 
                    id="client_id" 
                    name="client_id" 
                    value="<?= htmlspecialchars($sale['client_id'] ?? '') ?>"
                    placeholder="Enter client ID if applicable"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary);"
                >
            </div>

            <!-- Note -->
            <div style="margin-bottom: 2rem;">
                <label for="note" style="display: block; font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                    Notes <span style="color: var(--color-text-secondary); font-weight: normal; font-size: 0.875rem;">(Optional)</span>
                </label>
                <textarea 
                    id="note" 
                    name="note" 
                    rows="4"
                    placeholder="Add any additional notes about this sale..."
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 1rem; background: var(--color-bg-primary); color: var(--color-text-primary); resize: vertical; font-family: inherit;"
                ><?= htmlspecialchars($sale['note'] ?? '') ?></textarea>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a 
                    href="<?= $baseUrl ?>/sales" 
                    class="btn btn-light"
                    style="padding: 0.75rem 1.5rem; text-decoration: none; display: inline-block;"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="padding: 0.75rem 2rem;"
                >
                    <?= $isEdit ? 'Update Sale' : 'Create Sale' ?>
                </button>
            </div>

        </form>

    </div>

</div>
