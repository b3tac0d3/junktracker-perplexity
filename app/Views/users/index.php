<?php
/** @var array $users */
/**
 * Each $user is an associative array with keys:
 * id, username, email, role, status, created_at, last_login
 * pulled from the `users` table.
 */

// Helper function to format dates as mm/dd/yyyy
function formatDate($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('m/d/Y', $timestamp);
}
?>

<div class="users-dashboard">

    <!-- Header -->
    <div class="users-header">
        <div class="users-header-left">
            <h1 class="users-title">Users</h1>
            <div class="users-filter-buttons">
                <button class="filter-btn active" data-role="all">All</button>
                <button class="filter-btn" data-role="admin">Admin</button>
                <button class="filter-btn" data-role="manager">Manager</button>
                <button class="filter-btn" data-role="user">User</button>
            </div>
        </div>
        <div class="users-header-right">
            <button class="btn btn-primary" id="addUserBtn">Add User</button>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="users-summary">
        <div class="summary-card">
            <div class="summary-label">Total Users</div>
            <div class="summary-value" id="totalUsers">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Active</div>
            <div class="summary-value" id="activeUsers">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Inactive</div>
            <div class="summary-value" id="inactiveUsers">0</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Admins</div>
            <div class="summary-value" id="adminUsers">0</div>
        </div>
    </div>

    <!-- Users table -->
    <div class="users-table-wrapper">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                            $lastLogin = !empty($user['last_login']) ? formatDate($user['last_login']) : 'Never';
                            $createdAt = !empty($user['created_at']) ? formatDate($user['created_at']) : '';
                            $role = $user['role'] ?? 'user';
                            $status = $user['status'] ?? 'active';
                        ?>
                        <tr data-role="<?php echo htmlspecialchars($role); ?>" data-status="<?php echo htmlspecialchars($status); ?>">
                            <td>
                                <a href="/junktracker/public/users/<?php echo $user['id']; ?>" class="text-primary" style="text-decoration: none;">
                                    #<?php echo htmlspecialchars($user['id']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="/junktracker/public/users/<?php echo $user['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo htmlspecialchars($role); ?>">
                                    <?php echo ucfirst($role); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($lastLogin); ?></td>
                            <td><?php echo htmlspecialchars($createdAt); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <a href="/junktracker/public/users/<?php echo $user['id']; ?>" class="btn btn-sm btn-light" title="View">
                                        👁️
                                    </a>
                                    <button
                                        class="btn btn-sm btn-secondary editUserBtn"
                                        data-user='<?php echo json_encode([
                                            'id' => $user['id'],
                                            'username' => $user['username'],
                                            'email' => $user['email'],
                                            'role' => $user['role'],
                                            'status' => $user['status'],
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

        <?php if (empty($users)): ?>
            <div class="empty-state">
                No users found.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal overlay -->
<div class="modal-backdrop" id="userModalBackdrop" style="display:none;"></div>

<!-- Add/Edit User Modal -->
<div class="modal" id="userModal" style="display:none;">
    <div class="modal-header">
        <h2 id="userModalTitle">Add User</h2>
        <button class="modal-close" id="userModalClose">×</button>
    </div>
    <form id="userForm" method="POST" action="/users/store">
        <input type="hidden" name="id" id="userId">
        <div class="modal-body">
            <div class="form-row">
                <label for="userUsername">Username</label>
                <input type="text" name="username" id="userUsername" required>
            </div>
            <div class="form-row">
                <label for="userEmail">Email</label>
                <input type="email" name="email" id="userEmail" required>
            </div>
            <div class="form-row">
                <label for="userPassword">Password</label>
                <input type="password" name="password" id="userPassword">
                <small class="form-help">Leave blank to keep existing password</small>
            </div>
            <div class="form-row">
                <label for="userRole">Role</label>
                <select name="role" id="userRole" required>
                    <option value="user">User</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-row">
                <label for="userStatus">Status</label>
                <select name="status" id="userStatus" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="userModalCancel">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('#usersTableBody tr');
    const totalUsersEl = document.getElementById('totalUsers');
    const activeUsersEl = document.getElementById('activeUsers');
    const inactiveUsersEl = document.getElementById('inactiveUsers');
    const adminUsersEl = document.getElementById('adminUsers');

    function recalcStats() {
        let total = 0;
        let active = 0;
        let inactive = 0;
        let admins = 0;

        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const role = row.getAttribute('data-role');
            const status = row.getAttribute('data-status');
            total++;
            if (status === 'active') active++;
            if (status === 'inactive') inactive++;
            if (role === 'admin') admins++;
        });

        totalUsersEl.textContent = total;
        activeUsersEl.textContent = active;
        inactiveUsersEl.textContent = inactive;
        adminUsersEl.textContent = admins;
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const role = this.getAttribute('data-role');
            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                row.style.display = (role === 'all' || rowRole === role) ? '' : 'none';
            });

            recalcStats();
        });
    });

    recalcStats();

    // Modal logic
    const addUserBtn = document.getElementById('addUserBtn');
    const userModal = document.getElementById('userModal');
    const userModalBackdrop = document.getElementById('userModalBackdrop');
    const userModalClose = document.getElementById('userModalClose');
    const userModalCancel = document.getElementById('userModalCancel');
    const userModalTitle = document.getElementById('userModalTitle');
    const userForm = document.getElementById('userForm');
    const userId = document.getElementById('userId');
    const userUsername = document.getElementById('userUsername');
    const userEmail = document.getElementById('userEmail');
    const userPassword = document.getElementById('userPassword');
    const userRole = document.getElementById('userRole');
    const userStatus = document.getElementById('userStatus');

    function openModal(mode, data = null) {
        if (mode === 'add') {
            userModalTitle.textContent = 'Add User';
            userForm.action = '/users/store';
            userId.value = '';
            userUsername.value = '';
            userEmail.value = '';
            userPassword.value = '';
            userPassword.required = true;
            userRole.value = 'user';
            userStatus.value = 'active';
        } else {
            userModalTitle.textContent = 'Edit User';
            userForm.action = '/users/update/' + data.id;
            userId.value = data.id;
            userUsername.value = data.username || '';
            userEmail.value = data.email || '';
            userPassword.value = '';
            userPassword.required = false;
            userRole.value = data.role || 'user';
            userStatus.value = data.status || 'active';
        }
        userModal.style.display = 'block';
        userModalBackdrop.style.display = 'block';
    }

    function closeModal() {
        userModal.style.display = 'none';
        userModalBackdrop.style.display = 'none';
    }

    addUserBtn.addEventListener('click', function () {
        openModal('add');
    });

    document.querySelectorAll('.editUserBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-user'));
            openModal('edit', data);
        });
    });

    userModalClose.addEventListener('click', closeModal);
    userModalCancel.addEventListener('click', closeModal);
    userModalBackdrop.addEventListener('click', closeModal);
});
</script>
