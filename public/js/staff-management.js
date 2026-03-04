// Staff Management - Modal Functions

// Create Modal
function openCreateModal() {
    // Reset role buttons
    document.getElementById('createRole').value = '';
    document.querySelectorAll('[id^="createRole_"]').forEach(btn => {
        btn.classList.remove('bg-slate-800', 'text-white', 'border-slate-800');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    document.getElementById('createModal').classList.remove('hidden');
}
function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

// Role toggle buttons
function selectRole(value, prefix) {
    document.getElementById(prefix + 'Role').value = value;
    const buttons = document.querySelectorAll('[id^="' + prefix + 'Role_"]');
    buttons.forEach(btn => {
        btn.classList.remove('bg-slate-800', 'text-white', 'border-slate-800');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    const active = document.getElementById(prefix + 'Role_' + value);
    if (active) {
        active.classList.remove('border-gray-200', 'text-gray-600');
        active.classList.add('bg-slate-800', 'text-white', 'border-slate-800');
    }
}

// Edit Modal
function openEditModal(id, name, email, role) {
    document.getElementById('editForm').action = '/staff-management/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    selectRole(role, 'edit');
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Reset Password Modal
function openResetModal(id, name) {
    document.getElementById('resetForm').action = '/staff-management/' + id + '/reset-password';
    document.getElementById('resetStaffName').textContent = 'Resetting password for: ' + name;
    document.getElementById('resetModal').classList.remove('hidden');
}
function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

// Delete Modal
function openDeleteModal(id, name) {
    document.getElementById('deleteForm').action = '/staff-management/' + id;
    document.getElementById('deleteStaffName').textContent = 'You are about to delete "' + name + '"';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Admin Password Modal
function openAdminPasswordModal() {
    document.getElementById('adminPasswordModal').classList.remove('hidden');
}
function closeAdminPasswordModal() {
    document.getElementById('adminPasswordModal').classList.add('hidden');
}
