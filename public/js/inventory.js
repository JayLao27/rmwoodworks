            // Initialize search and filter listeners on page load
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const categoryFilter = document.getElementById('categoryFilter');
                
                if (searchInput) {
                    searchInput.addEventListener('keyup', applyInventoryFilters);
                    searchInput.addEventListener('change', applyInventoryFilters);
                }
                
                if (categoryFilter) {
                    categoryFilter.addEventListener('change', applyInventoryFilters);
                }
            });

            // Tab functionality
            function showTab(tab) {
                const materialsTab = document.getElementById('materials-tab');
                const productsTab = document.getElementById('products-tab');
                const materialsTable = document.getElementById('materials-table');
                const productsTable = document.getElementById('products-table');
                const materialsButton = document.getElementById('materialsButton');
                const productsButton = document.getElementById('productsButton');
                
                if (tab === 'materials') {
                    materialsTab.style.backgroundColor = '#FFF1DA';
                    materialsTab.style.color = '#111827';
                    materialsTab.style.borderColor = 'transparent';
                    productsTab.style.backgroundColor = '#475569';
                    productsTab.style.color = '#FFFFFF';
                    productsTab.style.borderColor = '#64748b';
                    materialsTable.classList.remove('hidden');
                    productsTable.classList.add('hidden');
                    materialsButton.classList.remove('hidden');
                    productsButton.classList.add('hidden');
                } else {
                    productsTab.style.backgroundColor = '#FFF1DA';
                    productsTab.style.color = '#111827';
                    productsTab.style.borderColor = 'transparent';
                    materialsTab.style.backgroundColor = '#475569';
                    materialsTab.style.color = '#FFFFFF';
                    materialsTab.style.borderColor = '#64748b';
                    productsTable.classList.remove('hidden');
                    materialsTable.classList.add('hidden');
                    materialsButton.classList.add('hidden');
                    productsButton.classList.remove('hidden');
                }

                // Save active tab to localStorage
                localStorage.setItem('activeInventoryTab', tab);
                applyInventoryFilters();
            }

            // Restore active tab on page load
            window.addEventListener('DOMContentLoaded', function() {
                const activeTab = localStorage.getItem('activeInventoryTab') || 'materials';
                showTab(activeTab);
            });

            function applyInventoryFilters() {
                const searchValue = (document.getElementById('searchInput')?.value || '').toLowerCase();
                const categoryValue = document.getElementById('categoryFilter')?.value || '';

                const materialsTable = document.getElementById('materials-table');
                const productsTable = document.getElementById('products-table');

                const cards = [];
                if (materialsTable && !materialsTable.classList.contains('hidden')) {
                    cards.push(...materialsTable.querySelectorAll('[data-name]'));
                }
                if (productsTable && !productsTable.classList.contains('hidden')) {
                    cards.push(...productsTable.querySelectorAll('[data-name]'));
                }

                cards.forEach(card => {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    const category = card.getAttribute('data-category') || '';

                    const matchesSearch = !searchValue || name.includes(searchValue);
                    const matchesCategory = !categoryValue || category === categoryValue;

                    card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
                });
            }

            // Modal functions
            function openAddItemModal() {
                document.getElementById('addItemModal').classList.remove('hidden');
            }

            function closeAddItemModal() {
                document.getElementById('addItemModal').classList.add('hidden');
                document.getElementById('addItemForm').reset();
                // Clear selection states
                document.querySelectorAll('#materialCategoryButtons .category-btn').forEach(btn => {
                    btn.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    btn.classList.add('border-gray-300', 'text-gray-600');
                });
                document.getElementById('selectedMaterialCategory').value = '';
                document.querySelectorAll('#supplierButtonsContainer .supplier-btn').forEach(btn => {
                    btn.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    btn.classList.add('border-gray-200');
                    btn.querySelector('div div').classList.remove('bg-amber-500');
                    btn.querySelector('div div').classList.add('bg-slate-300');
                });
                document.getElementById('selectedMaterialSupplierId').value = '';
                if(document.getElementById('supplierSearchInput')) document.getElementById('supplierSearchInput').value = '';
                document.querySelectorAll('.supplier-btn').forEach(btn => btn.classList.remove('hidden'));
            }

            function selectMaterialCategory(btn, category) {
                document.querySelectorAll('#materialCategoryButtons .category-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-300', 'text-gray-600');
                });
                btn.classList.remove('border-gray-300', 'text-gray-600');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                document.getElementById('selectedMaterialCategory').value = category;
            }

            function selectSupplier(btn, supplierId) {
                document.querySelectorAll('#supplierButtonsContainer .supplier-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-200');
                    b.querySelector('div div').classList.remove('bg-amber-500');
                    b.querySelector('div div').classList.add('bg-slate-300');
                });
                btn.classList.remove('border-gray-200');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                btn.querySelector('div div').classList.remove('bg-slate-300');
                btn.querySelector('div div').classList.add('bg-amber-500');
                document.getElementById('selectedMaterialSupplierId').value = supplierId;
            }

            function selectProductCategory(btn, category) {
                document.querySelectorAll('#productCategoryButtons .prod-category-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-300', 'text-gray-600');
                });
                btn.classList.remove('border-gray-300', 'text-gray-600');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                document.getElementById('selectedProductCategory').value = category;
            }

            // Supplier Search
            document.addEventListener('DOMContentLoaded', function() {
                const supplierSearch = document.getElementById('supplierSearchInput');
                if (supplierSearch) {
                    supplierSearch.addEventListener('input', function() {
                        const term = this.value.toLowerCase();
                        document.querySelectorAll('.supplier-btn').forEach(btn => {
                            const name = btn.dataset.supplierName;
                            btn.classList.toggle('hidden', !name.includes(term));
                        });
                    });
                }
            });

            function openAddProductModal() {
                document.getElementById('addProductModal').classList.remove('hidden');
                document.getElementById('materialsContainer').innerHTML = '';
            }

            function closeAddProductModal() {
                document.getElementById('addProductModal').classList.add('hidden');
                document.getElementById('addProductForm').reset();
                document.getElementById('materialsContainer').innerHTML = '';
            }

            // Material row counter
            let materialRowCount = 0;

            function addMaterialRow() {
                const materialsContainer = document.getElementById('materialsContainer');
                const rowId = 'material-row-' + materialRowCount++;
                
                const row = document.createElement('div');
                row.id = rowId;
                row.className = 'flex gap-2 items-end bg-white/70 p-4 rounded-xl border-2 border-gray-300';
                
                row.innerHTML = `
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Material *</label>
                        <select name="materials[${materialRowCount-1}][material_id]" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                            <option value="">Select a material...</option>
                            ${(window.inventoryMaterials || []).map(m => `<option value="${m.id}">${escHtml(m.name)} (${escHtml(m.unit)})</option>`).join('')}
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Quantity Needed *</label>
                        <input type="number" name="materials[${materialRowCount-1}][quantity_needed]" step="0.01" min="0.01" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                    </div>
                    <button type="button" onclick="removeMaterialRow('${rowId}')" class="px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold shadow-lg transition-all">
                        Remove
                    </button>
                `;
                
                materialsContainer.appendChild(row);
            }

            function removeMaterialRow(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    openGenericConfirm('Remove Material', 'Remove this material from the product?', function() {
                        row.remove();
                    });
                }
            }

            // Edit material row counter
            let editMaterialRowCount = 0;

            async function openEditProductModal(productId) {
                try {
                    const response = await fetch(`/inventory/${productId}/edit-product`);
                    if (!response.ok) {
                        let errMsg;
                        try { const d = await response.json(); errMsg = d.message; } catch (_) {}
                        if (response.status === 404) throw new Error(errMsg || 'Product not found (404).');
                        if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                        throw new Error(errMsg || `Failed to load product (${response.status}).`);
                    }
                    const data = await response.json();
                    document.getElementById('editProductName').value = data.product_name;
                    document.getElementById('editProductUnit').value = data.unit;
                    document.getElementById('editProductCategory').value = data.category;
                    document.getElementById('editProductProductionCost').value = data.production_cost;
                    document.getElementById('editProductSellingPrice').value = data.selling_price;

                    document.getElementById('editProductForm').action = `/inventory/${productId}`;

                    document.getElementById('editMaterialsContainer').innerHTML = '';
                    editMaterialRowCount = 0;

                    if (data.materials && data.materials.length > 0) {
                        data.materials.forEach(material => {
                            addEditMaterialRow(material.id, material.name, material.unit, material.pivot.quantity_needed);
                        });
                    }

                    document.getElementById('editProductModal').classList.remove('hidden');
                } catch (error) {
                    console.error('Error loading product:', error);
                    showErrorNotification(error.message || 'Error loading product details.');
                }
            }

            function closeEditProductModal() {
                document.getElementById('editProductModal').classList.add('hidden');
                document.getElementById('editProductForm').reset();
                document.getElementById('editMaterialsContainer').innerHTML = '';
            }

            function addEditMaterialRow(materialId = null, materialName = null, materialUnit = null, quantityNeeded = null) {
                const materialsContainer = document.getElementById('editMaterialsContainer');
                const rowId = 'edit-material-row-' + editMaterialRowCount++;
                
                const row = document.createElement('div');
                row.id = rowId;
                row.className = 'flex gap-2 items-end bg-white/70 p-4 rounded-xl border-2 border-gray-300';
                
                row.innerHTML = `
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Material *</label>
                        <select name="materials[${editMaterialRowCount-1}][material_id]" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                            <option value="">Select a material...</option>
                            ${(window.inventoryMaterials || []).map(m => `<option value="${m.id}" ${materialId == m.id ? 'selected' : ''}>${escHtml(m.name)} (${escHtml(m.unit)})</option>`).join('')}
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Quantity Needed *</label>
                        <input type="number" name="materials[${editMaterialRowCount-1}][quantity_needed]" step="0.01" min="0.01" value="${quantityNeeded || ''}" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                    </div>
                    <button type="button" onclick="removeEditMaterialRow('${rowId}')" class="px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold shadow-lg transition-all">
                        Remove
                    </button>
                `;
                
                materialsContainer.appendChild(row);
            }

            function removeEditMaterialRow(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    openGenericConfirm('Remove Material', 'Remove this material from the product?', function() {
                        row.remove();
                    });
                }
            }

            async function openStockModal(type, id) {
                document.getElementById('stockModal').style.display = 'flex';
                try {
                    const response = await fetch(`/inventory/${id}/details?type=${type}`);
                    if (!response.ok) {
                        let errMsg;
                        try { const d = await response.json(); errMsg = d.message; } catch (_) {}
                        if (response.status === 404) throw new Error(errMsg || 'Item not found (404).');
                        if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                        throw new Error(errMsg || `Failed to load item details (${response.status}).`);
                    }
                    const data = await response.json();
                    document.getElementById('itemName').textContent = data.item.name || data.item.product_name || 'Item';
                        const currentStockCard = document.getElementById('currentStockCard');
                        const minimumStockCard = document.getElementById('minimumStockCard');
                        const unitCostCard = document.getElementById('unitCostCard');
                        const productionCostCard = document.getElementById('productionCostCard');
                        const stockModalContent = document.getElementById('stockModalContent');
                        const itemInfoGrid = document.getElementById('itemInfoGrid');

                        if (type === 'product') {
                            stockModalContent.classList.remove('max-w-4xl');
                            stockModalContent.classList.add('max-w-2xl');
                            itemInfoGrid.classList.remove('grid-cols-3');
                            itemInfoGrid.classList.add('grid-cols-2');

                            currentStockCard.classList.add('hidden');
                            minimumStockCard.classList.add('hidden');
                            productionCostCard.classList.remove('hidden');

                            document.getElementById('unitCost').textContent = data.item.selling_price
                                ? '₱' + parseFloat(data.item.selling_price).toFixed(2)
                                : 'N/A';
                            document.getElementById('productionCost').textContent = data.item.production_cost
                                ? '₱' + parseFloat(data.item.production_cost).toFixed(2)
                                : 'N/A';
                        } else {
                            stockModalContent.classList.remove('max-w-2xl');
                            stockModalContent.classList.add('max-w-4xl');
                            itemInfoGrid.classList.remove('grid-cols-2');
                            itemInfoGrid.classList.add('grid-cols-3');

                            currentStockCard.classList.remove('hidden');
                            minimumStockCard.classList.remove('hidden');
                            productionCostCard.classList.add('hidden');

                            document.getElementById('currentStock').textContent = data.item.current_stock + ' ' + (data.item.unit || '');
                            document.getElementById('minimumStock').textContent = data.item.minimum_stock || '-';
                            document.getElementById('unitCost').textContent = data.item.unit_cost
                                ? '₱' + parseFloat(data.item.unit_cost).toFixed(2)
                                : 'N/A';
                        }

                        const materialsSection = document.getElementById('productMaterialsSection');
                        const materialsList = document.getElementById('productMaterialsList');
                        const materialMovementsSection = document.getElementById('materialMovementsSection');
                        const materialMovementsBody = document.getElementById('materialMovementsBody');

                        if (type === 'product') {
                            materialsSection.classList.remove('hidden');
                            materialMovementsSection.classList.add('hidden');
                            materialMovementsBody.innerHTML = '';

                            if (data.materials && data.materials.length > 0) {
                                materialsList.innerHTML = data.materials.map(material => `
                                    <div class="flex items-center justify-between px-6 py-4">
                                        <div class="text-gray-900 font-medium">• ${material.name}</div>
                                        <div class="text-gray-700 font-bold">${material.quantity_needed} ${material.unit}</div>
                                    </div>
                                `).join('');
                            } else {
                                materialsList.innerHTML = '<div class="px-6 py-8 text-center font-medium" style="color: #666;">No materials assigned</div>';
                            }
                        } else {
                            materialsSection.classList.add('hidden');
                            materialsList.innerHTML = '';

                            materialMovementsSection.classList.remove('hidden');
                            if (data.movements && data.movements.length > 0) {
                                materialMovementsBody.innerHTML = data.movements.map(movement => `
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-6 py-1.5 text-gray-900 font-medium">${new Date(movement.created_at).toLocaleDateString()}</td>
                                        <td class="px-6 py-1.5">
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-xl ${
                                                movement.movement_type === 'in' ? 'bg-green-100 text-green-800' :
                                                movement.movement_type === 'out' ? 'bg-red-100 text-red-800' :
                                                'bg-blue-100 text-blue-800'
                                            }">
                                                ${movement.movement_type.charAt(0).toUpperCase() + movement.movement_type.slice(1)}
                                            </span>
                                        </td>
                                        <td class="px-6 py-1.5 text-right font-bold text-gray-900">${movement.quantity}</td>
                                        <td class="px-6 py-1.5 text-gray-600">${movement.notes || '-'}</td>
                                    </tr>
                                `).join('');
                            } else {
                                materialMovementsBody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No movements recorded yet</td></tr>';
                            }
                        }
                } catch (error) {
                    console.error('Error fetching item details:', error);
                    showErrorNotification(error.message || 'Failed to load item details.');
                    const materialsSection = document.getElementById('productMaterialsSection');
                    const materialsList = document.getElementById('productMaterialsList');
                    if (materialsSection) materialsSection.classList.add('hidden');
                    if (materialsList) materialsList.innerHTML = '';
                    const materialMovementsSection = document.getElementById('materialMovementsSection');
                    const materialMovementsBody = document.getElementById('materialMovementsBody');
                    if (materialMovementsSection) materialMovementsSection.classList.add('hidden');
                    if (materialMovementsBody) materialMovementsBody.innerHTML = '';
                }
            }

            function closeStockModal() {
                document.getElementById('stockModal').style.display = 'none';
            }

            let deleteConfirmState = {
                type: null,
                id: null,
                name: null
            };

            function openDeleteModal(type, id, name) {
                deleteConfirmState = { type, id, name };
                document.getElementById('deleteConfirmationModal').style.display = 'flex';
                document.getElementById('deleteConfirmationInput').value = '';
                document.getElementById('deleteConfirmButton').disabled = true;
                
                // Update modal header with specific item name
                const itemLabel = type === 'material' ? 'Material' : 'Product';
                document.getElementById('deleteItemTitle').textContent = `Delete ${itemLabel}: "${name}"?`;
                document.getElementById('deleteItemName').textContent = name;
            }

            function closeDeleteModal() {
                document.getElementById('deleteConfirmationModal').style.display = 'none';
                deleteConfirmState = { type: null, id: null, name: null };
                document.getElementById('deleteConfirmationInput').value = '';
                document.getElementById('deleteConfirmButton').disabled = true;
            }

            async function confirmDelete() {
                const deleteBtn = document.getElementById('deleteConfirmButton');
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML = '<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>Deleting...';
                }

                try {
                    const response = await fetch(`/inventory/${deleteConfirmState.id}/${deleteConfirmState.type}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });
                    if (!response.ok) {
                        let errMsg;
                        try { const d = await response.json(); errMsg = d.message; } catch (_) {}
                        if (response.status === 409) throw new Error(errMsg || 'Cannot delete product associated with active orders.');
                        if (response.status === 404) throw new Error(errMsg || 'Item not found (404). It may have already been deleted.');
                        if (response.status === 403) throw new Error(errMsg || 'Permission denied (403). You are not allowed to delete this item.');
                        if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                        throw new Error(errMsg || `Delete failed (${response.status}).`);
                    }
                    const data = await response.json();
                    if (data.success) {
                        const card = document.querySelector(`[data-id="${deleteConfirmState.id}"][data-type="${deleteConfirmState.type}"]`);
                        if (card) {
                            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease, max-height 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => card.remove(), 310);
                        }
                        closeDeleteModal();
                        showSuccessNotification(data.message || 'Item deleted successfully!');
                        if (data.metrics) updateMetrics(data.metrics);
                    } else {
                        showErrorNotification(data.message || 'Failed to delete item.');
                        if (deleteBtn) { deleteBtn.disabled = false; deleteBtn.textContent = 'Delete Permanently'; }
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    showErrorNotification(error.message || 'An error occurred while deleting the item.');
                    if (deleteBtn) { deleteBtn.disabled = false; deleteBtn.textContent = 'Delete Permanently'; }
                }
            }

            // Update delete button state based on input
            document.addEventListener('DOMContentLoaded', function() {
                const deleteInput = document.getElementById('deleteConfirmationInput');
                const deleteButton = document.getElementById('deleteConfirmButton');
                if (deleteInput) {
                    deleteInput.addEventListener('input', function() {
                        deleteButton.disabled = this.value.trim() !== 'DELETE';
                    });
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    closeAddItemModal();
                    closeAddProductModal();
                    closeStockModal();
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                showTab('materials');
                const searchInput = document.getElementById('searchInput');
                const categoryFilter = document.getElementById('categoryFilter');
                if (searchInput) {
                    searchInput.addEventListener('input', applyInventoryFilters);
                }
                if (categoryFilter) {
                    categoryFilter.addEventListener('change', applyInventoryFilters);
                }
            });

            // Generic confirmation modal handlers (replace confirm() with modal)
            // Modal HTML (will be inserted near script end if not present)
            function openGenericConfirm(title, message, onConfirm, variant = 'danger') {
                // ensure modal exists
                const modal = document.getElementById('genericConfirmModal');
                if (!modal) return onConfirm();

                document.getElementById('genericConfirmTitle').textContent = title || 'Confirm';
                document.getElementById('genericConfirmMessage').textContent = message || '';
                const confirmBtn = document.getElementById('genericConfirmButton');
                const header = document.getElementById('genericConfirmHeader');
                const container = document.getElementById('genericConfirmContainer');

                // clear previous handlers
                confirmBtn.onclick = null;
                confirmBtn.onclick = function() {
                    closeGenericConfirm();
                    if (typeof onConfirm === 'function') onConfirm();
                };

                // set variant classes: 'danger' -> red (default), 'positive' -> green
                const baseClasses = 'flex-1 px-4 py-3 rounded-xl transition-all font-bold shadow-lg hover:shadow-xl';
                if (variant === 'positive') {
                    confirmBtn.className = baseClasses + ' bg-green-600 text-white hover:bg-green-700';
                    if (header) header.className = 'sticky top-0 bg-gradient-to-r from-green-600 to-green-700 p-3 text-white rounded-t-xl z-10';
                    if (container) {
                        container.className = 'bg-green-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-green-700';
                    }
                } else {
                    confirmBtn.className = baseClasses + ' bg-red-600 text-white hover:bg-red-700';
                }

                modal.style.display = 'flex';
            }

            function closeGenericConfirm() {
                const modal = document.getElementById('genericConfirmModal');
                if (!modal) return;
                modal.style.display = 'none';
            }

            // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            // AJAX helpers
            // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

            /** Escape a string for safe HTML insertion */
            function escHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            /** Format a number with 2 decimals + commas */
            function phpNum(n) {
                return Number(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            /** Update the four metric cards from a metrics object */
            function updateMetrics(m) {
                document.querySelectorAll('[data-metric="totalMaterials"], [data-metric="totalMaterials-sub"]').forEach(el => el.textContent = m.totalMaterials);
                document.querySelectorAll('[data-metric="lowStockAlerts"], [data-metric="lowStockAlerts-sub"]').forEach(el => el.textContent = m.lowStockAlerts);
                document.querySelectorAll('[data-metric="newOrders"]').forEach(el => el.textContent = m.newOrders);
                document.querySelectorAll('[data-metric="pendingDeliveries"]').forEach(el => el.textContent = m.pendingDeliveries);
            }

            /** Build and return a material card DOM element */
            function renderMaterialCard(item) {
                const isLow = item.is_low_stock;
                const badge = isLow
                    ? '<span class="px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-xl shadow-lg">Low Stock</span>'
                    : '<span class="px-3 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-bold rounded-xl shadow-lg">In Stock</span>';
                const div = document.createElement('div');
                div.className = 'p-4 border-2 border-slate-600 rounded-xl hover:border-amber-500 hover:bg-slate-600/50 transition-all shadow-lg hover:shadow-xl backdrop-blur-sm cursor-pointer';
                div.setAttribute('data-name', item.name);
                div.setAttribute('data-category', item.category);
                div.setAttribute('data-id', item.id);
                div.setAttribute('data-type', 'material');
                div.onclick = () => openStockModal('material', item.id);
                div.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-white text-lg">${escHtml(item.name)}</h3>
                            <p class="text-sm text-slate-300 font-medium mt-1">${escHtml(item.supplier_name)} &bull; ${escHtml(item.unit)}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${badge}
                            <span class="text-white font-bold text-lg">₱${phpNum(item.unit_cost)}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2 text-sm">
                        <div>
                            <span class="text-slate-400 font-medium text-xs">Current Stock</span>
                            <p class="text-white font-bold text-lg mt-1">${item.current_stock} ${escHtml(item.unit)}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium text-xs">Min Stock</span>
                            <p class="text-white font-bold text-lg mt-1">${item.minimum_stock} ${escHtml(item.unit)}</p>
                        </div>
                        <div class="flex items-center space-x-2 justify-end">
                            <button onclick="event.stopPropagation(); openDeleteModal('material', ${item.id}, '${item.name.replace(/'/g, "\\'").replace(/"/g,'&quot;')}')" class="p-2.5 hover:bg-red-500/20 rounded-lg transition-all group" title="Delete">
                                <svg class="w-5 h-5 text-red-400 group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>`;
                return div;
            }

            /** Build and return a product card DOM element */
            function renderProductCard(item) {
                const div = document.createElement('div');
                div.className = 'p-4 border-2 border-slate-600 rounded-xl hover:border-amber-500 hover:bg-slate-600/50 transition-all shadow-lg hover:shadow-xl backdrop-blur-sm cursor-pointer';
                div.setAttribute('data-name', item.product_name);
                div.setAttribute('data-category', item.category);
                div.setAttribute('data-id', item.id);
                div.setAttribute('data-type', 'product');
                div.onclick = () => openStockModal('product', item.id);
                div.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-white text-lg">${escHtml(item.product_name)}</h3>
                            <p class="text-sm text-slate-300 font-medium mt-1">Finished Product</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-white font-bold text-lg">₱${phpNum(item.selling_price)}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2 text-sm">
                        <div>
                            <span class="text-slate-400 font-medium text-xs">Production Cost</span>
                            <p class="text-white font-bold text-lg mt-1">₱${phpNum(item.production_cost)}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium text-xs">Selling Price</span>
                            <p class="text-white font-bold text-lg mt-1">₱${phpNum(item.selling_price)}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 mt-2 justify-end">
                        <button onclick="event.stopPropagation(); openEditProductModal(${item.id})" class="p-2.5 hover:bg-slate-500 rounded-lg transition-all group" title="Edit">
                            <svg class="w-5 h-5 text-amber-400 group-hover:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button onclick="event.stopPropagation(); openDeleteModal('product', ${item.id}, '${item.product_name.replace(/'/g, "\\'").replace(/"/g,'&quot;')}')" class="p-2.5 hover:bg-red-500/20 rounded-lg transition-all group" title="Delete">
                            <svg class="w-5 h-5 text-red-400 group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>`;
                return div;
            }

            /**
             * Generic AJAX form submit helper.
             * Serialises FormData, sends via fetch, calls onSuccess(data) on success.
             */
            async function submitInventoryForm(form, url, method, onSuccess) {
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.dataset.originalText = submitBtn.textContent;
                    submitBtn.textContent = 'Saving...';
                }
                const formData = new FormData(form);
                if (method !== 'POST') formData.append('_method', method);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });
                    if (!response.ok) {
                        let errMsg;
                        try { const d = await response.json(); errMsg = d.message || (d.errors ? Object.values(d.errors).flat().join(' ') : null); } catch (_) {}
                        if (response.status === 404) throw new Error(errMsg || 'Resource not found (404).');
                        if (response.status === 422) throw new Error(errMsg || 'Validation failed (422). Please check your inputs.');
                        if (response.status === 403) throw new Error(errMsg || 'Permission denied (403).');
                        if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                        throw new Error(errMsg || `Request failed (${response.status}).`);
                    }
                    const data = await response.json();
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText || 'Save'; }
                    if (data.success) { onSuccess(data); }
                    else { showErrorNotification(data.message || 'An error occurred.'); }
                } catch (error) {
                    console.error('Form submit error:', error);
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText || 'Save'; }
                    showErrorNotification(error.message || 'An error occurred.');
                }
            }

            // â”€â”€ Attach form submit interceptors (AJAX) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            document.addEventListener('DOMContentLoaded', function() {
                // Add Product
                const addForm = document.getElementById('addProductForm');
                if (addForm) {
                    addForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Add Product', 'Are you sure you want to add this product with the listed materials?', function() {
                            submitInventoryForm(addForm, '/inventory', 'POST', function(data) {
                                if (data.item) {
                                    const table = document.getElementById('products-table');
                                    const empty = table.querySelector('.py-12');
                                    if (empty) empty.remove();
                                    table.prepend(renderProductCard(data.item));
                                }
                                closeAddProductModal();
                                showSuccessNotification(data.message || 'Product added successfully!');
                                localStorage.setItem('activeInventoryTab', 'products');
                                showTab('products');
                            });
                        }, 'positive');
                    });
                }

                // Add Material
                const addItemForm = document.getElementById('addItemForm');
                if (addItemForm) {
                    addItemForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Add Material', 'Are you sure you want to add this material?', function() {
                            submitInventoryForm(addItemForm, '/inventory', 'POST', function(data) {
                                if (data.item) {
                                    const table = document.getElementById('materials-table');
                                    const empty = table.querySelector('.py-12');
                                    if (empty) empty.remove();
                                    table.prepend(renderMaterialCard(data.item));
                                }
                                closeAddItemModal();
                                showSuccessNotification(data.message || 'Material added successfully!');
                                if (data.metrics) updateMetrics(data.metrics);
                                showTab('materials');
                            });
                        }, 'positive');
                    });
                }

                // Edit Product
                const editForm = document.getElementById('editProductForm');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Update Product', 'Are you sure you want to update this product and its materials?', function() {
                            const url = editForm.getAttribute('action');
                            submitInventoryForm(editForm, url, 'PUT', function(data) {
                                if (data.item) {
                                    const oldCard = document.querySelector(`[data-id="${data.item.id}"][data-type="product"]`);
                                    if (oldCard) oldCard.replaceWith(renderProductCard(data.item));
                                }
                                closeEditProductModal();
                                showSuccessNotification(data.message || 'Product updated successfully!');
                                localStorage.setItem('activeInventoryTab', 'products');
                            });
                        }, 'positive');
                    });
                }
            });

            function openStockLogsModal() {
                document.getElementById('stockLogsModal').classList.remove('hidden');
                loadStockLogs();
            }

            function closeStockLogsModal() {
                document.getElementById('stockLogsModal').classList.add('hidden');
            }

            async function loadStockLogs() {
                const dateFrom = document.getElementById('logDateFromFilter')?.value || '';
                const dateTo = document.getElementById('logDateToFilter')?.value || '';

                const params = new URLSearchParams();
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                const stockMovementsUrl = `/inventory/stock-movements/report?${params.toString()}`;
                try {
                    const res = await fetch(stockMovementsUrl);
                    if (!res.ok) {
                        let errMsg;
                        try { const d = await res.json(); errMsg = d.message; } catch (_) {}
                        if (res.status === 404) throw new Error(errMsg || 'Stock movements endpoint not found (404).');
                        if (res.status === 500) throw new Error(errMsg || 'Server error (500) loading stock logs.');
                        throw new Error(errMsg || `Failed to load stock logs (${res.status}).`);
                    }
                    const movementData = await res.json();
                    if (movementData.success) {
                        displayStockLogs(movementData.movements, movementData.summary);
                        filterStockLogs();
                    } else {
                        showErrorNotification(movementData.message || 'Failed to load stock logs.');
                    }
                } catch (error) {
                    console.error('Error loading stock logs:', error);
                    showErrorNotification(error.message || 'An error occurred while loading stock logs.');
                }
            }

            function filterStockLogs() {
                const searchText = document.getElementById('logSearchFilter')?.value.toLowerCase() || '';
                const stockInBody = document.getElementById('stockInTable');
                const stockOutBody = document.getElementById('stockOutTable');

                if (!searchText) {
                    // If search is empty, show all rows
                    document.querySelectorAll('#stockInTable tr, #stockOutTable tr').forEach(row => {
                        if (row.querySelector('td[colspan]')) return; // Skip empty message rows
                        row.style.display = '';
                    });
                    return;
                }

                // Filter Stock IN table - use Array.from().filter() for broad browser compatibility
                const stockInRows = Array.from(stockInBody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
                stockInRows.forEach(row => {
                    const material = row.cells[1]?.textContent.toLowerCase() || '';
                    const poId = row.cells[3]?.textContent.toLowerCase() || '';
                    const supplier = row.cells[4]?.textContent.toLowerCase() || '';
                    const notes = row.cells[5]?.textContent.toLowerCase() || '';
                    
                    const matches = material.includes(searchText) || poId.includes(searchText) || 
                                  supplier.includes(searchText) || notes.includes(searchText);
                    row.style.display = matches ? '' : 'none';
                });

                // Filter Stock OUT table - use Array.from().filter() for broad browser compatibility
                const stockOutRows = Array.from(stockOutBody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
                stockOutRows.forEach(row => {
                    const material = row.cells[1]?.textContent.toLowerCase() || '';
                    const workOrder = row.cells[3]?.textContent.toLowerCase() || '';
                    const reference = row.cells[4]?.textContent.toLowerCase() || '';
                    const notes = row.cells[5]?.textContent.toLowerCase() || '';
                    
                    const matches = material.includes(searchText) || workOrder.includes(searchText) || 
                                  reference.includes(searchText) || notes.includes(searchText);
                    row.style.display = matches ? '' : 'none';
                });
            }

            function switchTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });
                
                // Remove active class from all tab buttons
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('active');
                });
                
                // Show selected tab content
                const selectedContent = document.getElementById('content' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                    selectedContent.classList.add('active');
                }
                
                // Add active class to selected tab button
                const selectedButton = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
                if (selectedButton) {
                    selectedButton.classList.add('active');
                }

                // Re-apply search filter to the newly visible tab
                filterStockLogs();
            }
            
            function displayStockLogs(movements, summary) {
                const stockInBody = document.getElementById('stockInTable');
                const stockOutBody = document.getElementById('stockOutTable');
                const stockOutSummaryBody = document.getElementById('stockOutSummaryTable');

                const stockInRecords = movements.filter(movement => movement.movement_type === 'in');
                const stockOut = movements.filter(movement => movement.movement_type === 'out');

                if (stockInRecords.length === 0) {
                    stockInBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock IN records found</td></tr>';
                } else {
                    stockInBody.innerHTML = stockInRecords.map(movement => {
                        const poId = movement.po_number || '-';
                        const userName = movement.user_name || 'Admin';
                        const quantity = `${Number(movement.quantity).toFixed(2)} ${movement.unit || ''}`.trim();
                        const dateTime = movement.date ? `${movement.date} ${movement.time || ''}`.trim() : '-';

                        return `
                            <tr class="hover:bg-green-50 transition-all duration-200">
                                <td class="px-4 py-3 text-gray-700 text-sm">${dateTime}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${movement.item_name || 'Unknown Material'}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 font-semibold text-green-700 bg-green-100 rounded-full">+${quantity}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-semibold">${poId}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-600 border border-slate-200">
                                            ${userName.substring(0, 2).toUpperCase()}
                                        </div>
                                        ${userName}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="${movement.notes || '-'}">${movement.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                }

                if (stockOut.length === 0) {
                    stockOutBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock OUT records found</td></tr>';
                } else {
                    stockOutBody.innerHTML = stockOut.map(movement => {
                        const quantity = `${Number(movement.quantity).toFixed(2)} ${movement.unit || ''}`;
                        const woId = movement.wo_id || '-';
                        const userName = movement.user_name || 'Admin';

                        return `
                            <tr class="hover:bg-red-50 transition-all duration-200">
                                <td class="px-4 py-3 text-gray-700 text-sm">${movement.date} ${movement.time}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${movement.item_name}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 font-semibold text-red-700 bg-red-100 rounded-full">-${quantity}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-semibold">${woId}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-600 border border-slate-200">
                                            ${userName.substring(0, 2).toUpperCase()}
                                        </div>
                                        ${userName}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="${movement.notes || '-'}">${movement.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                }

                document.getElementById('logTotalIn').textContent = Number(summary?.total_in || 0).toFixed(2);
                document.getElementById('logTotalOut').textContent = Number(summary?.total_out || 0).toFixed(2);
                document.getElementById('logTotalMovements').textContent = Number(summary?.total_movements || 0);
            }

// --- Second Script Block ---

            // Enhanced Purchase Order Selection Logic
            function selectPurchaseOrder(card, orderId) {
                // Remove selected class from all cards
                document.querySelectorAll('.po-card').forEach(c => {
                    c.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    c.classList.add('border-slate-200', 'bg-white');
                });

                // Add selected class to current card
                card.classList.remove('border-slate-200', 'bg-white');
                card.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');

                // Update hidden input and load items
                document.getElementById('selectedPurchaseOrderId').value = orderId;
                loadPurchaseOrderItems(orderId);
            }

            // PO Search Logic
            document.addEventListener('DOMContentLoaded', function() {
                const poSearchInput = document.getElementById('poSearchInput');
                if (poSearchInput) {
                    poSearchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const cards = document.querySelectorAll('.po-card');
                        
                        cards.forEach(card => {
                            const poNum = card.dataset.poNum.toLowerCase();
                            const supplier = card.dataset.supplier.toLowerCase();
                            
                            if (poNum.includes(searchTerm) || supplier.includes(searchTerm)) {
                                card.classList.remove('hidden');
                            } else {
                                card.classList.add('hidden');
                            }
                        });
                    });
                }
            });

            // Receive Stock Modal Functions
            function openReceiveStockModal() {
                document.getElementById('receiveStockModal').classList.remove('hidden');
                // Reset PO cards selection
                document.querySelectorAll('.po-card').forEach(c => {
                    c.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    c.classList.add('border-slate-200', 'bg-white');
                    c.classList.remove('hidden');
                });
                document.getElementById('selectedPurchaseOrderId').value = '';
                if (document.getElementById('poSearchInput')) document.getElementById('poSearchInput').value = '';
            }

            function closeReceiveStockModal() {
                document.getElementById('receiveStockModal').classList.add('hidden');
                document.getElementById('receiveStockForm').reset();
                document.getElementById('selectedPurchaseOrderId').value = '';
                document.getElementById('receiveStockItems').innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 px-3">
                        <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-slate-500 text-sm font-medium">Please select a purchase order to view items</p>
                        <p class="text-slate-400 text-xs mt-1">Materials from the selected PO will appear here</p>
                    </div>
                `;
                const itemsCount = document.getElementById('itemsCount');
                if (itemsCount) itemsCount.classList.add('hidden');
            }

            // Enhanced loadPurchaseOrderItems function with improved UI
            function loadPurchaseOrderItems(orderId) {
                const container = document.getElementById('receiveStockItems');
                const itemsCount = document.getElementById('itemsCount');

                if (!orderId) {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 px-3">
                            <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-slate-500 text-sm font-medium">Please select a purchase order to view items</p>
                            <p class="text-slate-400 text-xs mt-1">Materials from the selected PO will appear here</p>
                        </div>
                    `;
                    itemsCount.classList.add('hidden');
                    return;
                }

                container.innerHTML = `
                    <div class="flex items-center justify-center py-12">
                        <div class="flex flex-col items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500 mb-4"></div>
                            <p class="text-slate-600 text-sm font-medium">Loading items...</p>
                        </div>
                    </div>
                `;

                (async () => {
                try {
                    const response = await fetch(`/procurement/purchase-orders/${orderId}/items`);
                    if (!response.ok) {
                        let errMsg;
                        try { const d = await response.json(); errMsg = d.message; } catch (_) {}
                        if (response.status === 404) throw new Error(errMsg || 'Purchase order not found (404).');
                        if (response.status === 403) throw new Error(errMsg || 'Access denied (403).');
                        if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                        throw new Error(errMsg || `Failed to load PO items (${response.status}).`);
                    }
                    const data = await response.json();
                    if (!data.success || !data.items || data.items.length === 0) {
                            container.innerHTML = `
                                <div class="flex flex-col items-center justify-center py-12 px-3">
                                    <svg class="w-16 h-16 text-orange-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-slate-600 text-sm font-bold">No Remaining Items</p>
                                    <p class="text-slate-500 text-xs mt-1">All items have been fully received for this purchase order</p>
                                </div>
                            `;
                            itemsCount.classList.add('hidden');
                            return;
                        }

                        const itemsToReceive = data.items.filter(item => Number(item.remaining_quantity || 0) > 0);
                        
                        if (itemsToReceive.length === 0) {
                            container.innerHTML = `
                                <div class="flex flex-col items-center justify-center py-12 px-3">
                                    <svg class="w-16 h-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-600 text-sm font-bold">All Items Received</p>
                                    <p class="text-slate-500 text-xs mt-1">This purchase order has been fully received</p>
                                </div>
                            `;
                            itemsCount.classList.add('hidden');
                            return;
                        }

                        // Update items count
                        itemsCount.textContent = `${itemsToReceive.length} item${itemsToReceive.length !== 1 ? 's' : ''}`;
                        itemsCount.classList.remove('hidden');

                        const itemsHtml = itemsToReceive.map((item, index) => `
                            <div class="p-2 border-b-2 border-slate-200 hover:bg-amber-50/50 transition-all ${index === 0 ? 'border-t-0' : ''}">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <!-- Material Name -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                            </svg>
                                            Material
                                        </label>
                                        <p class="text-gray-900 font-bold text-sm">${item.material_name}</p>
                                        <p class="text-xs text-gray-500 mt-1 font-medium">Unit: ${item.unit ? item.unit : 'N/A'}</p>
                                    </div>
                                    
                                    <!-- Ordered Quantity -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Ordered</label>
                                        <div class="px-3 py-2 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                            <p class="text-blue-700 font-bold text-sm">${Number(item.ordered_quantity).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Already Received -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Received</label>
                                        <div class="px-3 py-2 bg-green-50 border-2 border-green-200 rounded-lg">
                                            <p class="text-green-700 font-bold text-sm">${Number(item.already_received).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Remaining -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Remaining</label>
                                        <div class="px-3 py-2 bg-amber-50 border-2 border-amber-300 rounded-lg">
                                            <p class="text-amber-700 font-bold text-sm">${Number(item.remaining_quantity).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Defect Quantity Input -->
                                    <div>
                                        <label class="block text-xs font-bold text-red-700 mb-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Defects
                                        </label>
                                        <input
                                            type="number"
                                            name="items[${index}][defect_quantity]"
                                            step="1"
                                            min="0"
                                            max="${Number(item.remaining_quantity).toFixed(2)}"
                                            value="0"
                                            class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                            placeholder="0.00"
                                            onchange="updateNetQuantity(this, ${index}, ${Number(item.remaining_quantity).toFixed(2)})"
                                        >
                                        <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                    </div>
                                </div>
                                
                                <!-- Net Quantity Display -->
                                <div class="mt-2 p-2 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-green-800 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Net Quantity to Add to Stock:
                                        </span>
                                        <span id="netQty${index}" class="text-lg font-bold text-green-700">
                                            ${Number(item.remaining_quantity).toFixed(2)} ${item.unit}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        container.innerHTML = itemsHtml;
                } catch (error) {
                    console.error('Error loading PO items:', error);
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 px-3">
                            <svg class="w-16 h-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-red-600 text-sm font-bold">Error Loading Items</p>
                            <p class="text-slate-500 text-xs mt-1">${error.message || 'Failed to load items for this purchase order'}</p>
                        </div>
                    `;
                    itemsCount.classList.add('hidden');
                }
                })();
            }

            // Calculate net quantity after defects
            function updateNetQuantity(input, index, remaining) {
                const defectQty = parseFloat(input.value) || 0;
                const netQty = Math.max(0, remaining - defectQty);
                const unit = input.closest('.grid').querySelector('p.text-xs').textContent.replace('Unit: ', '');
                
                const netQtyDisplay = document.getElementById(`netQty${index}`);
                if (netQtyDisplay) {
                    netQtyDisplay.textContent = `${netQty.toFixed(2)} ${unit}`;
                    
                    // Change color based on whether there are defects
                    const container = netQtyDisplay.closest('.bg-gradient-to-r');
                    if (defectQty > 0) {
                        container.className = 'mt-2 p-2 bg-gradient-to-r from-yellow-50 to-yellow-100 border-2 border-yellow-300 rounded-lg';
                        netQtyDisplay.className = 'text-lg font-bold text-yellow-700';
                    } else {
                        container.className = 'mt-2 p-2 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg';
                        netQtyDisplay.className = 'text-lg font-bold text-green-700';
                    }
                }
            }

            // Form submission handler
            const receiveStockForm = document.getElementById('receiveStockForm');
            if (receiveStockForm) {
                receiveStockForm.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const purchaseOrderInput = document.getElementById('selectedPurchaseOrderId');
                    const purchaseOrderId = purchaseOrderInput ? purchaseOrderInput.value : null;

                    if (!purchaseOrderId) {
                        showErrorNotification('Please select a purchase order first.');
                        return;
                    }

                    const formData = new FormData(receiveStockForm);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    try {
                        const response = await fetch(`/procurement/purchase-orders/${purchaseOrderId}/receive-stock`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        if (!response.ok) {
                            let errMsg;
                            try { const d = await response.json(); errMsg = d.message; } catch (_) {}
                            if (response.status === 404) throw new Error(errMsg || 'Purchase order not found (404). It may have been cancelled or deleted.');
                            if (response.status === 422) throw new Error(errMsg || 'Validation failed (422). Please check the quantities entered.');
                            if (response.status === 409) throw new Error(errMsg || 'Conflict (409). This stock may have already been received.');
                            if (response.status === 403) throw new Error(errMsg || 'Permission denied (403).');
                            if (response.status === 500) throw new Error(errMsg || 'Server error (500). Please try again.');
                            throw new Error(errMsg || `Failed to receive stock (${response.status}).`);
                        }
                        const data = await response.json();
                        if (data.success) {
                            showSuccessNotification(data.message || 'Stock received successfully.');
                            window.location.reload();
                        } else {
                            showErrorNotification(data.message || 'Failed to receive stock.');
                        }
                    } catch (error) {
                        console.error('Stock receive error:', error);
                        showErrorNotification(error.message || 'An error occurred while receiving stock.');
                    }
                });
            }
            function setQuickFilter(period) {
                const today = new Date();
                let fromDate = new Date();
                let toDate = new Date();

                switch(period) {
                    case 'today':
                        break;
                    case 'yesterday':
                        fromDate.setDate(today.getDate() - 1);
                        toDate.setDate(today.getDate() - 1);
                        break;
                    case 'last_week':
                        fromDate.setDate(today.getDate() - 7);
                        break;
                    case '1_month':
                        fromDate.setMonth(today.getMonth() - 1);
                        break;
                    case '1_year':
                        fromDate.setFullYear(today.getFullYear() - 1);
                        break;
                }

                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                document.getElementById('logDateFromFilter').value = formatDate(fromDate);
                document.getElementById('logDateToFilter').value = formatDate(toDate);
                
                loadStockLogs();
            }


