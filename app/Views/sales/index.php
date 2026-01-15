<?php
/** @var array $sales */
/**
 * Each $sale is an associative array with keys:
 * id, type, name, note, start_date, end_date, gross_amount, net_amount, updated_at
 * pulled from the `sales` table. [file:1]
 */
?>
<div class="sales-dashboard">

    <!-- Filters -->
    <div class="sales-filters">
        <div class="sales-filters-left">
            <h1 class="sales-title">Sales</h1>
            <div class="sales-filter-buttons">
                <button class="filter-btn active" data-type="all">All</button>
                <button class="filter-btn" data-type="shop">Shop</button>
                <button class="filter-btn" data-type="scrap">Scrap</button>
                <button class="filter-btn" data-type="ebay">eBay</button>
                <button class="filter-btn" data-type="other">Other</button>
            </div>
        </div>
        <div class="sales-filters-right">
            <button class="btn btn-primary" id="addSaleBtn">Add Sale</button>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="sales-summary">
        <div class="summary-card">
            <div class="summary-label">Total Gross</div>
            <div class="summary-value" id="totalGross">$0.00</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Net</div>
            <div class="summary-value" id="totalNet">$0.00</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Entries</div>
            <div class="summary-value" id="totalEntries">0</div>
        </div>
    </div>

    <!-- Sales table -->
    <div class="sales-table-wrapper">
        <table class="sales-table">
            <thead>
                <tr>
                    <th>Date / Range</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Gross</th>
                    <th>Net</th>
                    <th>Note</th>
                    <th>Last Updated</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody id="salesTableBody">
                <?php if (!empty($sales)): ?>
                    <?php foreach ($sales as $sale): ?>
                        <?php
                            $start = !empty($sale['start_date']) ? $sale['start_date'] : null;
                            $end   = !empty($sale['end_date']) ? $sale['end_date'] : null;
                            if ($start && $end && $start !== $end) {
                                $dateLabel = $start . ' – ' . $end;
                            } else {
                                $dateLabel = $start ?: ($end ?: '');
                            }
                            $netRaw = isset($sale['net_amount']) ? (float)$sale['net_amount'] : 0;
                        ?>
                        <tr data-type="<?php echo htmlspecialchars($sale['type']); ?>">
                            <td><?php echo htmlspecialchars($dateLabel); ?></td>
                            <td>
                                <span class="type-badge type-<?php echo htmlspecialchars($sale['type']); ?>">
                                    <?php echo ucfirst($sale['type']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($sale['name']); ?></td>
                            <td class="amount-cell"
                                data-gross="<?php echo htmlspecialchars($sale['gross_amount']); ?>">
                                $<?php echo number_format($sale['gross_amount'], 2); ?>
                            </td>
                            <td class="amount-cell"
                                data-net="<?php echo htmlspecialchars($netRaw); ?>">
                                <?php if ($sale['net_amount'] !== null): ?>
                                    $<?php echo number_format($sale['net_amount'], 2); ?>
                                <?php else: ?>
                                    <span class="muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="note-cell">
                                <?php if (!empty($sale['note'])): ?>
                                    <?php echo htmlspecialchars($sale['note']); ?>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($sale['updated_at']); ?>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-secondary editSaleBtn"
                                    data-sale='<?php echo json_encode([
                                        'id'           => $sale['id'],
                                        'type'         => $sale['type'],
                                        'name'         => $sale['name'],
                                        'note'         => $sale['note'],
                                        'start_date'   => $sale['start_date'],
                                        'end_date'     => $sale['end_date'],
                                        'gross_amount' => $sale['gross_amount'],
                                        'net_amount'   => $sale['net_amount'],
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- No rows, handled by empty-state below -->
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (empty($sales)): ?>
            <div class="empty-state">
                No sales recorded yet.
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal overlay -->
<div class="modal-backdrop" id="saleModalBackdrop" style="display:none;"></div>

<!-- Add/Edit Sale Modal -->
<div class="modal" id="saleModal" style="display:none;">
    <div class="modal-header">
        <h2 id="saleModalTitle">Add Sale</h2>
        <button class="modal-close" id="saleModalClose">&times;</button>
    </div>
    <form id="saleForm" method="POST" action="/sales/store">
        <!-- Adjust action URLs to your router conventions -->
        <input type="hidden" name="id" id="saleId">
        <div class="modal-body">
            <div class="form-row">
                <label for="saleType">Type</label>
                <select name="type" id="saleType" required>
                    <option value="shop">Shop</option>
                    <option value="scrap">Scrap</option>
                    <option value="ebay">eBay</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-row">
                <label for="saleName">Name</label>
                <input type="text" name="name" id="saleName" required>
            </div>

            <div class="form-row">
                <label for="saleNote">Note</label>
                <textarea name="note" id="saleNote" rows="2"></textarea>
            </div>

            <div class="form-row form-row-inline">
                <div>
                    <label for="saleStartDate">Start date</label>
                    <input type="date" name="start_date" id="saleStartDate">
                </div>
                <div>
                    <label for="saleEndDate">End date</label>
                    <input type="date" name="end_date" id="saleEndDate">
                </div>
            </div>

            <div class="form-row form-row-inline">
                <div>
                    <label for="saleGross">Gross</label>
                    <input type="number" step="0.01" min="0" name="gross_amount" id="saleGross" required>
                </div>
                <div>
                    <label for="saleNet">Net</label>
                    <input type="number" step="0.01" min="0" name="net_amount" id="saleNet">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" id="saleModalCancel">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saleModalSave">Save</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('#salesTableBody tr');
    const totalGrossEl = document.getElementById('totalGross');
    const totalNetEl = document.getElementById('totalNet');
    const totalEntriesEl = document.getElementById('totalEntries');

    function recalcTotals() {
        let gross = 0;
        let net = 0;
        let count = 0;

        rows.forEach(row => {
            if (row.style.display === 'none') return;

            const grossCell = row.querySelector('[data-gross]');
            const netCell = row.querySelector('[data-net]');
            if (!grossCell || !netCell) return;

            gross += parseFloat(grossCell.getAttribute('data-gross')) || 0;
            net   += parseFloat(netCell.getAttribute('data-net')) || 0;
            count++;
        });

        totalGrossEl.textContent = '$' + gross.toFixed(2);
        totalNetEl.textContent   = '$' + net.toFixed(2);
        totalEntriesEl.textContent = count;
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const type = this.getAttribute('data-type');
            rows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                row.style.display = (type === 'all' || rowType === type) ? '' : 'none';
            });

            recalcTotals();
        });
    });

    recalcTotals();

    // Modal logic
    const addSaleBtn = document.getElementById('addSaleBtn');
    const saleModal = document.getElementById('saleModal');
    const saleModalBackdrop = document.getElementById('saleModalBackdrop');
    const saleModalClose = document.getElementById('saleModalClose');
    const saleModalCancel = document.getElementById('saleModalCancel');
    const saleModalTitle = document.getElementById('saleModalTitle');
    const saleForm = document.getElementById('saleForm');

    const saleId = document.getElementById('saleId');
    const saleType = document.getElementById('saleType');
    const saleName = document.getElementById('saleName');
    const saleNote = document.getElementById('saleNote');
    const saleStartDate = document.getElementById('saleStartDate');
    const saleEndDate = document.getElementById('saleEndDate');
    const saleGross = document.getElementById('saleGross');
    const saleNet = document.getElementById('saleNet');

    function openModal(mode, data = null) {
        if (mode === 'add') {
            saleModalTitle.textContent = 'Add Sale';
            saleForm.action = '/sales/store'; // adjust to your routing
            saleId.value = '';
            saleType.value = 'shop';
            saleName.value = '';
            saleNote.value = '';
            saleStartDate.value = '';
            saleEndDate.value = '';
            saleGross.value = '';
            saleNet.value = '';
        } else {
            saleModalTitle.textContent = 'Edit Sale';
            saleForm.action = '/sales/update/' + data.id; // adjust to your routing
            saleId.value = data.id;
            saleType.value = data.type;
            saleName.value = data.name || '';
            saleNote.value = data.note || '';
            saleStartDate.value = data.start_date || '';
            saleEndDate.value = data.end_date || '';
            saleGross.value = data.gross_amount || '';
            saleNet.value = data.net_amount || '';
        }
        saleModal.style.display = 'block';
        saleModalBackdrop.style.display = 'block';
    }

    function closeModal() {
        saleModal.style.display = 'none';
        saleModalBackdrop.style.display = 'none';
    }

    addSaleBtn.addEventListener('click', function () {
        openModal('add');
    });

    document.querySelectorAll('.editSaleBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-sale'));
            openModal('edit', data);
        });
    });

    saleModalClose.addEventListener('click', closeModal);
    saleModalCancel.addEventListener('click', closeModal);
    saleModalBackdrop.addEventListener('click', closeModal);
});
</script>