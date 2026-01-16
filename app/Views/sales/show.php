<?php
/** @var array $sale */
/**
 * Sale detail view - content only, wrapped by main.php layout
 */

// Helper function to format dates as mm/dd/yyyy
function formatDateSale($date) {
    if (empty($date)) return 'N/A';
    $timestamp = strtotime($date);
    return date('m/d/Y', $timestamp);
}

// Helper function to format datetime as mm/dd/yyyy hh:mm AM/PM
function formatDateTimeSale($datetime) {
    if (empty($datetime)) return 'N/A';
    $timestamp = strtotime($datetime);
    return date('m/d/Y g:i A', $timestamp);
}
?>

<?php
$baseUrl = rtrim($this->config['app']['base_url'], '/');
?>

<!-- Wrapper container for consistent layout -->
<div style="max-width: 900px; margin: 0 auto; padding: 1.5rem 0;">
    
    <!-- Back Button -->
    <div style="margin-bottom: 1rem;">
        <a href="<?= $baseUrl ?>/sales" class="btn btn-light">
            ← Back to Sales
        </a>
    </div>

    <!-- Header: Title, Type Badge, and Actions -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin: 0 0 0.5rem 0; font-size: 1.75rem; font-weight: 600;">
                <?php echo htmlspecialchars($sale['name']); ?>
            </h1>
            <span class="type-badge type-<?php echo htmlspecialchars($sale['type']); ?>">
                <?php echo ucfirst($sale['type']); ?>
            </span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= $baseUrl ?>/sales/<?php echo $sale['id']; ?>/edit" class="btn btn-secondary">
                Edit
            </a>
            <button class="btn btn-danger" id="deleteSaleBtn">
                Delete
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        
        <!-- Financial Summary Card -->
        <div style="background: #ffffff; border: 1px solid var(--color-border, #d0d7de); border-radius: 0.75rem; padding: 1.25rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Financial Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Gross Amount:</span>
                <span class="amount-cell" style="font-weight: 600;">
                    $<?php echo number_format($sale['gross_amount'], 2); ?>
                </span>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Net Amount:</span>
                <span class="amount-cell" style="font-weight: 600;">
                    <?php if ($sale['net_amount'] !== null): ?>
                        $<?php echo number_format($sale['net_amount'], 2); ?>
                    <?php else: ?>
                        <span class="muted">Not specified</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- Date Information Card -->
        <div style="background: #ffffff; border: 1px solid var(--color-border, #d0d7de); border-radius: 0.75rem; padding: 1.25rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Date Information</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Start Date:</span>
                <span style="font-weight: 500;">
                    <?php echo formatDateSale($sale['start_date']); ?>
                </span>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">End Date:</span>
                <span style="font-weight: 500;">
                    <?php echo !empty($sale['end_date']) ? formatDateSale($sale['end_date']) : 'N/A'; ?>
                </span>
            </div>

            <?php if (!empty($sale['start_date']) && !empty($sale['end_date'])): ?>
                <?php 
                    $start = strtotime($sale['start_date']);
                    $end = strtotime($sale['end_date']);
                    $days = round(($end - $start) / (60 * 60 * 24));
                ?>
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-border, #d0d7de);">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Duration:</span>
                        <span style="font-weight: 500;">
                            <?php echo abs($days); ?> day<?php echo abs($days) !== 1 ? 's' : ''; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Record Information Card -->
        <div style="background: #ffffff; border: 1px solid var(--color-border, #d0d7de); border-radius: 0.75rem; padding: 1.25rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Record Information</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Sale ID:</span>
                <span style="font-weight: 500;">#<?php echo $sale['id']; ?></span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Type:</span>
                <span style="font-weight: 500;"><?php echo ucfirst($sale['type']); ?></span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Last Updated:</span>
                <span style="font-weight: 500; font-size: 0.8125rem;">
                    <?php echo formatDateTimeSale($sale['updated_at']); ?>
                </span>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--color-text-subtle, #57606a); font-size: 0.875rem;">Created:</span>
                <span style="font-weight: 500; font-size: 0.8125rem;">
                    <?php echo formatDateTimeSale($sale['created_at']); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <?php if (!empty($sale['note'])): ?>
        <div style="background: #ffffff; border: 1px solid var(--color-border, #d0d7de); border-radius: 0.75rem; padding: 1.25rem;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Notes</h3>
            <p style="margin: 0; white-space: pre-wrap; color: var(--color-text-main, #24292f); line-height: 1.6;">
                <?php echo htmlspecialchars($sale['note']); ?>
            </p>
        </div>
    <?php else: ?>
        <div style="background: var(--color-border-soft, #f6f8fa); border: 1px solid var(--color-border, #d0d7de); border-radius: 0.75rem; padding: 1.25rem; text-align: center;">
            <p class="muted" style="margin: 0;">No notes for this sale.</p>
        </div>
    <?php endif; ?>

</div>

<!-- Delete Confirmation Modal -->
<div class="modal-backdrop" id="deleteModalBackdrop" style="display:none;"></div>
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-header">
        <h2>Delete Sale</h2>
        <button class="modal-close" id="deleteModalClose">×</button>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to delete this sale? This action cannot be undone.</p>
        <p><strong><?php echo htmlspecialchars($sale['name']); ?></strong></p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" id="deleteModalCancel">Cancel</button>
        <form method="POST" action="/sales/<?php echo $sale['id']; ?>/delete" style="display: inline;">
            <button type="submit" class="btn btn-danger">Delete Sale</button>
        </form>
    </div>
</div>

<script>
    // Delete Modal Functionality
    const deleteModal = document.getElementById('deleteModal');
    const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
    const deleteBtn = document.getElementById('deleteSaleBtn');
    const deleteModalClose = document.getElementById('deleteModalClose');
    const deleteModalCancel = document.getElementById('deleteModalCancel');

    function openDeleteModal() {
        deleteModal.style.display = 'block';
        deleteModalBackdrop.style.display = 'block';
    }

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        deleteModalBackdrop.style.display = 'none';
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', openDeleteModal);
    }

    if (deleteModalClose) {
        deleteModalClose.addEventListener('click', closeDeleteModal);
    }

    if (deleteModalCancel) {
        deleteModalCancel.addEventListener('click', closeDeleteModal);
    }

    if (deleteModalBackdrop) {
        deleteModalBackdrop.addEventListener('click', closeDeleteModal);
    }
</script>
