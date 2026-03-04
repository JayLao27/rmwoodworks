	function openModal(modalId) {
		document.getElementById(modalId).classList.remove('hidden');
	}

	function closeModal(modalId) {
		document.getElementById(modalId).classList.add('hidden');
		if (modalId === 'newOrderModal') { resetSalesOrderItems(); }
	}

	function resetSalesOrderItems() {
		const container = document.getElementById('salesItemsContainer');
		if (!container) return;
		const rows = container.querySelectorAll('.sales-item-row');
		rows.forEach((row, i) => { if (i > 0) row.remove(); });
		const firstRow = container.querySelector('.sales-item-row');
		if (firstRow) {
			const select = firstRow.querySelector('.sales-product-select');
			if (select) select.value = '';
			const searchInput = firstRow.querySelector('.sales-product-search');
			if (searchInput) { searchInput.value = ''; searchInput.classList.remove('hidden'); }
			const badge = firstRow.querySelector('.sales-product-badge');
			if (badge) { badge.classList.add('hidden'); badge.classList.remove('flex'); }
			const unitPrice = firstRow.querySelector('.sales-item-unit-price');
			if (unitPrice) unitPrice.value = '';
			const unitPriceHidden = firstRow.querySelector('.sales-item-unit-price-hidden');
			if (unitPriceHidden) unitPriceHidden.value = '';
			const lineTotal = firstRow.querySelector('.sales-item-line-total');
			if (lineTotal) lineTotal.textContent = 'â‚±0.00';
			firstRow.querySelectorAll('.sales-product-option').forEach(opt => opt.classList.remove('hidden'));
			const qtyInput = firstRow.querySelector('.sales-item-qty');
			if (qtyInput) qtyInput.value = '';
		}
		const grandTotal = document.getElementById('salesGrandTotal');
		if (grandTotal) grandTotal.textContent = 'â‚±0.00';
	}

	function openEditCustomerModal(customerId, name, type, phone, email) {
		document.getElementById('editCustomerName').value = name || '';
		document.getElementById('editCustomerType').value = type || 'Retail';
		document.getElementById('editCustomerPhone').value = phone || '';
		document.getElementById('editCustomerEmail').value = email || '';
		document.getElementById('editCustomerForm').action = `/customers/${customerId}`;
		openModal('editCustomerModal');
	}


	function openViewCustomerModalFromData(btn) {
		const data = btn.getAttribute('data-customer');
		if (!data) return;
		const customer = JSON.parse(data);

		// Customer type badge colors
		const typeColors = {
			'Wholesale': '#64B5F6',
			'Retail': '#6366F1',
			'Contractor': '#BA68C8'
		};

		document.getElementById('viewCustomerName').textContent = customer.name || 'â€”';
		const typeElement = document.getElementById('viewCustomerType');
		typeElement.innerHTML = `<span class="px-2 py-0.5 rounded text-white text-xs" style="background: ${typeColors[customer.type] || '#e5e7eb'};">${customer.type}</span>`;
		document.getElementById('viewCustomerPhone').textContent = customer.phone || 'â€”';
		document.getElementById('viewCustomerEmail').textContent = customer.email || 'â€”';
		document.getElementById('viewCustomerOrders').textContent = customer.totalOrders || '0';
		document.getElementById('viewCustomerSpent').textContent = 'â‚±' + (customer.totalSpent ? parseFloat(customer.totalSpent).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : '0.00');

		const productsContainer = document.getElementById('viewCustomerProducts');
		if (customer.productsSummary && Object.keys(customer.productsSummary).length > 0) {
			let tableHTML = `
				<table class="w-full text-sm border-collapse">
					<thead>
						<tr class="bg-slate-700 text-white">
							<th class="px-3 py-2 text-left font-semibold">Product Name</th>
							<th class="px-3 py-2 text-right font-semibold">Total Quantity</th>
						</tr>
					</thead>
					<tbody>
				`;
			for (const [productName, totalQty] of Object.entries(customer.productsSummary)) {
				tableHTML += `
					<tr class="border-b border-gray-200 hover:bg-gray-50 transition">
						<td class="px-3 py-2">${productName}</td>
						<td class="px-3 py-2 text-right font-medium">${totalQty} pcs</td>
					</tr>
				`;
			}
		
			tableHTML += `
					</tbody>
				</table>
			`;
			productsContainer.innerHTML = tableHTML;
		} else {
			productsContainer.innerHTML = '<p class="text-slate-500 text-sm p-4 text-center">No products purchased yet</p>';
		}

		openModal('viewCustomerModal');
	}

	(function() {
		const salesTab = document.getElementById('salesTab');
		const customersTab = document.getElementById('customersTab');
		const salesTable = document.getElementById('salesTable');
		const customerTable = document.getElementById('customerTable');
		const headerBtn = document.getElementById('headerBtn');
		const searchInput = document.getElementById('searchInput');
		const statusFilter = document.getElementById('statusFilter');
		const paymentFilter = document.getElementById('paymentFilter');
		const salesTbody = document.getElementById('salesTbody');
		const customersTbody = document.getElementById('customersTbody');
		const salesNoMatch = document.getElementById('salesNoMatch');
		const customersNoMatch = document.getElementById('customersNoMatch');

		function setMode(mode) {
			salesTable.classList.add('hidden');
			customerTable.classList.add('hidden');

			const allTabs = [salesTab, customersTab];
			allTabs.forEach(t => {
				t.style.backgroundColor = '#475569';
				t.style.color = '#FFFFFF';
				t.style.borderColor = '#64748b';
			});

			if (mode === 'sales') {
				salesTable.classList.remove('hidden');
				headerBtn.textContent = '+ New Order';
				headerBtn.setAttribute('onclick', 'openModal("newOrderModal")');
				salesTab.style.backgroundColor = '#FFF1DA';
				salesTab.style.color = '#111827';
				salesTab.style.borderColor = 'transparent';
			} else {
				customerTable.classList.remove('hidden');
				headerBtn.textContent = '+ New Customer';
				headerBtn.setAttribute('onclick', 'openModal("newCustomerModal")');
				customersTab.style.backgroundColor = '#FFF1DA';
				customersTab.style.color = '#111827';
				customersTab.style.borderColor = 'transparent';
			}
			applyFilters();
		}

		salesTab.addEventListener('click', () => setMode('sales'));
		customersTab.addEventListener('click', () => setMode('customers'));


		function stringIncludes(haystack, needle) {
			return haystack.toLowerCase().includes(needle.toLowerCase());
		}

		function applyFilters() {
			const q = searchInput.value.trim();
			const sVal = statusFilter ? statusFilter.value : '';
			const pVal = paymentFilter.value;
			const inSales = !salesTable.classList.contains('hidden');

			if (inSales) {
				let any = false;
				const rows = salesTbody.querySelectorAll('tr.data-row');
				rows.forEach(tr => {
					let show = true;
					if (q) {
						const text = tr.textContent || '';
						show = show && stringIncludes(text, q);
					}
					if (sVal) {
						show = show && (tr.dataset.status === sVal);
					}
					if (pVal) {
						show = show && (tr.dataset.payment === pVal);
					}
					tr.classList.toggle('hidden', !show);
					any = any || show;
				});
				salesNoMatch.classList.toggle('hidden', any);
			} else {
				let any = false;
				const rows = customersTbody.querySelectorAll('tr.data-row');
				rows.forEach(tr => {
					let show = true;
					if (q) {
						const nameCell = tr.querySelector('td:nth-child(1)');
						const typeCell = tr.querySelector('td:nth-child(2)');
						const contactCell = tr.querySelector('td:nth-child(3)');
						const hay = [nameCell, typeCell, contactCell].map(c => (c?.textContent || '')).join(' ');
						show = show && stringIncludes(hay, q);
					}
					tr.classList.toggle('hidden', !show);
					any = any || show;
				});
				if (customersNoMatch) customersNoMatch.classList.toggle('hidden', any);
			}
		}


		searchInput.addEventListener('input', applyFilters);
		if (statusFilter) statusFilter.addEventListener('change', applyFilters);
		paymentFilter.addEventListener('change', applyFilters);


		// ---- Contact method validation for customer forms ----
		function validateContactMethod(phoneInput, emailInput, errorMessageEl) {
			const phone = (phoneInput?.value || '').trim();
			const email = (emailInput?.value || '').trim();
			const hasError = !phone && !email;

			if (hasError) {
				phoneInput?.classList.add('border-red-500');
				emailInput?.classList.add('border-red-500');
				if (errorMessageEl) {
					errorMessageEl.textContent = 'Please provide at least a phone number or email address.';
					errorMessageEl.classList.remove('hidden');
				}
			} else {
				phoneInput?.classList.remove('border-red-500');
				emailInput?.classList.remove('border-red-500');
				if (errorMessageEl) {
					errorMessageEl.classList.add('hidden');
					errorMessageEl.textContent = '';
				}
			}

			return !hasError;
		}

		// New Customer form validation
		const newCustomerForm = document.getElementById('newCustomerForm');
		if (newCustomerForm) {
			const newPhoneInput = document.getElementById('newCustomerPhone');
			const newEmailInput = document.getElementById('newCustomerEmail');
			const newContactError = document.getElementById('newCustomerContactError');

			newCustomerForm.addEventListener('submit', function(e) {
				if (!validateContactMethod(newPhoneInput, newEmailInput, newContactError)) {
					e.preventDefault();
					return false;
				}
			});

			// Real-time validation as user types
			newPhoneInput?.addEventListener('blur', () => validateContactMethod(newPhoneInput, newEmailInput, newContactError));
			newEmailInput?.addEventListener('blur', () => validateContactMethod(newPhoneInput, newEmailInput, newContactError));
		}

		// Edit Customer form validation (for each customer)
		const editCustomerForms = document.querySelectorAll('.editCustomerForm');
		editCustomerForms.forEach(form => {
			const phoneInput = form.querySelector('.editCustomerPhone');
			const emailInput = form.querySelector('.editCustomerEmail');
			const contactError = form.querySelector('.editCustomerContactError');

			form.addEventListener('submit', function(e) {
				if (!validateContactMethod(phoneInput, emailInput, contactError)) {
					e.preventDefault();
					return false;
				}
			});

			// Real-time validation as user types
			phoneInput?.addEventListener('blur', () => validateContactMethod(phoneInput, emailInput, contactError));
			emailInput?.addEventListener('blur', () => validateContactMethod(phoneInput, emailInput, contactError));
		});

		// default: sales tab
		setMode('sales');
	})();

