        // Tab functions
        function showTab(tab) {
            const purchaseOrdersTab = document.getElementById('purchase-orders-tab');
            const suppliersTab = document.getElementById('suppliers-tab');
            const purchaseOrdersTable = document.getElementById('purchase-orders-table');
            const suppliersTable = document.getElementById('suppliers-table');
            const purchaseOrdersButton = document.getElementById('purchaseOrdersButton');
            const suppliersButton = document.getElementById('suppliersButton');
            
            if (tab === 'purchase-orders') {
                purchaseOrdersTab.style.backgroundColor = '#FFF1DA';
                purchaseOrdersTab.style.color = '#111827';
                purchaseOrdersTab.style.borderColor = '#FDE68A';
                suppliersTab.style.backgroundColor = '#475569';
                suppliersTab.style.color = '#FFFFFF';
                suppliersTab.style.borderColor = '#64748b';
                purchaseOrdersTable.classList.remove('hidden');
                suppliersTable.classList.add('hidden');
                purchaseOrdersButton.classList.remove('hidden');
                suppliersButton.classList.add('hidden');
            } else {
                suppliersTab.style.backgroundColor = '#FFF1DA';
                suppliersTab.style.color = '#111827';
                suppliersTab.style.borderColor = '#FDE68A';
                purchaseOrdersTab.style.backgroundColor = '#475569';
                purchaseOrdersTab.style.color = '#FFFFFF';
                purchaseOrdersTab.style.borderColor = '#64748b';
                suppliersTable.classList.remove('hidden');
                purchaseOrdersTable.classList.add('hidden');
                purchaseOrdersButton.classList.add('hidden');
                suppliersButton.classList.remove('hidden');
            }
        }

        // Modal functions
        function openAddSupplierModal() {
            document.getElementById('addSupplierModal').classList.remove('hidden');
        }

        function closeAddSupplierModal() {
            document.getElementById('addSupplierModal').classList.add('hidden');
            document.getElementById('addSupplierForm').reset();
        }

        function openAddPurchaseOrderModal() {
            document.getElementById('addPurchaseOrderModal').classList.remove('hidden');
        }

        function closeAddPurchaseOrderModal() {
            document.getElementById('addPurchaseOrderModal').classList.add('hidden');
            document.getElementById('addPurchaseOrderForm').reset();
            // Remove all extra rows
            const container = document.getElementById('procurementItemsContainer');
            const rows = container.querySelectorAll('.procurement-item-row');
            rows.forEach((row, i) => {
                if (i > 0) row.remove();
            });
            // Reset first row
            const firstRow = container.querySelector('.procurement-item-row');
            if (firstRow) {
                const select = firstRow.querySelector('.proc-material-select');
                if (select) { select.value = ''; }
                const searchInput = firstRow.querySelector('.proc-material-search');
                if (searchInput) { searchInput.value = ''; searchInput.classList.remove('hidden'); }
                const badge = firstRow.querySelector('.proc-material-badge');
                if (badge) { badge.classList.add('hidden'); badge.classList.remove('flex'); }
                const unitPrice = firstRow.querySelector('.proc-item-unit-price');
                if (unitPrice) unitPrice.value = '';
                const unitPriceHidden = firstRow.querySelector('.proc-item-unit-price-hidden');
                if (unitPriceHidden) unitPriceHidden.value = '';
                const lineTotal = firstRow.querySelector('.proc-item-line-total');
                if (lineTotal) lineTotal.textContent = 'â‚±0.00';
                firstRow.querySelectorAll('.proc-material-option').forEach(opt => opt.classList.remove('hidden'));
            }
            updateProcGrandTotal();
        }

        // --- Procurement Multi-Row Material Functions ---
        var procRowIndex = 0;

        function procFormatCurrency(num) {
            return 'â‚±' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function procToNumber(value) {
            const n = parseFloat(value);
            return Number.isFinite(n) ? n : 0;
        }

        function updateProcRowTotal(el) {
            const row = el.closest('.procurement-item-row');
            if (!row) return;
            const select = row.querySelector('.proc-material-select');
            const qtyInput = row.querySelector('.proc-item-qty');
            const unitPriceDisplay = row.querySelector('.proc-item-unit-price');
            const unitPriceHidden = row.querySelector('.proc-item-unit-price-hidden');
            const lineTotalDisplay = row.querySelector('.proc-item-line-total');

            let unit = 0;
            if (select) {
                const opt = select.options[select.selectedIndex];
                unit = procToNumber(opt ? opt.getAttribute('data-price') : null);
            }
            const qty = procToNumber(qtyInput?.value || '0');
            if (unitPriceDisplay) unitPriceDisplay.value = unit ? procFormatCurrency(unit) : '';
            if (unitPriceHidden) unitPriceHidden.value = unit ? unit.toFixed(2) : '';
            const total = unit * (qty || 0);
            if (lineTotalDisplay) lineTotalDisplay.textContent = procFormatCurrency(total);
            updateProcGrandTotal();
        }

        function updateProcGrandTotal() {
            let grand = 0;
            document.querySelectorAll('.procurement-item-row').forEach(row => {
                const select = row.querySelector('.proc-material-select');
                const qtyInput = row.querySelector('.proc-item-qty');
                let unit = 0;
                if (select) {
                    const opt = select.options[select.selectedIndex];
                    unit = procToNumber(opt ? opt.getAttribute('data-price') : null);
                }
                const qty = procToNumber(qtyInput?.value || '0');
                grand += unit * (qty || 0);
            });
            const el = document.getElementById('procGrandTotal');
            if (el) el.textContent = procFormatCurrency(grand);
        }

        function addProcurementMaterialRow() {
            procRowIndex++;
            const container = document.getElementById('procurementItemsContainer');
            const firstRow = container.querySelector('.procurement-item-row');
            const newRow = firstRow.cloneNode(true);
            const idx = procRowIndex;
            
            newRow.setAttribute('data-index', idx);
            
            // Update name attributes
            const select = newRow.querySelector('.proc-material-select');
            if (select) {
                select.name = `items[${idx}][material_id]`;
                select.id = '';
                select.value = '';
            }
            const qtyInput = newRow.querySelector('.proc-item-qty');
            if (qtyInput) {
                qtyInput.name = `items[${idx}][quantity]`;
                qtyInput.value = '';
            }
            const unitPriceHidden = newRow.querySelector('.proc-item-unit-price-hidden');
            if (unitPriceHidden) {
                unitPriceHidden.name = `items[${idx}][unit_price]`;
                unitPriceHidden.value = '';
            }
            const unitPriceDisplay = newRow.querySelector('.proc-item-unit-price');
            if (unitPriceDisplay) unitPriceDisplay.value = '';
            
            // Reset search input
            const searchInput = newRow.querySelector('.proc-material-search');
            if (searchInput) { searchInput.value = ''; searchInput.classList.remove('hidden'); searchInput.id = ''; }
            
            // Reset badge
            const badge = newRow.querySelector('.proc-material-badge');
            if (badge) { badge.classList.add('hidden'); badge.classList.remove('flex'); }
            
            // Reset line total
            const lineTotal = newRow.querySelector('.proc-item-line-total');
            if (lineTotal) lineTotal.textContent = 'â‚±0.00';
            
            // Reset results list
            const resultsList = newRow.querySelector('.proc-material-results');
            if (resultsList) { resultsList.classList.add('hidden'); resultsList.id = ''; }

            // Show all material options
            newRow.querySelectorAll('.proc-material-option').forEach(opt => opt.classList.remove('hidden'));
            newRow.querySelector('.proc-material-no-match')?.classList.add('hidden');
            
            // Add remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all shadow-md';
            removeBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            removeBtn.onclick = function() {
                newRow.style.transition = 'all 0.3s ease-out';
                newRow.style.opacity = '0';
                newRow.style.transform = 'scale(0.95)';
                setTimeout(() => { newRow.remove(); updateProcGrandTotal(); }, 300);
            };
            newRow.style.position = 'relative';
            newRow.appendChild(removeBtn);

            // Add animation
            newRow.style.opacity = '0';
            newRow.style.transform = 'translateY(-10px)';
            container.appendChild(newRow);
            requestAnimationFrame(() => {
                newRow.style.transition = 'all 0.3s ease-out';
                newRow.style.opacity = '1';
                newRow.style.transform = 'translateY(0)';
            });
        }

        function filterProcMaterialList(input) {
            const wrapper = input.closest('.proc-material-search-wrapper');
            const term = input.value.toLowerCase().trim();
            const options = wrapper.querySelectorAll('.proc-material-option');
            const noMatch = wrapper.querySelector('.proc-material-no-match');
            const resultsList = wrapper.querySelector('.proc-material-results');
            let count = 0;

            resultsList.classList.remove('hidden');
            options.forEach(opt => {
                const name = opt.getAttribute('data-name').toLowerCase();
                if (name.includes(term)) { opt.classList.remove('hidden'); count++; }
                else { opt.classList.add('hidden'); }
            });
            noMatch.classList.toggle('hidden', count > 0);
        }

        function selectProcMaterial(btn) {
            const row = btn.closest('.procurement-item-row');
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const price = btn.getAttribute('data-price');

            const hiddenSelect = row.querySelector('.proc-material-select');
            hiddenSelect.value = id;

            const searchInput = row.querySelector('.proc-material-search');
            searchInput.value = name;
            searchInput.classList.add('hidden');

            const resultsList = row.querySelector('.proc-material-results');
            resultsList.classList.add('hidden');

            const badge = row.querySelector('.proc-material-badge');
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            row.querySelector('.proc-material-selected-name').textContent = name;
            row.querySelector('.proc-material-selected-price').textContent = 'â‚±' + parseFloat(price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            row.querySelector('.proc-material-initial').textContent = name.charAt(0).toUpperCase();

            // Auto-fill pricing
            const unitPriceDisplay = row.querySelector('.proc-item-unit-price');
            const unitPriceHidden = row.querySelector('.proc-item-unit-price-hidden');
            if (unitPriceDisplay) unitPriceDisplay.value = procFormatCurrency(parseFloat(price));
            if (unitPriceHidden) unitPriceHidden.value = parseFloat(price).toFixed(2);

            // Default qty to 1 if empty
            const qtyInput = row.querySelector('.proc-item-qty');
            if (qtyInput && (!qtyInput.value || procToNumber(qtyInput.value) < 1)) {
                qtyInput.value = '1';
            }
            updateProcRowTotal(btn);
        }

        function clearProcMaterialSelection(btn) {
            const row = btn.closest('.procurement-item-row');
            const hiddenSelect = row.querySelector('.proc-material-select');
            hiddenSelect.value = '';

            const input = row.querySelector('.proc-material-search');
            input.value = '';
            input.classList.remove('hidden');

            const badge = row.querySelector('.proc-material-badge');
            badge.classList.add('hidden');
            badge.classList.remove('flex');

            row.querySelectorAll('.proc-material-option').forEach(opt => opt.classList.remove('hidden'));
            row.querySelector('.proc-material-no-match')?.classList.add('hidden');

            const unitPriceDisplay = row.querySelector('.proc-item-unit-price');
            const unitPriceHidden = row.querySelector('.proc-item-unit-price-hidden');
            if (unitPriceDisplay) unitPriceDisplay.value = '';
            if (unitPriceHidden) unitPriceHidden.value = '';
            const lineTotal = row.querySelector('.proc-item-line-total');
            if (lineTotal) lineTotal.textContent = 'â‚±0.00';
            
            input.focus();
            updateProcGrandTotal();
        }

        // Close material dropdown on outside click
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.proc-material-search-wrapper').forEach(wrapper => {
                const results = wrapper.querySelector('.proc-material-results');
                if (results && !wrapper.contains(e.target)) {
                    results.classList.add('hidden');
                }
            });
        });

        function openViewOrderModal(orderId) {
            document.getElementById('viewOrderItemsModal').classList.remove('hidden');

            const itemsContainer = document.getElementById('viewOrderItemsContainer');
            itemsContainer.innerHTML = '<div class="p-6 rounded-lg border text-center shadow-sm" style="background-color: rgba(255,255,255,0.7); border-color: #374151; color: #999;">Loading items...</div>';

            fetch(`/procurement/purchase-orders/${orderId}/items?include_received=1`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success || !data.items || data.items.length === 0) {
                        itemsContainer.innerHTML = '<div class="p-6 rounded-lg border text-center shadow-sm" style="background-color: rgba(255,255,255,0.7); border-color: #374151; color: #999;">No items found for this order.</div>';
                        return;
                    }

                    if (data.order) {
                        const orderNumber = data.order.order_number || '-';
                        const supplierName = data.order.supplier_name || '-';
                        document.getElementById('viewOrderHeaderNumber').textContent = `Order #${orderNumber}`;
                        document.getElementById('viewOrderHeaderSupplier').textContent = supplierName;
                        document.getElementById('viewOrderDetailNumber').textContent = orderNumber;
                        document.getElementById('viewOrderDetailSupplier').textContent = supplierName;
                    } else {
                        const row = document.querySelector(`button[onclick="event.stopPropagation(); openViewOrderModal(${orderId})"]`)?.closest('tr');
                        if (row) {
                            const orderCell = row.querySelector('td:first-child');
                            const supplierCell = row.querySelector('td:nth-child(2)');
                            const orderNumber = orderCell ? orderCell.textContent.trim() : '-';
                            const supplierName = supplierCell ? supplierCell.textContent.trim() : '-';
                            document.getElementById('viewOrderHeaderNumber').textContent = `Order #${orderNumber}`;
                            document.getElementById('viewOrderHeaderSupplier').textContent = supplierName;
                            document.getElementById('viewOrderDetailNumber').textContent = orderNumber;
                            document.getElementById('viewOrderDetailSupplier').textContent = supplierName;
                        }
                    }

                    // Calculate totals
                    let totalAmount = 0;
                    const itemsHTML = data.items.map(item => {
                        const lineTotal = (item.ordered_quantity || 0) * (item.unit_price || 0);
                        totalAmount += lineTotal;
                        const received = Number(item.already_received || 0);
                        const ordered = Number(item.ordered_quantity || 0);
                        
                        return `
                            <div class="p-5 rounded-lg border-l-4 hover:shadow-md transition shadow-sm" style="background-color: rgba(255,255,255,0.85); border-left-color: #374151;">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h5 class="font-bold text-base" style="color: #374151;">${item.material_name || 'Unknown Material'}</h5>
                                        <p class="text-xs mt-1" style="color: #666;">Unit Price: <span class="font-semibold" style="color: #374151;">â‚±${Number(item.unit_price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span></p>
                                    </div>
                                    <div class="text-right">
                                        <div class="inline-block px-3 py-1.5 rounded-full font-bold text-base" style="background-color: #374151; color: #FFF1DA;">
                                            ${Number(ordered).toFixed(2)} <span class="text-xs">units</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 grid grid-cols-3 gap-4 border-t" style="border-top-color: #E8D5BF; color: #666;">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase" style="color: #999;">Received</p>
                                        <p class="text-sm font-bold" style="color: #374151;">${Number(received).toFixed(2)}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase" style="color: #999;">Remaining</p>
                                        <p class="text-sm font-bold" style="color: #374151;">${Number(ordered - received).toFixed(2)}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold uppercase" style="color: #999;">Line Total</p>
                                        <p class="text-base font-bold" style="color: #10B981;">â‚±${Number(lineTotal).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    document.getElementById('viewOrderTotalItems').textContent = data.items.length;
                    document.getElementById('viewOrderTotalAmount').textContent = `â‚±${Number(totalAmount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    itemsContainer.innerHTML = itemsHTML;
                })
                .catch(() => {
                    itemsContainer.innerHTML = '<div class="p-6 rounded-lg border text-center shadow-sm" style="background-color: rgba(255,255,255,0.7); border-color: #374151; color: #EF4444;">Failed to load items for this order.</div>';
                });
        }

        function closeViewOrderItemsModal() {
            document.getElementById('viewOrderItemsModal').classList.add('hidden');
        }

        // Received Stock Reports Modal Functions
        function openReceivedStockReportsModal() {
            document.getElementById('receivedStockReportsModal').classList.remove('hidden');
            // Reset filters on open
            if(document.getElementById('reportSearchInput')) document.getElementById('reportSearchInput').value = '';
            if(document.getElementById('filterDateFrom')) document.getElementById('filterDateFrom').value = '';
            if(document.getElementById('filterDateTo')) document.getElementById('filterDateTo').value = '';
            filterReceivedStock();
        }

        function closeReceivedStockReportsModal() {
            document.getElementById('receivedStockReportsModal').classList.add('hidden');
        }

        // loadReceivedStockReports is replaced by filterReceivedStock to unify logic
        // Keeping this alias if it's called from elsewhere, though openModal now calls filter directly
        const loadReceivedStockReports = filterReceivedStock;

        async function filterReceivedStock() {
            const dateFrom = document.getElementById('filterDateFrom')?.value || '';
            const dateTo = document.getElementById('filterDateTo')?.value || '';
            const search = document.getElementById('reportSearchInput')?.value || '';

            const params = new URLSearchParams();
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (search) params.append('search', search);

            const tbody = document.getElementById('receivedStockTableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="py-8 px-4 text-center text-gray-400">Loading...</td></tr>';

            try {
                const response = await fetch(`/procurement/received-stock-reports?${params.toString()}`);
                const data = await response.json();

                if (data.success && data.movements && data.movements.length > 0) {
                    let totalReceived = 0;
                    let totalDefects = 0;

                    tbody.innerHTML = data.movements.map(movement => {
                        const receivedQty = parseFloat(movement.quantity || 0);
                        const defectQty = parseFloat(movement.defect_quantity || 0);
                        totalReceived += receivedQty;
                        totalDefects += defectQty;

                        return `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700">${movement.date}</td>
                                <td class="px-4 py-3 text-gray-700">${movement.po_number || 'N/A'}</td>
                                <td class="px-4 py-3 text-gray-700">${movement.material_name}</td>
                                <td class="px-4 py-3 text-gray-700">${receivedQty.toFixed(2)}</td>
                                <td class="px-4 py-3 text-red-600">${defectQty.toFixed(2)}</td>
                                <td class="px-4 py-3 text-gray-700">${movement.supplier_name || 'N/A'}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold ${
                                        movement.status === 'completed' ? 'text-green-600' : 
                                        movement.status === 'pending' ? 'text-orange-600' : 'text-red-600'
                                    }">${movement.status || 'N/A'}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">${movement.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');

                    // Update summary - with null checks
                    const totalReceivedEl = document.getElementById('totalReceived');
                    const totalDefectsEl = document.getElementById('totalDefects');
                    
                    if (totalReceivedEl) totalReceivedEl.textContent = totalReceived.toFixed(2);
                    if (totalDefectsEl) totalDefectsEl.textContent = totalDefects.toFixed(2);
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="py-8 px-4 text-center text-gray-400">No records found matching the filters</td></tr>';
                    const totalReceivedEl = document.getElementById('totalReceived');
                    const totalDefectsEl = document.getElementById('totalDefects');
                    if (totalReceivedEl) totalReceivedEl.textContent = '0';
                    if (totalDefectsEl) totalDefectsEl.textContent = '0';
                }
            } catch (error) {
                console.error('Error filtering received stock:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="py-8 px-4 text-center text-red-400">Error loading data</td></tr>';
            }
        }

    // Add row selection functionality
    const procurementTbody = document.getElementById('procurementTbody');
    if (procurementTbody) {
        procurementTbody.addEventListener('click', function(e) {
            const row = e.target.closest('tr.data-row');

            // Ignore clicks on action buttons
            if (e.target.closest('button') || e.target.closest('form')) {
                return;
            }

            if (row) {
                // Remove selection from all rows
                const allRows = procurementTbody.querySelectorAll('tr.data-row');
                allRows.forEach(r => {
                    r.classList.remove('!bg-slate-500', 'border-l-4', 'border-amber-500', 'bg-slate-600');
                    r.classList.add('hover:bg-slate-600'); 
                });

                // Add selection to clicked row using Tailwind classes
                // We use !bg-slate-500 to override hover effects more strongly if needed, 
                // but standard classes usually work if specificity is managed. 
                // Based on sales.blade.php, they used !bg-gray-600. Let's use a distinct color for selection.
                row.classList.remove('hover:bg-slate-600');
                row.classList.add('!bg-slate-500', 'border-l-4', 'border-amber-500');
            }
        });
    }

    // Export Functions
    function exportReceipt(event) {
        event.preventDefault();
        document.getElementById('exportDropdown').classList.add('hidden');

        // Check if a row is selected
        const selectedRow = document.querySelector('tr.data-row.border-amber-500');

        if (selectedRow) {
            // Get order number from the selected row (first column)
            // The order number is in the first td
            const orderNumber = selectedRow.querySelector('td:first-child').innerText.trim();
            window.open(`/procurement/receipt/${orderNumber}`, '_blank');
            return;
        }

        // If no row is selected, prompt user
        alert('Please select an order by clicking on a row in the table to export as receipt.');
    }

        // Apply Quick Filter
        function applyReportQuickFilter(type) {
            const dateFromInput = document.getElementById('filterDateFrom');
            const dateToInput = document.getElementById('filterDateTo');
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

            if (dateFromInput) dateFromInput.value = start;
            if (dateToInput) dateToInput.value = end;
            filterReceivedStock();
        }

        // Reset Filters
        function resetFilters() {
            if(document.getElementById('reportSearchInput')) document.getElementById('reportSearchInput').value = '';
            if(document.getElementById('filterDateFrom')) document.getElementById('filterDateFrom').value = '';
            if(document.getElementById('filterDateTo')) document.getElementById('filterDateTo').value = '';
            loadReceivedStockReports();
        }

        // Add event listener for search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('reportSearchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        filterReceivedStock();
                    }
                });
            }
        });

        // Export to Excel (placeholder - requires backend implementation)
        function exportToExcel() {
            alert('Export to Excel functionality will be implemented with backend support.');
            // Future implementation: window.location.href = '/procurement/received-stock-reports/export';
        }

        // Print Report
        function printReport() {
            window.print();
        }

        // Pagination placeholders (for future implementation with backend)
        function previousPage() {
            console.log('Previous page clicked');
            // Future implementation with pagination parameters
        }

        function nextPage() {
            console.log('Next page clicked');
            // Future implementation with pagination parameters
        }

        // Function to populate unit price from material data
        function populateUnitPrice(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const unitPrice = selectedOption.getAttribute('data-price');
            const unitPriceInput = selectElement.closest('.grid').querySelector('input[name*="[unit_price]"]');
            
            if (unitPrice && unitPriceInput) {
                unitPriceInput.value = unitPrice;
            }
        }

        // Purchase Order Item Management
        let itemCount = 1;

        function addItem() {
            const container = document.getElementById('orderItems');
            const materialsData = window.__procurementData?.materials || [];
            const materialOptions = materialsData.map(m =>
                `<option value="${m.id}" data-price="${m.unit_cost}">${m.name}</option>`
            ).join('');
            const newItem = document.createElement('div');
            newItem.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 mb-4';
            newItem.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Material *</label>
                    <select name="items[${itemCount}][material_id]" class="material-select w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">Select Material</option>
                        ${materialOptions}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="items[${itemCount}][quantity]" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price *</label>
                    <input type="number" name="items[${itemCount}][unit_price]" step="0.01" min="0" placeholder="0.00" class="unit-price-input w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                
                <div class="flex items-end">
                    <button type="button" onclick="removeItem(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Remove
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            
            // Attach event listener to the newly added material select
            const materialSelect = newItem.querySelector('.material-select');
            materialSelect.addEventListener('change', function() {
                populateUnitPrice(this);
            });
            
            itemCount++;
        }

        function removeItem(button) {
            button.closest('.grid').remove();
        }

        // Action functions
        function viewOrder(id) {
            // Implement view order functionality
            console.log('View order:', id);
        }

        // Purchase Orders and Suppliers data for edit modals
        const purchaseOrdersData = window.__procurementData?.purchaseOrders || [];
        const suppliersData = window.__procurementData?.suppliers || [];

        function editOrder(id) {
            try {
                console.log('editOrder called with id=', id);
                const order = purchaseOrdersData.data ? purchaseOrdersData.data.find(o => o.id === id) : purchaseOrdersData.find(o => o.id === id);
                if (!order) {
                    console.error('Order not found:', id, purchaseOrdersData);
                    return;
                }

                const orderNumberEl = document.getElementById('editPOOrderNumber');
                const supplierNameEl = document.getElementById('editPOSupplierName');
                const paymentStatusEl = document.getElementById('editPOPaymentStatus');
                const statusEl = document.getElementById('editPOStatus');
                const expectedEl = document.getElementById('editPOExpectedDelivery');
                const totalEl = document.getElementById('editPOTotalAmount');

                if (orderNumberEl) orderNumberEl.value = order.order_number || 'PO-' + String(order.id).padStart(6, '0');
                if (supplierNameEl) supplierNameEl.value = order.supplier ? order.supplier.name : 'N/A';
                if (paymentStatusEl) paymentStatusEl.value = order.payment_status || 'Pending';
                if (statusEl && order.status) statusEl.value = order.status;
                if (expectedEl && order.expected_delivery) {
                    try {
                        const ed = new Date(order.expected_delivery);
                        if (!isNaN(ed)) expectedEl.value = ed.toISOString().split('T')[0];
                        else expectedEl.value = order.expected_delivery;
                    } catch (e) {
                        expectedEl.value = order.expected_delivery || '';
                    }
                }
                if (totalEl) totalEl.value = (order.total_amount !== undefined && order.total_amount !== null) ? parseFloat(order.total_amount).toFixed(2) : '';

                // Set form action
                const updateUrlTemplate = "{{ route('procurement.purchase-order.update', ['id' => '__ID__']) }}";
                const editForm = document.getElementById('editPurchaseOrderForm');
                if (editForm) editForm.action = updateUrlTemplate.replace('__ID__', id);

                const modal = document.getElementById('editPurchaseOrderModal');
                if (!modal) {
                    console.error('editPurchaseOrderModal element not found');
                    return;
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (err) {
                console.error('Error in editOrder:', err);
                alert('Unable to open edit modal. Check console for details.');
            }
        }

        function closeEditPurchaseOrderModal() {
            const modal = document.getElementById('editPurchaseOrderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
            document.getElementById('editPurchaseOrderForm').reset();
        }

        let currentDeleteOrderId = null;

        function deleteOrder(id) {
            currentDeleteOrderId = id;
            const modal = document.getElementById('deleteOrderModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteOrder() {
            const modal = document.getElementById('deleteOrderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentDeleteOrderId = null;
        }

        function submitDeleteOrder() {
            if (!currentDeleteOrderId) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/procurement/purchase-orders/${currentDeleteOrderId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
            
            closeDeleteOrder();
        }

        function editSupplier(id) {
            const supplier = suppliersData.find(s => s.id === id);
            if (!supplier) {
                console.error('Supplier not found:', id);
                return;
            }
            
            document.getElementById('editSupplierName').value = supplier.name || '';
            document.getElementById('editSupplierContactPerson').value = supplier.contact_person || '';
            document.getElementById('editSupplierPhone').value = supplier.phone || '';
            document.getElementById('editSupplierEmail').value = supplier.email || '';
            document.getElementById('editSupplierAddress').value = supplier.address || '';
            document.getElementById('editSupplierPaymentTerms').value = supplier.payment_terms || '';
            document.getElementById('editSupplierStatus').value = supplier.status || 'active';
            document.getElementById('editSupplierForm').action = `/procurement/suppliers/${id}`;
            
            document.getElementById('editSupplierModal').classList.remove('hidden');
        }

        function closeEditSupplierModal() {
            document.getElementById('editSupplierModal').classList.add('hidden');
            document.getElementById('editSupplierForm').reset();
        }

        let currentDeleteSupplierId = null;

        function deleteSupplier(id) {
            currentDeleteSupplierId = id;
            const modal = document.getElementById('deleteSupplierModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteSupplier() {
            const modal = document.getElementById('deleteSupplierModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentDeleteSupplierId = null;
        }

        function submitDeleteSupplier() {
            if (!currentDeleteSupplierId) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/procurement/suppliers/${currentDeleteSupplierId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
            
            closeDeleteSupplier();
        }

        // Initialize tab state on page load
        document.addEventListener('DOMContentLoaded', function() {
            showTab('purchase-orders');
            const purchaseOrdersTab = document.getElementById('purchase-orders-tab');
            purchaseOrdersTab.style.borderRadius = '10px';
            const suppliersTab = document.getElementById('suppliers-tab');
            suppliersTab.style.borderRadius = '10px';

            // Attach event listeners to all material select dropdowns
            const materialSelects = document.querySelectorAll('.material-select');
            materialSelects.forEach(select => {
                select.addEventListener('change', function() {
                    populateUnitPrice(this);
                });
            });

            // Procurement search & filter logic
            function applyProcurementFilters() {
                const searchInput = document.getElementById('searchInput');
                const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
                const statusFilter = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : 'all';
                // Search and status filters handled client-side

                const purchaseOrdersTable = document.getElementById('purchase-orders-table');
                const suppliersTable = document.getElementById('suppliers-table');
                const activeContainer = purchaseOrdersTable && !purchaseOrdersTable.classList.contains('hidden') ? purchaseOrdersTable : suppliersTable;

                if (!activeContainer) return;

                const rows = Array.from(activeContainer.querySelectorAll('tbody tr'));
                let visibleCount = 0;

                rows.forEach(row => {
                    // Collect searchable text depending on active table
                    let text = (row.textContent || '').toLowerCase();

                    // For purchase orders, filter additionally by payment status if selected
                    if (activeContainer.id === 'purchase-orders-table' && statusFilter && statusFilter !== 'all') {
                        const statusCell = row.querySelector('td:nth-child(6)');
                        const statusText = statusCell ? (statusCell.textContent || '').toLowerCase() : '';
                        if (!statusText.includes(statusFilter)) {
                            row.style.display = 'none';
                            return;
                        }
                    }

                    // If query exists, match against row text
                    if (query.length > 0) {
                        if (text.includes(query)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // no query, row already passed status filter
                        row.style.display = '';
                        visibleCount++;
                    }
                });

                // Manage contextual no-results message inside active container
                let noResultsEl = activeContainer.querySelector('#procurementNoResultsMessage');
                if (!noResultsEl) {
                    noResultsEl = document.createElement('div');
                    noResultsEl.id = 'procurementNoResultsMessage';
                    noResultsEl.className = 'py-8 px-4 text-center text-slate-400';
                    noResultsEl.style.display = 'none';
                    activeContainer.appendChild(noResultsEl);
                }

                if (visibleCount === 0) {
                    noResultsEl.textContent = 'No items match your search/filter.';
                    noResultsEl.style.display = '';
                } else {
                    noResultsEl.style.display = 'none';
                }
            }

            // Attach listeners (guarded)
            const searchEl = document.getElementById('searchInput');
            if (searchEl) {
                searchEl.addEventListener('input', function() {
                    applyProcurementFilters();
                });
            }

            const statusEl = document.getElementById('statusFilter');
            if (statusEl) {
                statusEl.addEventListener('change', function() {
                    applyProcurementFilters();
                });
            }

            // payment filter removed; no-op
        });





        // Purchase Order Confirmation Modal Functions
        function confirmPurchaseOrder(event) {
            event.preventDefault();
            const modal = document.getElementById('confirmPurchaseOrderModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return false;
        }

        function closeConfirmPurchaseOrder() {
            const modal = document.getElementById('confirmPurchaseOrderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitPurchaseOrder() {
            closeConfirmPurchaseOrder();
            const form = document.getElementById('addPurchaseOrderForm');
            form.onsubmit = null;
            form.submit();
        }

        // Supplier Confirmation Modal Functions
        function confirmSupplier(event) {
            event.preventDefault();
            const modal = document.getElementById('confirmSupplierModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return false;
        }

        function closeConfirmSupplier() {
            const modal = document.getElementById('confirmSupplierModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitSupplier() {
            closeConfirmSupplier();
            const form = document.getElementById('addSupplierForm');
            form.onsubmit = null;
            form.submit();
        }

// --- Second Script Block ---

    // Archive Functions
    function openArchiveModal() {
        const modal = document.getElementById('archiveModal');
        modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
    }

    let currentArchiveStatus = 'all';

    function filterArchive(status) {
        currentArchiveStatus = status;
        
        // Update button styles
        document.querySelectorAll('.archive-filter-btn').forEach(btn => {
            btn.classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
            btn.classList.add('text-gray-500', 'hover:bg-gray-200');
        });
        
        const btnId = 'archiveFilter' + (status === 'all' ? 'All' : (status.charAt(0).toUpperCase() + status.slice(1)));
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
            const date = tr.getAttribute('data-date');
            
            const matchesSearch = !q || text.includes(q);
            const matchesStatus = currentArchiveStatus === 'all' || status === currentArchiveStatus.toLowerCase();
            
            let matchesDate = true;
            if (start || end) {
                if (date) {
                    if (start && date < start) matchesDate = false;
                    if (end && date > end) matchesDate = false;
                } else {
                    matchesDate = false; // or true if you want to include undated ones
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
        document.getElementById('archiveSearchInput').value = '';
        document.getElementById('archiveStartDate').value = '';
        document.getElementById('archiveEndDate').value = '';
        filterArchive('all');
    }

    // Receive Stock Functions
    function openReceiveOrderModal(id, number) {
        document.getElementById('receiveOrderNumber').textContent = number;
        document.getElementById('receiveOrderForm').action = `/procurement/purchase-orders/${id}/receive-stock`; // Ensure this route exists or update logic
        document.getElementById('receiveOrderForm').action = `/procurement/purchase-orders/${id}/receive`;
        
        const modal = document.getElementById('receiveOrderModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReceiveOrderModal() {
        const modal = document.getElementById('receiveOrderModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Listeners for Archive
    document.addEventListener('DOMContentLoaded', function() {
        const archiveSearch = document.getElementById('archiveSearchInput');
        if (archiveSearch) archiveSearch.addEventListener('input', applyArchiveFilters);
        const archiveStart = document.getElementById('archiveStartDate');
        if (archiveStart) archiveStart.addEventListener('change', applyArchiveFilters);
        const archiveEnd = document.getElementById('archiveEndDate');
        if (archiveEnd) archiveEnd.addEventListener('change', applyArchiveFilters);

        // Close supplier dropdown on outside click
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('supplierSearchWrapper');
            const results = document.getElementById('supplierResultsList');
            if (wrapper && results && !wrapper.contains(e.target)) {
                results.classList.add('hidden');
            }
        });
    });

    // Supplier searchable typeahead
    function filterSupplierList() {
        const input = document.getElementById('supplierSearchInput');
        const term = input.value.toLowerCase().trim();
        const options = document.querySelectorAll('.supplier-option');
        const noMatch = document.getElementById('supplierNoMatch');
        const resultsList = document.getElementById('supplierResultsList');
        let count = 0;

        resultsList.classList.remove('hidden');
        options.forEach(opt => {
            const name = opt.getAttribute('data-name').toLowerCase();
            const contact = opt.getAttribute('data-contact').toLowerCase();
            if (name.includes(term) || contact.includes(term)) {
                opt.classList.remove('hidden');
                count++;
            } else {
                opt.classList.add('hidden');
            }
        });
        noMatch.classList.toggle('hidden', count > 0);
    }

    function selectSupplier(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const contact = btn.getAttribute('data-contact');

        document.getElementById('procSupplierIdHidden').value = id;
        document.getElementById('supplierSearchInput').value = name;
        document.getElementById('supplierSearchInput').classList.add('hidden');
        document.getElementById('supplierResultsList').classList.add('hidden');

        // Show selected badge
        const badge = document.getElementById('supplierSelectedBadge');
        badge.classList.remove('hidden');
        badge.classList.add('flex');
        document.getElementById('supplierSelectedName').textContent = name;
        document.getElementById('supplierSelectedInfo').textContent = contact;
        document.getElementById('supplierInitial').textContent = name.charAt(0).toUpperCase();
    }

    function clearSupplierSelection() {
        document.getElementById('procSupplierIdHidden').value = '';
        const input = document.getElementById('supplierSearchInput');
        input.value = '';
        input.classList.remove('hidden');

        const badge = document.getElementById('supplierSelectedBadge');
        badge.classList.add('hidden');
        badge.classList.remove('flex');

        // Reset all options
        document.querySelectorAll('.supplier-option').forEach(opt => opt.classList.remove('hidden'));
        document.getElementById('supplierNoMatch').classList.add('hidden');
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
