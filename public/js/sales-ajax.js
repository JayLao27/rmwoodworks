function confirmSalesOrder(event) {
    event.preventDefault();
    const form = event.target;

    // Validate customer
    const customerIdHidden = document.getElementById('salesCustomerIdHidden');
    if (!customerIdHidden || !customerIdHidden.value) {
        showErrorNotification('Please select a customer.');
        return false;
    }

    // Validate delivery date
    const deliveryDate = form.querySelector('[name="delivery_date"]');
    if (!deliveryDate || !deliveryDate.value) {
        showErrorNotification('Please select a delivery date.');
        return false;
    }

    // Validate at least one product selected
    const productSelects = form.querySelectorAll('.sales-product-select');
    let hasProduct = false;
    for (const sel of productSelects) {
        if (sel.value) { hasProduct = true; break; }
    }
    if (!hasProduct) {
        showErrorNotification('Please select at least one product.');
        return false;
    }

    // Validate all quantities
    const qtyInputs = form.querySelectorAll('.sales-item-qty');
    for (const qty of qtyInputs) {
        if (!qty.value || parseInt(qty.value) < 1) {
            showErrorNotification('All quantities must be at least 1.');
            return false;
        }
    }

    // Open confirmation modal
    const modal = document.getElementById('confirmSalesOrderModal');
    if (modal) modal.style.display = 'flex';

    return false;
}

// Override submitSalesOrder to use AJAX (no page reload)
function submitSalesOrder() {
    const modal = document.getElementById('confirmSalesOrderModal');
    if (modal) modal.style.display = 'none';

    const form = document.getElementById('newOrderForm');
    if (!form) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
    }

    const formData = new FormData(form);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; }

        if (data.success) {
            showSuccessNotification(data.message || 'Sales order created successfully!');
            closeModal('newOrderModal');

            // Insert new row into table
            const tbody = document.getElementById('salesTbody');
            if (tbody && data.html) {
                const noMatch = document.getElementById('salesNoMatch');
                if (noMatch) noMatch.classList.add('hidden');
                const firstRow = tbody.querySelector('tr');
                if (firstRow && firstRow.children.length === 1 && firstRow.innerText.includes('No orders yet')) {
                    firstRow.remove();
                }
                tbody.insertAdjacentHTML('afterbegin', data.html);
            }

            // Inject modals for the new order
            if (data.modalHtml) {
                document.body.insertAdjacentHTML('beforeend', data.modalHtml);
            }

            // Fallback: reload if no row HTML provided
            if (!data.html) {
                setTimeout(() => window.location.reload(), 1000);
            }
        } else {
            showErrorNotification(data.message || 'Error creating order.');
        }
    })
    .catch(err => {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; }
        console.error(err);
        showErrorNotification('An error occurred. Please try again.');
    });
}

// Handle Deliver Order Form
document.addEventListener('DOMContentLoaded', function () {
    const deliverForm = document.getElementById('deliverOrderForm');
    if (deliverForm) {
        deliverForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Marking Delivered...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccessNotification(data.message);
                    closeModal('deliverOrderModal');
                    // Reload to reflect changes in table and archive
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showErrorNotification(data.message || 'Error delivering order.');
                }
            } catch (error) {
                console.error(error);
                showErrorNotification('An error occurred. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});

async function submitEditOrder(event, orderId) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    // Show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Updating...';

    try {
        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Close the edit modal
            closeModal('editOrderModal-' + orderId);

            // Replace the old row with the updated one
            const oldRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
            if (oldRow && data.html) {
                oldRow.insertAdjacentHTML('afterend', data.html);
                oldRow.remove();
            }

            // Replace the old modals with updated ones
            const oldViewModal = document.getElementById('viewOrderModal-' + orderId);
            const oldEditModal = document.getElementById('editOrderModal-' + orderId);
            if (oldViewModal) oldViewModal.remove();
            if (oldEditModal) oldEditModal.remove();

            if (data.modalHtml) {
                document.body.insertAdjacentHTML('beforeend', data.modalHtml);
            }

            showSuccessNotification(data.message);
        } else {
            showErrorNotification(data.message || 'Error updating order.');
        }
    } catch (error) {
        console.error(error);
        showErrorNotification('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }

    return false;
}