// ---- Sales Multi-Row Product Functions ----
var salesRowIndex = 0;

function salesToNumber(value) {
	const n = parseFloat(value);
	return Number.isFinite(n) ? n : 0;
}

function salesFormatCurrency(num) {
	return 'â‚±' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function salesFetchLatestPrice(productId) {
	if (!productId) return 0;
	try {
		const response = await fetch(`/inventory/${productId}/details?type=product`);
		const data = await response.json();
		if (data && data.item && data.item.selling_price) {
			return salesToNumber(data.item.selling_price);
		}
	} catch (error) {
		console.error('Error fetching latest price:', error);
	}
	return 0;
}

function updateSalesRowTotal(el) {
	const row = el.closest('.sales-item-row');
	if (!row) return;
	const select = row.querySelector('.sales-product-select');
	const qtyInput = row.querySelector('.sales-item-qty');
	const unitPriceDisplay = row.querySelector('.sales-item-unit-price');
	const unitPriceHidden = row.querySelector('.sales-item-unit-price-hidden');
	const lineTotalDisplay = row.querySelector('.sales-item-line-total');

	let unit = 0;
	if (select) {
		const opt = select.options[select.selectedIndex];
		unit = salesToNumber(opt ? opt.getAttribute('data-price') : null);
	}
	const qty = salesToNumber(qtyInput?.value || '0');
	if (unitPriceDisplay) unitPriceDisplay.value = unit ? salesFormatCurrency(unit) : '';
	if (unitPriceHidden) unitPriceHidden.value = unit ? unit.toFixed(2) : '';
	const total = unit * (qty || 0);
	if (lineTotalDisplay) lineTotalDisplay.textContent = salesFormatCurrency(total);
	updateSalesGrandTotal();
}

function updateSalesGrandTotal() {
	let grand = 0;
	document.querySelectorAll('.sales-item-row').forEach(row => {
		const select = row.querySelector('.sales-product-select');
		const qtyInput = row.querySelector('.sales-item-qty');
		let unit = 0;
		if (select) {
			const opt = select.options[select.selectedIndex];
			unit = salesToNumber(opt ? opt.getAttribute('data-price') : null);
		}
		const qty = salesToNumber(qtyInput?.value || '0');
		grand += unit * (qty || 0);
	});
	const el = document.getElementById('salesGrandTotal');
	if (el) el.textContent = salesFormatCurrency(grand);
}

function addSalesProductRow() {
	salesRowIndex++;
	const container = document.getElementById('salesItemsContainer');
	const firstRow = container.querySelector('.sales-item-row');
	const newRow = firstRow.cloneNode(true);
	const idx = salesRowIndex;

	newRow.setAttribute('data-index', idx);

	const select = newRow.querySelector('.sales-product-select');
	if (select) { select.name = `items[${idx}][product_id]`; select.value = ''; }
	const qtyInput = newRow.querySelector('.sales-item-qty');
	if (qtyInput) { qtyInput.name = `items[${idx}][quantity]`; qtyInput.value = ''; }
	const unitPriceHidden = newRow.querySelector('.sales-item-unit-price-hidden');
	if (unitPriceHidden) { unitPriceHidden.name = `items[${idx}][unit_price]`; unitPriceHidden.value = ''; }
	const unitPriceDisplay = newRow.querySelector('.sales-item-unit-price');
	if (unitPriceDisplay) unitPriceDisplay.value = '';

	const searchInput = newRow.querySelector('.sales-product-search');
	if (searchInput) { searchInput.value = ''; searchInput.classList.remove('hidden'); }

	const badge = newRow.querySelector('.sales-product-badge');
	if (badge) { badge.classList.add('hidden'); badge.classList.remove('flex'); }

	const lineTotal = newRow.querySelector('.sales-item-line-total');
	if (lineTotal) lineTotal.textContent = 'â‚±0.00';

	const resultsList = newRow.querySelector('.sales-product-results');
	if (resultsList) resultsList.classList.add('hidden');

	newRow.querySelectorAll('.sales-product-option').forEach(opt => opt.classList.remove('hidden'));
	newRow.querySelector('.sales-product-no-match')?.classList.add('hidden');

	const qtyError = newRow.querySelector('.sales-item-qty-error');
	if (qtyError) { qtyError.textContent = ''; qtyError.classList.add('hidden'); }

	const removeBtn = document.createElement('button');
	removeBtn.type = 'button';
	removeBtn.className = 'absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all shadow-md';
	removeBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
	removeBtn.onclick = function() {
		newRow.style.transition = 'all 0.3s ease-out';
		newRow.style.opacity = '0';
		newRow.style.transform = 'scale(0.95)';
		setTimeout(() => { newRow.remove(); updateSalesGrandTotal(); }, 300);
	};
	newRow.style.position = 'relative';
	newRow.appendChild(removeBtn);

	newRow.style.opacity = '0';
	newRow.style.transform = 'translateY(-10px)';
	container.appendChild(newRow);
	requestAnimationFrame(() => {
		newRow.style.transition = 'all 0.3s ease-out';
		newRow.style.opacity = '1';
		newRow.style.transform = 'translateY(0)';
	});
}

function filterSalesProductList(input) {
	const wrapper = input.closest('.sales-product-search-wrapper');
	const term = input.value.toLowerCase().trim();
	const options = wrapper.querySelectorAll('.sales-product-option');
	const noMatch = wrapper.querySelector('.sales-product-no-match');
	const resultsList = wrapper.querySelector('.sales-product-results');
	let count = 0;

	resultsList.classList.remove('hidden');
	options.forEach(opt => {
		const name = opt.getAttribute('data-name').toLowerCase();
		if (name.includes(term)) { opt.classList.remove('hidden'); count++; }
		else { opt.classList.add('hidden'); }
	});
	noMatch.classList.toggle('hidden', count > 0);
}

async function selectSalesProduct(btn) {
	const row = btn.closest('.sales-item-row');
	const id = btn.getAttribute('data-id');
	const name = btn.getAttribute('data-name');
	let price = parseFloat(btn.getAttribute('data-price'));

	const hiddenSelect = row.querySelector('.sales-product-select');
	hiddenSelect.value = id;

	const searchInput = row.querySelector('.sales-product-search');
	searchInput.value = name;
	searchInput.classList.add('hidden');

	const resultsList = row.querySelector('.sales-product-results');
	resultsList.classList.add('hidden');

	const badge = row.querySelector('.sales-product-badge');
	badge.classList.remove('hidden');
	badge.classList.add('flex');
	row.querySelector('.sales-product-selected-name').textContent = name;
	row.querySelector('.sales-product-initial').textContent = name.charAt(0).toUpperCase();

	// Fetch latest price
	const unitPriceDisplay = row.querySelector('.sales-item-unit-price');
	if (unitPriceDisplay) unitPriceDisplay.value = 'Fetching...';

	const latestPrice = await salesFetchLatestPrice(id);
	if (latestPrice > 0) price = latestPrice;

	const opt = hiddenSelect.querySelector(`option[value="${id}"]`);
	if (opt) opt.setAttribute('data-price', price.toFixed(2));

	row.querySelector('.sales-product-selected-price').textContent = salesFormatCurrency(price);
	if (unitPriceDisplay) unitPriceDisplay.value = salesFormatCurrency(price);
	const unitPriceHidden = row.querySelector('.sales-item-unit-price-hidden');
	if (unitPriceHidden) unitPriceHidden.value = price.toFixed(2);

	const qtyInput = row.querySelector('.sales-item-qty');
	if (qtyInput && (!qtyInput.value || salesToNumber(qtyInput.value) < 1)) {
		qtyInput.value = '1';
	}
	updateSalesRowTotal(btn);
}

function clearSalesProductSelection(btn) {
	const row = btn.closest('.sales-item-row');
	const hiddenSelect = row.querySelector('.sales-product-select');
	hiddenSelect.value = '';

	const input = row.querySelector('.sales-product-search');
	input.value = '';
	input.classList.remove('hidden');

	const badge = row.querySelector('.sales-product-badge');
	badge.classList.add('hidden');
	badge.classList.remove('flex');

	row.querySelectorAll('.sales-product-option').forEach(opt => opt.classList.remove('hidden'));
	row.querySelector('.sales-product-no-match')?.classList.add('hidden');

	const unitPriceDisplay = row.querySelector('.sales-item-unit-price');
	const unitPriceHidden = row.querySelector('.sales-item-unit-price-hidden');
	if (unitPriceDisplay) unitPriceDisplay.value = '';
	if (unitPriceHidden) unitPriceHidden.value = '';
	const lineTotal = row.querySelector('.sales-item-line-total');
	if (lineTotal) lineTotal.textContent = 'â‚±0.00';

	input.focus();
	updateSalesGrandTotal();
}

// Close product dropdown on outside click
document.addEventListener('click', function(e) {
	document.querySelectorAll('.sales-product-search-wrapper').forEach(wrapper => {
		const results = wrapper.querySelector('.sales-product-results');
		if (results && !wrapper.contains(e.target)) {
			results.classList.add('hidden');
		}
	});
});




	// Auto-sync Due Date with Delivery Date
	document.addEventListener('DOMContentLoaded', function() {
		// For New Order Modal
		const newDeliveryDate = document.getElementById('newOrderDeliveryDate');
		const newDueDate = document.getElementById('newOrderDueDate');
		if (newDeliveryDate && newDueDate) {
			newDeliveryDate.addEventListener('change', function() {
				if (!newDueDate.value || newDueDate.dataset.autoSynced === 'true' || newDueDate.value === this.dataset.lastAutoValue) {
					newDueDate.value = this.value;
					newDueDate.dataset.autoSynced = 'true';
					this.dataset.lastAutoValue = this.value;
				}
			});
			newDueDate.addEventListener('input', function() {
				this.dataset.autoSynced = 'false';
			});
		}

		// For Edit Order Modals (Delegated)
		document.addEventListener('change', function(e) {
			if (e.target.classList.contains('editDeliveryDate')) {
				const container = e.target.closest('form') || e.target.closest('div.grid');
				const dueInput = container.querySelector('.editDueDate');
				if (dueInput) {
					if (!dueInput.value || dueInput.dataset.autoSynced === 'true' || dueInput.value === e.target.dataset.lastAutoValue) {
						dueInput.value = e.target.value;
						dueInput.dataset.autoSynced = 'true';
						e.target.dataset.lastAutoValue = e.target.value;
					}
				}
			}
		});
		document.addEventListener('input', function(e) {
			if (e.target.classList.contains('editDueDate')) {
				e.target.dataset.autoSynced = 'false';
			}
		});
	});



	// Export Dropdown Toggle 
	const exportButton = document.getElementById('exportButton');
	const exportDropdown = document.getElementById('exportDropdown');

	if (exportButton && exportDropdown) {
		exportButton.addEventListener('click', function(e) {
			e.preventDefault(); // Prevent default action
			e.stopPropagation();

			// Toggle dropdown visibility
			exportDropdown.classList.toggle('hidden');
		});

		// Close dropdown when clicking outside
		document.addEventListener('click', function(e) {
			if (!exportButton.contains(e.target) && !exportDropdown.contains(e.target)) {
				exportDropdown.classList.add('hidden');
			}
		});

		// Prevent dropdown from closing when clicking inside it
		exportDropdown.addEventListener('click', function(e) {
			e.stopPropagation();
		});
	}

	// Add row selection functionality
	const salesTbody = document.getElementById('salesTbody');
	if (salesTbody) {
		salesTbody.addEventListener('click', function(e) {
			const row = e.target.closest('tr.data-row');

			// Ignore clicks on action buttons
			if (e.target.closest('button') || e.target.closest('form')) {
				return;
			}

			if (row) {
				// Remove selection from all rows
				const allRows = salesTbody.querySelectorAll('tr.data-row');
				allRows.forEach(r => {
					r.classList.remove('!bg-gray-600', 'border-l-4', 'border-amber-500');
				});

				// Add selection to clicked row using Tailwind classes
				row.classList.add('!bg-gray-600', 'border-l-4', 'border-amber-500');
			}
		});
	}

	// Export Functions
	function exportReceipt(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');

		// Check if a row is selected
		const selectedRow = document.querySelector('tr.data-row.border-amber-500');

		if (selectedRow) {
			// Get order number from the selected row
			const orderNumber = selectedRow.querySelector('td:first-child').textContent.trim();
			window.open(`/sales/receipt/${orderNumber}`, '_blank');
			return;
		}

		// If no row is selected, prompt user
		showErrorNotification('Please select an order by clicking on a row in the table to export as receipt.');
	}

	function exportSalesReport(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');
		// Implement sales report export (CSV/PDF)
		window.location.href = '/sales/export/report';
	}

	function exportCustomerList(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');
		// Implement customer list export (CSV/Excel)
		window.location.href = '/customers/export';
	}

	// Confirmation Modal Functions for Sales Order
	function confirmSalesOrder(event) {
		event.preventDefault();
		document.getElementById('confirmSalesOrderModal').style.display = 'flex';
		return false;
	}

	function closeConfirmSalesOrder() {
		document.getElementById('confirmSalesOrderModal').style.display = 'none';
	}

	function submitSalesOrder() {
		closeConfirmSalesOrder();
		// Show success notification before submitting form
		showSuccessNotification('Sales order created successfully!');
		// Submit the form normally (let Laravel handle it)
		const form = document.getElementById('newOrderForm');
		form.onsubmit = null; 
		setTimeout(() => {
			form.submit();
		}, 2000);
	}

	// Confirmation Modal Functions for Cancelling Sales Order
	let currentCancelOrderId = null;

	function openCancelSalesOrderModal(id, orderNumber) {
		currentCancelOrderId = id;
		document.getElementById('cancelSalesOrderNumber').textContent = orderNumber;
		const modal = document.getElementById('confirmCancelSalesOrderModal');
		modal.style.display = 'flex';
	}

	function closeCancelSalesOrderModal() {
		const modal = document.getElementById('confirmCancelSalesOrderModal');
		modal.style.display = 'none';
	}

	function submitCancelSalesOrder() {
		const orderNumber = document.getElementById('cancelSalesOrderNumber').textContent;
		
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/sales-orders/${currentCancelOrderId}`;

		const csrf = document.createElement('input');
		csrf.type = 'hidden';
		csrf.name = '_token';
		csrf.value = document.querySelector('meta[name="csrf-token"]').content;

		const method = document.createElement('input');
		method.type = 'hidden';
		method.name = '_method';
		method.value = 'DELETE';

		form.appendChild(csrf);
		form.appendChild(method);
		document.body.appendChild(form);
		
		closeCancelSalesOrderModal();
		showSuccessNotification(`Sales order ${orderNumber} has been cancelled successfully.`);
		setTimeout(() => {
			form.submit();
		}, 2000);
	}

	// Confirmation Modal Functions for Customer
	function confirmCustomer(event) {
		event.preventDefault();
		document.getElementById('confirmCustomerModal').style.display = 'flex';
		return false;
	}

	function closeConfirmCustomer() {
		document.getElementById('confirmCustomerModal').style.display = 'none';
	}

	function submitCustomer() {
		closeConfirmCustomer();
		// Submit the form without showing success message
		// Let server validate and show success after successful validation
		const form = document.getElementById('newCustomerForm');
		// Remove the onsubmit to avoid infinite loop
		form.onsubmit = null;
		form.submit();
	}

	// Delete Customer Modal Functions
	let currentDeleteCustomerId = null;

	function openDeleteCustomerModal(id, customerName) {
		currentDeleteCustomerId = id;
		document.getElementById('deleteCustomerName').textContent = customerName;
		const modal = document.getElementById('deleteCustomerModal');
		modal.style.display = 'flex';
	}

	function closeDeleteCustomerModal() {
		const modal = document.getElementById('deleteCustomerModal');
		modal.style.display = 'none';
	}

	function submitDeleteCustomer() {
		const customerName = document.getElementById('deleteCustomerName').textContent;
		
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/customers/${currentDeleteCustomerId}`;

		const csrf = document.createElement('input');
		csrf.type = 'hidden';
		csrf.name = '_token';
		csrf.value = document.querySelector('meta[name="csrf-token"]').content;

		const method = document.createElement('input');
		method.type = 'hidden';
		method.name = '_method';
		method.value = 'DELETE';

		form.appendChild(csrf);
		form.appendChild(method);
		document.body.appendChild(form);
		
		closeDeleteCustomerModal();
		showSuccessNotification(`Customer ${customerName} has been deleted successfully.`);
		setTimeout(() => {
			form.submit();
		}, 2000);
	}



// Ensure date inputs do not allow past dates (handles browser & timezone differences)
document.addEventListener('DOMContentLoaded', function() {
	try {
		const today = new Date();
		const yyyy = today.getFullYear();
		const mm = String(today.getMonth() + 1).padStart(2, '0');
		const dd = String(today.getDate()).padStart(2, '0');
		const iso = `${yyyy}-${mm}-${dd}`;
		document.querySelectorAll('input[type="date"][name="delivery_date"]').forEach(function(el) {
			// set or override min to today's date
			el.setAttribute('min', iso);
		});
	} catch (e) {
		console.warn('Could not set min for delivery_date inputs', e);
	}
});

// --- Second Script Block ---

let currentArchiveStatus = 'all';

function filterArchive(status) {
	currentArchiveStatus = status;
	
	// Update button styles
	document.querySelectorAll('.archive-filter-btn').forEach(btn => {
		btn.classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
		btn.classList.add('text-gray-500', 'hover:bg-gray-200');
	});
	
	const btnId = 'archiveFilter' + (status === 'all' ? 'All' : status);
	const activeBtn = document.getElementById(btnId);
	if (activeBtn) {
		activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-200');
		activeBtn.classList.add('bg-amber-500', 'text-white', 'shadow-lg');
	}

	applyArchiveFilters();
}

function applyArchiveFilters() {
	const q = document.getElementById('archiveSearchInput')?.value.trim().toLowerCase() || '';
	const start = document.getElementById('archiveStartDate')?.value || '';
	const end = document.getElementById('archiveEndDate')?.value || '';
	const tbody = document.getElementById('archiveTbody');
	const rows = tbody.querySelectorAll('tr.data-row');
	const noMatch = document.getElementById('archiveNoMatch');
	let any = false;

	rows.forEach(tr => {
		const text = (tr.textContent || '').toLowerCase();
		const status = tr.getAttribute('data-status');
		const completedDate = tr.getAttribute('data-completed-date');
		
		const matchesSearch = !q || text.includes(q);
		const matchesStatus = currentArchiveStatus === 'all' || status === currentArchiveStatus;
		
		let matchesDate = true;
		if (start || end) {
			if (completedDate) {
				if (start && completedDate < start) matchesDate = false;
				if (end && completedDate > end) matchesDate = false;
			} else {
				matchesDate = false;
			}
		}
		
		const show = matchesSearch && matchesStatus && matchesDate;
		tr.classList.toggle('hidden', !show);
		any = any || show;
	});

	if (noMatch) noMatch.classList.toggle('hidden', any);
}

function applyArchiveQuickFilter(type) {
	const startInput = document.getElementById('archiveStartDate');
	const endInput = document.getElementById('archiveEndDate');
	const now = new Date();
	const today = now.toISOString().split('T')[0];
	
	let start = '';
	let end = today;

	switch(type) {
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
	applyArchiveFilters();
}

function clearArchiveFilters() {
	const searchInput = document.getElementById('archiveSearchInput');
	const startInput = document.getElementById('archiveStartDate');
	const endInput = document.getElementById('archiveEndDate');
	
	if (searchInput) searchInput.value = '';
	if (startInput) startInput.value = '';
	if (endInput) endInput.value = '';
	
	filterArchive('all');
}

function openDeliverOrderModal(id, number) {
	document.getElementById('deliverOrderNumber').textContent = number;
	document.getElementById('deliverOrderForm').action = `/sales-orders/${id}/deliver`;
	const modal = document.getElementById('deliverOrderModal');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

// Archive modal events
document.addEventListener('DOMContentLoaded', function() {
	const archiveSearch = document.getElementById('archiveSearchInput');
	if (archiveSearch) {
		archiveSearch.addEventListener('input', applyArchiveFilters);
	}
	const archiveStart = document.getElementById('archiveStartDate');
	const archiveEnd = document.getElementById('archiveEndDate');
	if (archiveStart) archiveStart.addEventListener('change', applyArchiveFilters);
	if (archiveEnd) archiveEnd.addEventListener('change', applyArchiveFilters);

	// Close customer dropdown on outside click
	document.addEventListener('click', function(e) {
		const wrapper = document.getElementById('customerSearchWrapper');
		const results = document.getElementById('customerResultsList');
		if (wrapper && results && !wrapper.contains(e.target)) {
			results.classList.add('hidden');
		}
	});
});

// Customer searchable typeahead
function filterCustomerList() {
	const input = document.getElementById('customerSearchInput');
	const term = input.value.toLowerCase().trim();
	const options = document.querySelectorAll('.customer-option');
	const noMatch = document.getElementById('customerNoMatch');
	const resultsList = document.getElementById('customerResultsList');
	let count = 0;

	resultsList.classList.remove('hidden');
	options.forEach(opt => {
		const name = opt.getAttribute('data-name').toLowerCase();
		const type = opt.getAttribute('data-type').toLowerCase();
		if (name.includes(term) || type.includes(term)) {
			opt.classList.remove('hidden');
			count++;
		} else {
			opt.classList.add('hidden');
		}
	});
	noMatch.classList.toggle('hidden', count > 0);
}

function selectCustomer(btn) {
	const id = btn.getAttribute('data-id');
	const name = btn.getAttribute('data-name');
	const type = btn.getAttribute('data-type');

	document.getElementById('salesCustomerIdHidden').value = id;
	document.getElementById('customerSearchInput').value = name;
	document.getElementById('customerSearchInput').classList.add('hidden');
	document.getElementById('customerResultsList').classList.add('hidden');

	// Show selected badge
	const badge = document.getElementById('customerSelectedBadge');
	badge.classList.remove('hidden');
	badge.classList.add('flex');
	document.getElementById('customerSelectedName').textContent = name;
	document.getElementById('customerSelectedType').textContent = type;
	document.getElementById('customerInitial').textContent = name.charAt(0).toUpperCase();
}

function clearCustomerSelection() {
	document.getElementById('salesCustomerIdHidden').value = '';
	const input = document.getElementById('customerSearchInput');
	input.value = '';
	input.classList.remove('hidden');

	const badge = document.getElementById('customerSelectedBadge');
	badge.classList.add('hidden');
	badge.classList.remove('flex');

	// Reset all options
	document.querySelectorAll('.customer-option').forEach(opt => opt.classList.remove('hidden'));
	document.getElementById('customerNoMatch').classList.add('hidden');
	input.focus();
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
