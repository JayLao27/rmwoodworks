// Accounting - Filter, Modal, Export, and Transaction Functions

let maxPaymentAmount = 0;

// Filter functionality
function filterTransactions() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.transaction-row');

    rows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        const rowStatus = row.getAttribute('data-status');
        const rowId = row.getAttribute('data-id').toLowerCase();
        const rowDate = row.getAttribute('data-date').toLowerCase();
        const rowCategory = row.getAttribute('data-category').toLowerCase();
        const rowDescription = row.getAttribute('data-description').toLowerCase();

        const categoryMatch = categoryFilter === 'all' || rowType === categoryFilter;
        const statusMatch = statusFilter === 'all' ||
            rowStatus === statusFilter ||
            (statusFilter === 'unpaid' && rowStatus === 'pending');
        const searchMatch = searchInput === '' ||
            rowId.includes(searchInput) ||
            rowDate.includes(searchInput) ||
            rowType.toLowerCase().includes(searchInput) ||
            rowCategory.includes(searchInput) ||
            rowDescription.includes(searchInput);

        if (categoryMatch && statusMatch && searchMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Attach filter event listeners
document.getElementById('searchInput').addEventListener('input', filterTransactions);
document.getElementById('categoryFilter').addEventListener('change', filterTransactions);
document.getElementById('statusFilter').addEventListener('change', filterTransactions);

// Modal functions
function openAddTransaction() {
    document.getElementById('addTransactionModal').classList.remove('hidden');
    document.getElementById('confirmationSection').classList.add('hidden');
    document.getElementById('salesOrdersContainer').classList.remove('hidden');
    document.getElementById('purchaseOrdersContainer').classList.add('hidden');
    // Reset tab styling
    showSalesOrders();
    document.body.style.overflow = 'hidden';
}

function closeAddTransaction() {
    document.getElementById('addTransactionModal').classList.add('hidden');
    resetSelection();
    document.body.style.overflow = '';
}

// Tab switching
function showSalesOrders() {
    document.getElementById('salesOrdersContainer').classList.remove('hidden');
    document.getElementById('purchaseOrdersContainer').classList.add('hidden');
    // Update tab styling for sales orders
    document.getElementById('salesOrdersTab').classList.add('bg-amber-500', 'text-white', 'shadow-lg');
    document.getElementById('salesOrdersTab').classList.remove('bg-slate-600', 'text-slate-300');
    document.getElementById('purchaseOrdersTab').classList.add('bg-slate-600', 'text-slate-300');
    document.getElementById('purchaseOrdersTab').classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
    document.getElementById('confirmationSection').classList.add('hidden');
    filterModalItems(); // Re-apply filters
}

function showPurchaseOrders() {
    document.getElementById('purchaseOrdersContainer').classList.remove('hidden');
    document.getElementById('salesOrdersContainer').classList.add('hidden');
    // Update tab styling for purchase orders
    document.getElementById('purchaseOrdersTab').classList.add('bg-amber-500', 'text-white', 'shadow-lg');
    document.getElementById('purchaseOrdersTab').classList.remove('bg-slate-600', 'text-slate-300');
    document.getElementById('salesOrdersTab').classList.add('bg-slate-600', 'text-slate-300');
    document.getElementById('salesOrdersTab').classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
    document.getElementById('confirmationSection').classList.add('hidden');
    filterModalItems(); // Re-apply filters
}

// Modal Filtering and Sorting Logic
function filterModalItems() {
    const searchTerm = document.getElementById('modalSearchInput').value.toLowerCase();
    const sortOrder = document.getElementById('modalSortFilter').value;

    const isSalesActive = !document.getElementById('salesOrdersContainer').classList.contains('hidden');
    const container = isSalesActive
        ? document.getElementById('salesOrdersContainer')
        : document.getElementById('purchaseOrdersContainer');

    if (!container) return;

    // Get all transaction items
    const items = Array.from(container.querySelectorAll('.transaction-item'));

    // 1. Filter items
    items.forEach(item => {
        const orderNum = item.querySelector('h3')?.textContent.toLowerCase() || '';
        const name = item.querySelector('.text-sm')?.textContent.toLowerCase() || '';
        const matchesSearch = orderNum.includes(searchTerm) || name.includes(searchTerm);

        if (matchesSearch) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });

    // 2. Sort items
    const sortedItems = items.sort((a, b) => {
        if (sortOrder === 'high-low') {
            return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
        } else if (sortOrder === 'low-high') {
            return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
        } else if (sortOrder === 'random') {
            return parseInt(a.dataset.random) - parseInt(b.dataset.random);
        }
        return 0;
    });

    // 3. Re-append sorted items to container
    sortedItems.forEach(item => container.appendChild(item));
}

// Attach listeners
document.getElementById('modalSearchInput').addEventListener('input', filterModalItems);
document.getElementById('modalSortFilter').addEventListener('change', filterModalItems);

// Transaction selection
function selectTransaction(reference, amount, date, type, orderId, remainingBalance = null) {
    document.getElementById('confirmRef').textContent = reference;
    document.getElementById('confirmAmount').textContent = '₱' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('confirmDate').textContent = date;
    document.getElementById('confirmType').textContent = type;

    // Use remaining balance for both Expense and Income types, fall back to full amount if not available
    const paymentCap = remainingBalance !== null ? remainingBalance : amount;

    // Store max amount for validation
    maxPaymentAmount = paymentCap;

    // Set hidden form fields
    document.getElementById('formRef').value = reference;
    document.getElementById('formTotalAmount').value = amount;
    document.getElementById('formDate').value = date;
    document.getElementById('formType').value = type;
    document.getElementById('formOrderId').value = orderId;
    document.getElementById('paymentAmount').value = paymentCap.toLocaleString('en-US');

    // Display max amount with formatting
    document.getElementById('maxAmount').textContent = '₱' + paymentCap.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Hide transaction list and show confirmation
    document.getElementById('salesOrdersContainer').classList.add('hidden');
    document.getElementById('purchaseOrdersContainer').classList.add('hidden');
    document.getElementById('confirmationSection').classList.remove('hidden');
}

// Format payment amount input with commas and enforce max limit
document.getElementById('paymentAmount').addEventListener('input', function (e) {
    let value = this.value.replace(/,/g, '');
    let numValue = parseInt(value) || 0;

    // Enforce max limit
    if (numValue > maxPaymentAmount) {
        numValue = maxPaymentAmount;
    }

    if (numValue > 0) {
        this.value = numValue.toLocaleString('en-US');
    } else {
        this.value = '';
    }
});

let isConfirming = false;

// Handle confirmation modal
function confirmTransaction(event) {
    if (event) event.preventDefault();

    let paymentAmount = document.getElementById('paymentAmount').value.replace(/,/g, '');
    let numValue = parseInt(paymentAmount) || 0;

    // Validate payment amount
    if (numValue <= 0) {
        alert('Please enter a valid payment amount');
        return false;
    }

    if (numValue > maxPaymentAmount) {
        alert('Payment amount cannot exceed ₱' + maxPaymentAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        return false;
    }

    // Populate modal with transaction details
    const reference = document.getElementById('confirmRef').textContent;
    const type = document.getElementById('confirmType').textContent;
    const formattedAmount = '₱' + numValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('modalRef').textContent = reference;
    document.getElementById('modalType').textContent = type;
    document.getElementById('modalType').className = type === 'Income'
        ? 'font-semibold text-sm text-green-600'
        : 'font-semibold text-sm text-red-600';
    document.getElementById('modalAmount').textContent = formattedAmount;

    // Show modal
    const modal = document.getElementById('confirmTransactionModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    return false;
}

function closeConfirmTransaction() {
    const modal = document.getElementById('confirmTransactionModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    isConfirming = false;
}

function submitConfirmedTransaction() {
    isConfirming = true;

    // Remove commas from payment amount before submission
    let paymentAmount = document.getElementById('paymentAmount').value.replace(/,/g, '');
    document.getElementById('paymentAmount').value = paymentAmount;

    // Close modal
    closeConfirmTransaction();

    // Submit the form
    document.getElementById('transactionForm').submit();
}

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('addTransactionModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeAddTransaction();
        }
    }
});

function resetSelection() {
    document.getElementById('confirmationSection').classList.add('hidden');
    document.getElementById('salesOrdersContainer').classList.remove('hidden');
    document.getElementById('purchaseOrdersContainer').classList.add('hidden');
}

// Export Dropdown Toggle
const exportButtonAccounting = document.getElementById('exportButtonAccounting');
const exportDropdownAccounting = document.getElementById('exportDropdownAccounting');

if (exportButtonAccounting && exportDropdownAccounting) {
    exportButtonAccounting.addEventListener('click', function (e) {
        e.stopPropagation();
        const isHidden = exportDropdownAccounting.classList.contains('hidden');

        if (isHidden) {
            // Position the dropdown below the button
            const rect = exportButtonAccounting.getBoundingClientRect();
            exportDropdownAccounting.style.top = (rect.bottom + 8) + 'px';
            exportDropdownAccounting.style.right = (window.innerWidth - rect.right) + 'px';
            exportDropdownAccounting.classList.remove('hidden');
        } else {
            exportDropdownAccounting.classList.add('hidden');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!exportButtonAccounting.contains(e.target) && !exportDropdownAccounting.contains(e.target)) {
            exportDropdownAccounting.classList.add('hidden');
        }
    });
}

// Add row selection functionality for transactions
const transactionTable = document.querySelector('.transaction-row');
if (transactionTable) {
    document.addEventListener('click', function (e) {
        const row = e.target.closest('tr.transaction-row');

        if (row) {
            // Remove selection from all rows
            const allRows = document.querySelectorAll('tr.transaction-row');
            allRows.forEach(r => {
                r.classList.remove('!bg-gray-600', 'border-l-4', 'border-amber-500');
            });

            // Add selection to clicked row using Tailwind classes
            row.classList.add('!bg-gray-600', 'border-l-4', 'border-amber-500');
        }
    });
}

// Tab switching functionality
function showAccountingTab(tab) {
    const transactionTab = document.getElementById('transactionTab');
    const reportsTab = document.getElementById('reportsTab');
    const transactionContent = document.getElementById('transactionContent');
    const reportsContent = document.getElementById('reportsContent');

    if (tab === 'transaction') {
        transactionTab.style.backgroundColor = '#FFF1DA';
        transactionTab.style.color = '#111827';
        transactionTab.style.borderColor = '#FDE68A';
        reportsTab.style.backgroundColor = '#475569';
        reportsTab.style.color = '#FFFFFF';
        reportsTab.style.borderColor = '#64748b';
        transactionContent.classList.remove('hidden');
        reportsContent.classList.add('hidden');
    } else if (tab === 'reports') {
        reportsTab.style.backgroundColor = '#FFF1DA';
        reportsTab.style.color = '#111827';
        reportsTab.style.borderColor = '#FDE68A';
        transactionTab.style.backgroundColor = '#475569';
        transactionTab.style.color = '#FFFFFF';
        transactionTab.style.borderColor = '#64748b';
        transactionContent.classList.add('hidden');
        reportsContent.classList.remove('hidden');
    }
}

// Export Functions
function exportTransactionReceipt(event) {
    event.preventDefault();
    exportDropdownAccounting.classList.add('hidden');

    // Get the selected transaction from the table
    const selectedRow = document.querySelector('tr.transaction-row.border-amber-500');

    // Extract transaction details from the selected row
    const transactionId = selectedRow.getAttribute('data-id');
    const status = selectedRow.getAttribute('data-status');

    if (status !== 'paid') {
        showErrorNotification('Receipts can only be exported for fully paid orders.');
        return;
    }

    // Open receipt in new tab
    window.open(`/accounting/receipt/${transactionId}`, '_blank');
}

function exportFinancialReport(event) {
    event.preventDefault();
    exportDropdownAccounting.classList.add('hidden');
    // Implement financial report export (PDF)
    window.location.href = '/accounting/export/financial-report';
}

function exportTransactionHistory(event) {
    event.preventDefault();
    exportDropdownAccounting.classList.add('hidden');
    // Implement transaction history export (CSV)
    window.location.href = '/accounting/export/transactions';
}

// Toast Notification Functions
function showSuccessNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
    notif.innerHTML = `
        <div class="flex items-center gap-3 bg-green-100 border-2 border-green-400 text-green-800 rounded-lg px-6 py-4 shadow-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-sm">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 4000);
}

function showErrorNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
    notif.innerHTML = `
        <div class="flex items-center gap-3 bg-red-100 border-2 border-red-400 text-red-800 rounded-lg px-6 py-4 shadow-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium text-sm">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 5000);
}
