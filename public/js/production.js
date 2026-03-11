// Modal functions with animations
function openWorkOrderModal() {
    const modal = document.getElementById('workOrderModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const content = modal.querySelector('.modal-content');
        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95) translateY(10px)';
            setTimeout(() => {
                content.style.transition = 'all 0.2s ease-out';
                content.style.opacity = '1';
                content.style.transform = 'scale(1) translateY(0)';
            }, 10);
        }
    }, 10);
}

function closeWorkOrderModal() {
    const modal = document.getElementById('workOrderModal');
    const content = modal.querySelector('.modal-content');
    if (content) {
        content.style.transition = 'all 0.15s ease-in';
        content.style.opacity = '0';
        content.style.transform = 'scale(0.95) translateY(10px)';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('workOrderForm').reset();
            document.getElementById('salesOrdersListSection').classList.remove('hidden');
            document.getElementById('productLineSelector').classList.add('hidden');
        }, 150);
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('workOrderForm').reset();
        // Clear search input and results in modal
        const searchInput = document.getElementById('modalSalesOrderSearch');
        if (searchInput) {
            searchInput.value = '';
            filterModalSalesOrders();
        }
    }
}

const rawWorkOrdersData = window.__productionData || [];
const workOrdersData = (rawWorkOrdersData && rawWorkOrdersData.data) ? rawWorkOrdersData.data : rawWorkOrdersData;
function editWorkOrder(id) {
    const wo = workOrdersData.find(w => w.id === id);
    if (wo) {
        document.getElementById('editStatus').value = wo.status;
        document.getElementById('editCompletionQuantity').value = wo.completion_quantity ?? 0;
    }
    const modal = document.getElementById('editWorkOrderModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('editWorkOrderForm').action = `/production/${id}`;
    setTimeout(() => {
        const content = modal.querySelector('.modal-content');
        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95) translateY(10px)';
            setTimeout(() => {
                content.style.transition = 'all 0.2s ease-out';
                content.style.opacity = '1';
                content.style.transform = 'scale(1) translateY(0)';
            }, 10);
        }
    }, 10);
}

