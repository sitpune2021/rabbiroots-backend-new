let variantIndex = 0;

document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('addVariantBtn');
    if (addBtn) addBtn.addEventListener('click', addVariant);

    const container = document.getElementById('variantsContainer');
    if (container) {
        container.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-variant-remove]');
            if (btn) {
                const idx = btn.getAttribute('data-variant-remove');
                removeVariant(idx);
            }
        });
    }

    // Promo Code Form 
    initPromoCodeForm();
});

function addVariant() {
    const container = document.getElementById("variantsContainer");
    const index = variantIndex++;

    const variantHtml = `
                <div class="variant-row mb-3 p-3 border rounded" id="variant-${index}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Variant #${index + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" data-variant-remove="${index}">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Variant Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="variants[${index}][variant_name]" 
                                placeholder="250g, 500g, 1kg" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="variants[${index}][sku]" 
                                placeholder="SKU-${Date.now()}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="variants[${index}][price]" 
                                placeholder="99.00" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sale Price (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="variants[${index}][sale_price]" 
                                placeholder="79.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="variants[${index}][stock]" 
                                placeholder="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Min Order Qty</label>
                            <input type="number" class="form-control" name="variants[${index}][min_order_qty]" 
                                value="1" placeholder="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Order Qty</label>
                            <input type="number" class="form-control" name="variants[${index}][max_order_qty]" 
                                placeholder="Optional">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="variants[${index}][status]" 
                                    value="1" id="variant_status_${index}" checked>
                                <label class="form-check-label" for="variant_status_${index}">
                                    Active Variant
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            `;

    container.insertAdjacentHTML("beforeend", variantHtml);
}

function removeVariant(index) {
    const variant = document.getElementById(`variant-${index}`);
    if (variant) {
        variant.remove();
    }
}

// Promo Code (promo create/edit pages)
function initPromoCodeForm() {
    const specificStore = document.getElementById('specificStore');
    const allStores = document.getElementById('allStores');
    const storeSelection = document.getElementById('storeSelection');
    const discountTypeRadios = document.querySelectorAll('input[name="discount_type"]');
    const discountUnit = document.getElementById('discountUnit');

    if (!specificStore || !allStores || !storeSelection) {
        return;
    }

    specificStore.addEventListener('change', function() {
        if (this.checked) {
            storeSelection.style.display = 'block';
        }
    });

    allStores.addEventListener('change', function() {
        if (this.checked) {
            storeSelection.style.display = 'none';
        }
    });

    if (discountTypeRadios.length > 0 && discountUnit) {
        discountTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                discountUnit.textContent = this.value === 'percentage' ? '%' : '₹';
            });
        });
    }
}
