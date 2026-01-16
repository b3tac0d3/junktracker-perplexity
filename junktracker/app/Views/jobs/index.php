<?php
/** @var array $jobs */
/**
 * Each $job is an associative array with keys:
 * id, client_id, client_name, description, status, created_at, updated_at
 * pulled from the `jobs` table.
 */

// Helper function to format dates as mm/dd/yyyy
function formatDate($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('m/d/Y', $timestamp);
}
?>

<div class="jobs-dashboard">

    <!-- Header -->
    <div class="jobs-header">
        <div class="jobs-header-left">
            <h1 class="jobs-title">Jobs</h1>
            <div class="jobs-filter-buttons">
                <button class="filter-btn active" data-status="all">All</button>
                <button class="filter-btn" data-status="pending">Pending</button>
                <button class="filter-btn" data-status="in_progress">In Progress</button>
                <button class="filter-btn" data-status="completed">Completed</button>
                <button class="filter-btn" data-status="cancelled">Cancelled</button>
            </div>
        </div>
        <div class="jobs-header-right">
            <button class="btn btn-primary" id="addJobBtn">Add Job</button>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="jobs-summary">
        <div class="summary-card">
            <div class="summary-label">Total Jobs</div>
            <div class="summary-value" id="totalJobs">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Pending</div>
            <div class="summary-value" id="pendingJobs">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">In Progress</div>
            <div class="summary-value" id="inProgressJobs">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Completed</div>
            <div class="summary-value" id="completedJobs">0</div>
        </div>
    </div>

    <!-- Jobs table -->
    <div class="jobs-table-wrapper">
        <table class="jobs-table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="jobsTableBody">
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            $createdAt = !empty($job['created_at']) ? formatDate($job['created_at']) : '';
                            $updatedAt = !empty($job['updated_at']) ? formatDate($job['updated_at']) : '';
                            $status = $job['status'] ?? 'pending';
                        ?>
                        <tr data-status="<?php echo htmlspecialchars($status); ?>">
                            <td>
                                <a href="/junktracker/public/jobs/<?php echo $job['id']; ?>" class="text-primary" style="text-decoration: none;">
                                    #<?php echo htmlspecialchars($job['id']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($job['client_name'])): ?>
                                    <a href="/junktracker/public/clients/<?php echo $job['client_id']; ?>" style="text-decoration: none; color: inherit;">
                                        <?php echo htmlspecialchars($job['client_name']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/junktracker/public/jobs/<?php echo $job['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($job['description']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($createdAt); ?></td>
                            <td><?php echo htmlspecialchars($updatedAt); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <a href="/junktracker/public/jobs/<?php echo $job['id']; ?>" class="btn btn-sm btn-light" title="View">
                                        👁️
                                    </a>
                                    <button
                                        class="btn btn-sm btn-secondary editJobBtn"
                                        data-job='<?php echo json_encode([
                                            'id' => $job['id'],
                                            'client_id' => $job['client_id'],
                                            'description' => $job['description'],
                                            'status' => $job['status'],
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                        title="Edit"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- No rows, handled by empty-state below -->
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (empty($jobs)): ?>
            <div class="empty-state">
                No jobs recorded yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal overlay -->
<div class="modal-backdrop" id="jobModalBackdrop" style="display:none;"></div>

<!-- Add/Edit Job Modal -->
<div class="modal" id="jobModal" style="display:none;">
    <div class="modal-header">
        <h2 id="jobModalTitle">Add Job</h2>
        <button class="modal-close" id="jobModalClose">×</button>
    </div>
    <form id="jobForm" method="POST" action="/jobs/store">
        <input type="hidden" name="id" id="jobId">
        <div class="modal-body">
            <div class="form-row">
                <label for="jobClientId">Client</label>
                <select name="client_id" id="jobClientId" required>
                    <option value="">Select a client...</option>
                    <!-- This should be populated from the controller -->
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>">
                                <?php echo htmlspecialchars($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-row">
                <label for="jobDescription">Description</label>
                <textarea name="description" id="jobDescription" rows="3" required></textarea>
            </div>
            <div class="form-row">
                <label for="jobStatus">Status</label>
                <select name="status" id="jobStatus" required>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="jobModalCancel">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('#jobsTableBody tr');
    const totalJobsEl = document.getElementById('totalJobs');
    const pendingJobsEl = document.getElementById('pendingJobs');
    const inProgressJobsEl = document.getElementById('inProgressJobs');
    const completedJobsEl = document.getElementById('completedJobs');

    function recalcStats() {
        let total = 0;
        let pending = 0;
        let inProgress = 0;
        let completed = 0;

        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const status = row.getAttribute('data-status');
            total++;
            if (status === 'pending') pending++;
            if (status === 'in_progress') inProgress++;
            if (status === 'completed') completed++;
        });

        totalJobsEl.textContent = total;
        pendingJobsEl.textContent = pending;
        inProgressJobsEl.textContent = inProgress;
        completedJobsEl.textContent = completed;
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const status = this.getAttribute('data-status');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
            });

            recalcStats();
        });
    });

    recalcStats();

    // Modal logic
    const addJobBtn = document.getElementById('addJobBtn');
    const jobModal = document.getElementById('jobModal');
    const jobModalBackdrop = document.getElementById('jobModalBackdrop');
    const jobModalClose = document.getElementById('jobModalClose');
    const jobModalCancel = document.getElementById('jobModalCancel');
    const jobModalTitle = document.getElementById('jobModalTitle');
    const jobForm = document.getElementById('jobForm');
    const jobId = document.getElementById('jobId');
    const jobClientId = document.getElementById('jobClientId');
    const jobDescription = document.getElementById('jobDescription');
    const jobStatus = document.getElementById('jobStatus');

    function openModal(mode, data = null) {
        if (mode === 'add') {
            jobModalTitle.textContent = 'Add Job';
            jobForm.action = '/jobs/store';
            jobId.value = '';
            jobClientId.value = '';
            jobDescription.value = '';
            jobStatus.value = 'pending';
        } else {
            jobModalTitle.textContent = 'Edit Job';
            jobForm.action = '/jobs/update/' + data.id;
            jobId.value = data.id;
            jobClientId.value = data.client_id || '';
            jobDescription.value = data.description || '';
            jobStatus.value = data.status || 'pending';
        }
        jobModal.style.display = 'block';
        jobModalBackdrop.style.display = 'block';
    }

    function closeModal() {
        jobModal.style.display = 'none';
        jobModalBackdrop.style.display = 'none';
    }

    addJobBtn.addEventListener('click', function () {
        openModal('add');
    });

    document.querySelectorAll('.editJobBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-job'));
            openModal('edit', data);
        });
    });

    jobModalClose.addEventListener('click', closeModal);
    jobModalCancel.addEventListener('click', closeModal);
    jobModalBackdrop.addEventListener('click', closeModal);
});
</script>
