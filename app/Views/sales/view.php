<?php
/** @var array $sale */
/**
 * Sale detail view
 * Displays all information about a single sale entry
 */

// Helper function to format dates as mm/dd/yyyy
function formatDate($date) {
    if (empty($date)) return 'N/A';
    $timestamp = strtotime($date);
    return date('m/d/Y', $timestamp);
}

// Helper function to format datetime as mm/dd/yyyy hh:mm AM/PM
function formatDateTime($datetime) {
    if (empty($datetime)) return 'N/A';
    $timestamp = strtotime($datetime);
    return date('m/d/Y g:i A', $timestamp);
}
?>

<div class="sale-detail">
    
    <!-- Header with back button and actions -->
    <div class="sale-detail-header">
        <div>
            <a href="/sales" class="btn btn-light">
                ← Back to Sales
            </a>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-secondary" id="editSaleBtn">Edit</button>
            <button class="btn btn-danger" id="deleteSaleBtn">Delete</button>
        </div>
    </div>

    <!-- Sale title and type badge -->
    <div class="sale-detail-title">
        <h1><?php echo htmlspecialchars($sale['name']); ?></h1>
        <span class="type-badge type-<?php echo htmlspecialchars($sale['type']); ?>">
            <?php echo ucfirst($sale['type']); ?>
        </span>
    </div>

    <!-- Main info cards -->
    <div class="sale-detail-cards">
        
        <!-- Financial Summary Card -->
        <div class="detail-card">
            <h3 class="detail-card-title">Financial Summary</h3>
            <div class="detail-row">
                <span class="detail-label">Gross Amount:</span>
                <span class="detail-value amount-cell">
                    $<?php echo number_format($sale['gross_amount'], 2); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Net Amount:</span>
                <span class="detail-value amount-cell">
                    <?php if ($sale['net_amount'] !== null): ?>
                        $<?php echo number_format($sale['net_amount'], 2); ?>
                    <?php else: ?>
                        <span class="muted">Not specified</span>
                    <?php endif; ?>
                </span>
            </div>
            <?php if ($sale['net_amount'] !== null && $sale['gross_amount'] > 0): ?>
                <?php 
                    $profit = $sale['net_amount'] - $sale['gross_amount'];
                    $margin = ($profit / $sale['gross_amount']) * 100;
                ?>
                <div class="detail-row">
                    <span class="detail-label">Profit/Loss:</span>
                    <span class="detail-value amount-cell <?php echo $profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $profit >= 0 ? '+' : ''; ?>$<?php echo number_format($profit, 2); ?>
                        (<?php echo number_format($margin, 1); ?>%)
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Date Information Card -->
        <div class="detail-card">
            <h3 class="detail-card-title">Date Information</h3>
            <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span class="detail-value">
                    <?php echo formatDate($sale['start_date']); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span class="detail-value">
                    <?php echo formatDate($sale['end_date']); ?>
                </span>
            </div>
            <?php if (!empty($sale['start_date']) && !empty($sale['end_date'])): ?>
                <?php 
                    $start = strtotime($sale['start_date']);
                    $end = strtotime($sale['end_date']);
                    $days = round(($end - $start) / (60 * 60 * 24));
                ?>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">
                        <?php echo abs($days); ?> day<?php echo abs($days) !== 1 ? 's' : ''; ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Metadata Card -->
        <div class="detail-card">
            <h3 class="detail-card-title">Record Information</h3>
            <div class="detail-row">
                <span class="detail-label">Sale ID:</span>
                <span class="detail-value">#<?php echo $sale['id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Type:</span>
                <span class="detail-value"><?php echo ucfirst($sale['type']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Last Updated:</span>
                <span class="detail-value">
                    <?php echo formatDateTime($sale['updated_at']); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created:</span>
                <span class="detail-value">
                    <?php echo formatDateTime($sale['created_at']); ?>
                </span>
            </div>
        </div>

    </div>

    <!-- Notes Section -->
    <?php if (!empty($sale['note'])): ?>
        <div class="detail-card detail-card-full">
            <h3 class="detail-card-title">Notes</h3>
            <div class="detail-note-content">
                <?php echo nl2br(htmlspecialchars($sale['note'])); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="detail-card detail-card-full">
            <p class="muted" style="margin: 0;">No notes for this sale.</p>
        </div>
    <?php endif; ?>

</div>

<!-- Delete Confirmation Modal -->
<div class="modal-backdrop" id="deleteModalBackdrop" style="display:none;"></div>
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-header">
        <h2>Delete Sale</h2>
        <button class="modal-close" id="deleteModalClose">&times;</button>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to delete this sale? This action cannot be undone.</p>
        <p><strong><?php echo htmlspecialchars($sale['name']); ?></strong></p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" id="deleteModalCancel">Cancel</button>
        <form method="POST" action="/sales/delete/<?php echo $sale['id']; ?>" style="display: inline;">
            <button type="submit" class="btn btn-danger">Delete Sale</button>
        </form>
    </div>
</div>

<style>
.sale-detail {
    padding: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.sale-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.sale-detail-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.sale-detail-title h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.sale-detail-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    padding: 1.25rem;
}

.detail-card-full {
    grid-column: 1 / -1;
}

.detail-card-title {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-text-main);
    border-bottom: 1px solid var(--color-border);
    padding-bottom: 0.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--color-border-soft);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.875rem;
    color: var(--color-text-subtle);
    font-weight: 500;
}

.detail-value {
    font-size: 0.875rem;
    color: var(--color-text-main);
    font-weight: 400;
    text-align: right;
}

.detail-note-content {
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--color-text-main);
    white-space: pre-wrap;
}

.text-success {
    color: #2da44e !important;
}

.text-danger {
    color: #cf222e !important;
}

.btn-danger {
    background: #cf222e !important;
    border-color: #cf222e !important;
    color: #ffffff !important;
}

.btn-danger:hover {
    background: #a40e26 !important;
    border-color: #a40e26 !important;
}

@media (max-width: 768px) {
    .sale-detail {
        padding: 1rem;
    }

    .sale-detail-header {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }

    .sale-detail-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .sale-detail-title h1 {
        font-size: 1.5rem;
    }

    .sale-detail-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit button - redirect to edit page or open modal
    const editBtn = document.getElementById('editSaleBtn');
    editBtn.addEventListener('click', function() {
        window.location.href = '/sales/edit/<?php echo $sale['id']; ?>';
    });

    // Delete button - show confirmation modal
    const deleteBtn = document.getElementById('deleteSaleBtn');
    const deleteModal = document.getElementById('deleteModal');
    const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
    const deleteModalClose = document.getElementById('deleteModalClose');
    const deleteModalCancel = document.getElementById('deleteModalCancel');

    deleteBtn.addEventListener('click', function() {
        deleteModal.style.display = 'block';
        deleteModalBackdrop.style.display = 'block';
    });

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        deleteModalBackdrop.style.display = 'none';
    }

    deleteModalClose.addEventListener('click', closeDeleteModal);
    deleteModalCancel.addEventListener('click', closeDeleteModal);
    deleteModalBackdrop.addEventListener('click', closeDeleteModal);
});
</script>