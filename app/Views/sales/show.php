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
<link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/sales.css?v=2">

<div class="sale-detail">
    
    <!-- Header with back button and actions -->
    <div class="sale-detail-header">
        <div>
            <a href="<?= $baseUrl ?>/sales" class="btn btn-light">
                ← Back to Sales
            </a>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= $baseUrl ?>/sales/<?php echo $sale['id']; ?>/edit" class="btn btn-secondary">Edit</a>
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
                    <?php echo formatDateSale($sale['start_date']); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span class="detail-value">
                    <?php echo formatDateSale($sale['end_date']); ?>
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
                    <?php echo formatDateTimeSale($sale['updated_at']); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created:</span>
                <span class="detail-value">
                    <?php echo formatDateTimeSale($sale['created_at']); ?>
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
        <form method="POST" action="/sales/<?php echo $sale['id']; ?>/delete" style="display: inline;">
            <button type="submit" class="btn btn-danger">Delete Sale</button>
        </form>
    </div>
</div>

<style>
.sale-detail {
    padding: 1.5rem 0;
}
    </style>

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