function closeEditWorkOrderModal() {
    const modal = document.getElementById('editWorkOrderModal');
    const content = modal.querySelector('.modal-content');
    if (content) {
        content.style.transition = 'all 0.15s ease-in';
        content.style.opacity = '0';
        content.style.transform = 'scale(0.95) translateY(10px)';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function filterModalSalesOrders() {
    const searchTerm = document.getElementById('modalSalesOrderSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.sales-order-card');
    const noResults = document.getElementById('modalNoResults');
    let visibleCount = 0;

    cards.forEach(card => {
        const orderNumber = card.getAttribute('data-order-number') || '';
        const customerName = card.getAttribute('data-customer-name') || '';

        if (orderNumber.includes(searchTerm) || customerName.includes(searchTerm)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    if (noResults) {
        noResults.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
    }
}

// Store current items when sales order is selected (for product line dropdown)
let currentOrderItems = [];

function selectSalesOrder(salesOrderId, orderNumber, customerName, deliveryDate, items) {
    const listSection = document.getElementById('salesOrdersListSection');
    const formSection = document.getElementById('workOrderForm');
    const tableBody = document.getElementById('orderItemsTableBody');

    currentOrderItems = Array.isArray(items) ? items : JSON.parse(items);
    document.getElementById('formSalesOrderId').value = salesOrderId;
    document.getElementById('formDueDate').value = deliveryDate;
    document.getElementById('summaryOrderNumber').textContent = orderNumber;
    document.getElementById('summaryCustomer').textContent = customerName;
    document.getElementById('summaryDeliveryDate').textContent = deliveryDate ? new Date(deliveryDate).toLocaleDateString() : '-';

    // Populate items table
    tableBody.innerHTML = '';
    if (currentOrderItems.length > 0) {
        currentOrderItems.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="items[${idx}][selected]" value="1" class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked onchange="checkSelection()">
                                <input type="hidden" name="items[${idx}][product_id]" value="${item.id}">
                                <input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-medium">
                                ${item.name}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">
                                ${item.quantity}
                            </td>
                        `;
            tableBody.appendChild(tr);
        });
        document.getElementById('selectAllItems').checked = true;
    } else {
        tableBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">No pending products found.</td></tr>';
    }

    listSection.classList.add('hidden');
    formSection.classList.remove('hidden');
    checkSelection();
}

function toggleAllItems(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    checkSelection();
}

function checkSelection() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const submitBtn = document.querySelector('#workOrderForm button[type="submit"]');
    const errorMsg = document.getElementById('itemSelectionError');

    if (checkboxes.length === 0) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        errorMsg.classList.remove('hidden');
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        errorMsg.classList.add('hidden');
    }
}

function selectTeam(btn, teamName) {
    // Remove active state from all team buttons
    document.querySelectorAll('.team-btn').forEach(b => {
        b.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
        b.classList.add('border-gray-300', 'bg-white');
    });
    // Add active state to clicked button
    btn.classList.remove('border-gray-300', 'bg-white');
    btn.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
    // Set hidden input value
    document.getElementById('selectedTeam').value = teamName;
}

function resetWorkOrderForm() {
    const listSection = document.getElementById('salesOrdersListSection');
    const formSection = document.getElementById('workOrderForm');
    document.getElementById('workOrderForm').reset();
    // Reset team buttons
    document.querySelectorAll('.team-btn').forEach(b => {
        b.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
        b.classList.add('border-gray-300', 'bg-white');
    });
    document.getElementById('selectedTeam').value = '';
    listSection.classList.remove('hidden');
    formSection.classList.add('hidden');
}

function viewWorkOrder(id) {
    const wo = workOrdersData.find(w => w.id === id);
    if (!wo) {
        console.warn('Work order not found in page data, attempting to fetch:', id);
        // Fallback: try fetching from server
        fetch(`/production/${id}`)
            .then(r => r.json())
            .then(data => {
                if (data && data.workOrder) populateAndShowViewModal(data.workOrder);
                else alert('Unable to load work order details.');
            })
            .catch(() => alert('Unable to load work order details.'));
        return;
    }

    populateAndShowViewModal(wo);
}

function populateAndShowViewModal(wo) {
    currentWorkOrderId = wo.id;
    document.getElementById('vw_orderNumber').textContent = wo.order_number || wo.orderNumber || '-';
    document.getElementById('vw_productName').textContent = wo.product_name || wo.productName || '-';
    document.getElementById('vw_quantity').textContent = (wo.quantity !== undefined) ? (wo.quantity + ' pcs') : '-';
    document.getElementById('vw_startingDate').textContent = wo.starting_date ? new Date(wo.starting_date).toLocaleDateString() : (wo.startingDate ? new Date(wo.startingDate).toLocaleDateString() : 'Not started');
    document.getElementById('vw_dueDate').textContent = wo.due_date ? new Date(wo.due_date).toLocaleDateString() : (wo.dueDate || '-');
    document.getElementById('vw_assignedTo').textContent = wo.assigned_to || wo.assignedTo || '-';
    document.getElementById('vw_notes').value = wo.notes || wo.details || '';
    document.getElementById('notesForm').action = `/production/${wo.id}`;
    const modal = document.getElementById('viewWorkOrderModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeViewWorkOrderModal() {
    const modal = document.getElementById('viewWorkOrderModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Generic Modal Helpers
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

let currentWorkOrderId = null;
let currentCompleteOrderId = null;

function openCompleteConfirmModal(id, orderNumber) {
    currentCompleteOrderId = id;
    document.getElementById('completeConfirmOrderNumber').textContent = orderNumber;
    const modal = document.getElementById('completeConfirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCompleteConfirmModal() {
    const modal = document.getElementById('completeConfirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmCompleteWorkOrder() {
    const orderNumber = document.getElementById('completeConfirmOrderNumber').textContent;

    fetch(`/production/${currentCompleteOrderId}/complete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            const data = isJson ? await response.json() : null;

            if (!response.ok) {
                const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                throw new Error(errorMessage);
            }
            return data;
        })
        .then(data => {
            closeCompleteConfirmModal();
            if (data.success) {
                showSuccessNotification(`Work Order ${orderNumber} marked as completed!`);
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorNotification('Error completing work order: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            closeCompleteConfirmModal();
            showErrorNotification('Error completing work order: ' + error.message);
        });
}

function openCancelConfirmModal(id, orderNumber) {
    if (id) currentWorkOrderId = id;
    const displayNum = orderNumber || document.getElementById('vw_orderNumber').textContent;
    document.getElementById('cancelConfirmOrderNumber').textContent = displayNum;

    const modal = document.getElementById('cancelConfirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCancelConfirmModal() {
    const modal = document.getElementById('cancelConfirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmCancelWorkOrder() {
    const orderNumber = document.getElementById('vw_orderNumber').textContent;

    // Make the cancel request
    fetch(`/production/${currentWorkOrderId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            const data = isJson ? await response.json() : null;

            if (!response.ok) {
                const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                throw new Error(errorMessage);
            }
            return data;
        })
        .then(data => {
            closeCancelConfirmModal();
            if (data.success) {
                showSuccessNotification(`Work Order ${orderNumber} has been cancelled successfully.`);
                closeViewWorkOrderModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorNotification('Error cancelling work order: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            closeCancelConfirmModal();
            showErrorNotification('Error cancelling work order: ' + error.message);
        });
}

// Production History Modal Functions
function openProductionHistoryModal() {
    const modal = document.getElementById('productionHistoryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const content = modal.querySelector('.modal-content');
        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95) translateY(10px)';
            setTimeout(() => {
                content.style.transition = 'all 0.2s ease-out';
                content.style.opacity = '1';
                content.style.transform = 'scale(1) translateY(0)';
            }, 10);
        }
    }, 10);
}

function closeProductionHistoryModal() {
    const modal = document.getElementById('productionHistoryModal');
    const content = modal.querySelector('.modal-content');
    if (content) {
        content.style.transition = 'all 0.15s ease-in';
        content.style.opacity = '0';
        content.style.transform = 'scale(0.95) translateY(10px)';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Filter production history
function filterProductionHistory() {
    const searchTerm = document.getElementById('historySearchInput')?.value.toLowerCase().trim() || '';
    const startDate = document.getElementById('historyStartDate')?.value || '';
    const endDate = document.getElementById('historyEndDate')?.value || '';

    const items = document.querySelectorAll('.history-item');
    const noResults = document.getElementById('historyNoResults');
    const emptyState = document.getElementById('historyEmptyState');
    let visibleCount = 0;
    const totalItems = items.length;

    items.forEach(item => {
        const orderNumber = item.getAttribute('data-order-number') || '';
        const productName = item.getAttribute('data-product-name') || '';
        const assignedTo = item.getAttribute('data-assigned-to') || '';
        const completedDate = item.getAttribute('data-completed-date') || '';
        const startingDate = item.getAttribute('data-starting-date') || '';

        // Search match
        const searchMatch = !searchTerm ||
            orderNumber.includes(searchTerm) ||
            productName.includes(searchTerm) ||
            assignedTo.includes(searchTerm);

        // Date range match (check both completed and starting dates)
        let dateMatch = true;
        if (startDate || endDate) {
            const checkDate = completedDate || startingDate;
            if (checkDate) {
                if (startDate && checkDate < startDate) dateMatch = false;
                if (endDate && checkDate > endDate) dateMatch = false;
            } else {
                dateMatch = false;
            }
        }

        // Show/hide item
        if (searchMatch && dateMatch) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Update count badge
    const countBadge = document.getElementById('completedCount');
    if (countBadge) {
        countBadge.textContent = `${visibleCount} of ${totalItems} Completed`;
    }

    // Show/hide no results message
    if (noResults) {
        noResults.classList.toggle('hidden', visibleCount !== 0 || totalItems === 0);
    }
    if (emptyState) {
        emptyState.classList.toggle('hidden', totalItems !== 0);
    }
}

function applyHistoryQuickFilter(type) {
    const startInput = document.getElementById('historyStartDate');
    const endInput = document.getElementById('historyEndDate');
    const now = new Date();
    const today = now.toISOString().split('T')[0];

    let start = '';
    let end = today;

    switch (type) {
        case 'yesterday':
            const yesterday = new Date(now);
            yesterday.setDate(now.getDate() - 1);
            start = yesterday.toISOString().split('T')[0];
            end = start;
            break;
        case 'last-week':
            const lastWeek = new Date(now);
            lastWeek.setDate(now.getDate() - 7);
            start = lastWeek.toISOString().split('T')[0];
            break;
        case '1-month':
            const oneMonth = new Date(now);
            oneMonth.setMonth(now.getMonth() - 1);
            start = oneMonth.toISOString().split('T')[0];
            break;
        case '1-year':
            const oneYear = new Date(now);
            oneYear.setFullYear(now.getFullYear() - 1);
            start = oneYear.toISOString().split('T')[0];
            break;
    }

    if (startInput) startInput.value = start;
    if (endInput) endInput.value = end;
    filterProductionHistory();
}

// Clear history filters
function clearHistoryFilters() {
    document.getElementById('historySearchInput').value = '';
    document.getElementById('historyStartDate').value = '';
    document.getElementById('historyEndDate').value = '';
    filterProductionHistory();
}


function startWorkOrder(id) {
    if (confirm('Start this work order?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/production/' + id + '/start';
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

function pauseWorkOrder(id) {
    if (confirm('Pause this work order?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/production/' + id;
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        const method = document.createElement('input');
        method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
        const status = document.createElement('input');
        status.type = 'hidden'; status.name = 'status'; status.value = 'in_progress';
        form.appendChild(csrf); form.appendChild(method); form.appendChild(status);
        document.body.appendChild(form);
        form.submit();
    }
}

function completeWorkOrder(id) {
    // Legacy function - now uses openCompleteConfirmModal
    // Kept for backward compatibility
}

// Close modals when clicking outside
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('fixed')) {
        closeWorkOrderModal();
        closeEditWorkOrderModal();
    }
});

// Handle work order form submission with notification
document.addEventListener('submit', function (e) {
    if (e.target.id === 'workOrderForm') {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Create Work Order';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                    throw new Error(errorMessage);
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    showSuccessNotification(data.message);
                    closeWorkOrderModal();
                    form.reset();

                    if (data.html) {
                        const tbody = document.getElementById('workOrderTableBody');
                        const emptyState = document.getElementById('workOrderEmptyState');
                        if (emptyState) emptyState.classList.add('hidden');
                        tbody.insertAdjacentHTML('afterbegin', data.html); // Add new row at top
                    } else {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    showErrorNotification(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorNotification(error.message);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
    }
});

// Handle notes form submission with notification
document.addEventListener('submit', function (e) {
    if (e.target.id === 'notesForm') {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method === 'POST' ? 'POST' : 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response. Status: ' + response.status);
                }
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessNotification('Notes saved successfully!');
                } else {
                    showErrorNotification(data.message || 'Error saving notes');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorNotification('Error saving notes: ' + error.message);
            });
    }
});

// Set minimum date to today and wire up search
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];
    const dueDateInput = document.querySelector('input[name="due_date"]');
    if (dueDateInput) dueDateInput.setAttribute('min', today);

    const searchInput = document.getElementById('workOrderSearchInput');
    const statusFilter = document.getElementById('statusFilterSelect');

    // Combined filter function
    function filterWorkOrders() {
        const searchTerm = searchInput?.value.toLowerCase().trim() || '';
        const statusValue = statusFilter?.value || 'all';
        const rows = document.querySelectorAll('.work-order-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const orderNumber = row.getAttribute('data-order-number')?.toLowerCase() || '';
            const productName = row.getAttribute('data-product-name')?.toLowerCase() || '';
            const assignedTo = row.getAttribute('data-assigned-to')?.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';

            // Check search match
            const searchMatch = !searchTerm ||
                orderNumber.includes(searchTerm) ||
                productName.includes(searchTerm) ||
                assignedTo.includes(searchTerm);

            // Check status match
            const statusMatch = statusValue === 'all' || status === statusValue;

            // Show row only if both conditions are met
            if (searchMatch && statusMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty state
        const emptyState = document.getElementById('workOrderEmptyState');
        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount !== 0);
        }
    }

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', filterWorkOrders);
    }

    // Status filter functionality
    if (statusFilter) {
        statusFilter.addEventListener('change', filterWorkOrders);
    }

    // Production History filters
    const historySearchInput = document.getElementById('historySearchInput');
    const historyStartDate = document.getElementById('historyStartDate');
    const historyEndDate = document.getElementById('historyEndDate');

    if (historySearchInput) {
        historySearchInput.addEventListener('input', filterProductionHistory);
    }
    if (historyStartDate) {
        historyStartDate.addEventListener('change', filterProductionHistory);
    }
    if (historyEndDate) {
        historyEndDate.addEventListener('change', filterProductionHistory);
    }
});

// Filter work orders by search text and status
function filterWorkOrders() {
    const searchTerm = (document.getElementById('workOrderSearchInput').value || '').toLowerCase().trim();
    const statusFilter = document.getElementById('statusFilterSelect').value;
    const rows = document.querySelectorAll('.work-order-row');
    const emptyState = document.getElementById('workOrderEmptyState');
    let visibleCount = 0;

    rows.forEach(row => {
        const orderNumber = (row.getAttribute('data-order-number') || '').toLowerCase();
        const productName = (row.getAttribute('data-product-name') || '').toLowerCase();
        const customerName = (row.getAttribute('data-customer-name') || '').toLowerCase();
        const assignedTo = (row.getAttribute('data-assigned-to') || '').toLowerCase();
        const status = row.getAttribute('data-status') || '';

        const matchesSearch = !searchTerm ||
            orderNumber.includes(searchTerm) ||
            productName.includes(searchTerm) ||
            customerName.includes(searchTerm) ||
            assignedTo.includes(searchTerm);

        const matchesStatus = statusFilter === 'all' || status === statusFilter;

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (emptyState) {
        emptyState.classList.toggle('hidden', visibleCount > 0);
        if (visibleCount === 0) {
            emptyState.textContent = searchTerm ? 'No work orders match your search' : 'No active production found';
        }
    }
}

