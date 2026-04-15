document.addEventListener("DOMContentLoaded", function () {

    const config = window.productFormConfig || {};
    const form = document.getElementById("productForm");
    if (!form) return;

    /* =====================================================
       UTIL
    ===================================================== */

    const qs = (sel, parent = document) => parent.querySelector(sel);
    const qsa = (sel, parent = document) => parent.querySelectorAll(sel);

    let variantIndex = 0;
    let attrIndex = 0;

    /* =====================================================
       SLUG AUTO GENERATE
    ===================================================== */

    const nameInput = qs("#name");
    const slugInput = qs("#slug");

    nameInput?.addEventListener("keyup", function () {
        const slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)/g, "");

        slugInput.value = slug;
    });

    /* =====================================================
       IMAGE PREVIEW
    ===================================================== */

    window.previewImages = function (input) {

        const previewBox = document.getElementById("image-preview");
        if (!previewBox) return;

        const existingCount = previewBox.querySelectorAll(".image-preview-item").length;

        Array.from(input.files).forEach((file, index) => {

            const reader = new FileReader();

            reader.onload = function (e) {

                const wrapper = document.createElement("div");
                wrapper.className = "position-relative image-preview-item";

                const img = document.createElement("img");
                img.src = e.target.result;
                img.className = "rounded border";
                img.style.width = "110px";
                img.style.height = "110px";
                img.style.objectFit = "cover";

                // Primary badge only if first overall image
                if (existingCount === 0 && index === 0) {
                    const badge = document.createElement("span");
                    badge.innerText = "Primary";
                    badge.className = "badge bg-primary position-absolute top-0 start-0";
                    wrapper.appendChild(badge);
                }

                wrapper.appendChild(img);
                previewBox.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });

        // DO NOT clear input value
    };


    /* =====================================================
       VARIANTS
    ===================================================== */

    function addVariant(data = null) {

        let tpl = qs("#variant-template").innerHTML;
        tpl = tpl.replaceAll("__i__", variantIndex);

        qs("#variants-wrapper").insertAdjacentHTML("beforeend", tpl);

        const container = qsa(".variant-item")[variantIndex];

        if (data) {
            Object.keys(data).forEach(key => {
                const input = qs(`[name="variants[${variantIndex}][${key}]"]`, container);
                if (input) {
                    if (input.type === "checkbox") {
                        input.checked = !!data[key];
                    } else {
                        input.value = data[key] ?? "";
                    }
                }
            });

            // existing images
            if (data.images?.length) {
                const imageContainer = document.createElement("div");
                imageContainer.className = "d-flex gap-2 flex-wrap mt-2";

                data.images.forEach(img => {
                    const wrapper = document.createElement("div");
                    wrapper.className = "position-relative existing-variant-image";

                    wrapper.innerHTML = `
                        <img src="/storage/${img.image}"
                            style="width:90px;height:90px;object-fit:cover"
                            class="rounded border">
                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-variant-image">
                            ✕
                        </button>
                        <input type="hidden"
                            name="variants[${variantIndex}][existing_images][]"
                            value="${img.id}">
                    `;

                    imageContainer.appendChild(wrapper);
                });

                container.appendChild(imageContainer);
            }

            const fileInput = container.querySelector(
                `input[name="variants[${variantIndex}][images][]"]`
            );

            fileInput?.addEventListener("change", function () {

                let previewWrapper = container.querySelector(".variant-preview-wrapper");

                if (!previewWrapper) {
                    previewWrapper = document.createElement("div");
                    previewWrapper.className = "d-flex gap-2 flex-wrap mt-2 variant-preview-wrapper";
                    container.appendChild(previewWrapper);
                }

                Array.from(this.files).forEach((file) => {

                    const reader = new FileReader();

                    reader.onload = function (e) {

                        const imgWrap = document.createElement("div");
                        imgWrap.className = "position-relative";

                        imgWrap.innerHTML = `
                <img src="${e.target.result}"
                    style="width:90px;height:90px;object-fit:cover"
                    class="rounded border">
            `;

                        previewWrapper.appendChild(imgWrap);
                    };

                    reader.readAsDataURL(file);
                });

            });


            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = `variants[${variantIndex}][id]`;
            hidden.value = data.id;
            container.appendChild(hidden);
        }

        variantIndex++;
    }

    // default variant rule
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("default-variant")) {
            qsa(".default-variant").forEach(cb => {
                if (cb !== e.target) cb.checked = false;
            });
        }
    });

    // load existing
    if (config.variants?.length) {
        config.variants.forEach(v => addVariant(v));
    } else {
        addVariant();
    }

    qs("#addVariantBtn")?.addEventListener("click", () => addVariant());

    /* =====================================================
       ATTRIBUTES
    ===================================================== */

    function addAttribute(data = null) {

        let tpl = qs("#attribute-template").innerHTML;
        tpl = tpl.replaceAll("__i__", attrIndex);

        qs("#attributes-wrapper").insertAdjacentHTML("beforeend", tpl);

        const row = qsa('[data-role="attribute-row"]')[attrIndex];

        if (data) {
            row.querySelector(`[name="attributes[${attrIndex}][attribute_key]"]`).value = data.attribute_key ?? "";
            row.querySelector(`[name="attributes[${attrIndex}][attribute_value]"]`).value = data.attribute_value ?? "";
            row.querySelector(`[name="attributes[${attrIndex}][is_filterable]"]`).checked = !!data.is_filterable;
            row.querySelector(`[name="attributes[${attrIndex}][is_visible]"]`).checked = !!data.is_visible;

            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = `attributes[${attrIndex}][id]`;
            hidden.value = data.id;
            row.appendChild(hidden);
        }

        attrIndex++;
    }

    config.attributes?.forEach(a => addAttribute(a));

    qs("#addAttributeBtn")?.addEventListener("click", () => addAttribute());

    document.addEventListener("click", function (e) {

        if (e.target.closest(".remove-attribute")) {
            e.target.closest('[data-role="attribute-row"]')?.remove();
        }

        if (e.target.classList.contains("remove-product-image")) {
            const wrapper = e.target.closest(".existing-image");
            wrapper?.querySelector("input")?.remove();
            wrapper?.remove();
        }

        if (e.target.classList.contains("remove-variant-image")) {
            const wrapper = e.target.closest(".existing-variant-image");
            wrapper?.querySelector("input")?.remove();
            wrapper?.remove();
        }
    });

    /* =====================================================
       SHELF LIFE
    ===================================================== */

    function toggleShelfLife() {
        const perishable = qs('input[name="is_perishable"]');
        const box = qs("#shelfLifeBox");
        if (!perishable || !box) return;
        box.style.display = perishable.checked ? "block" : "none";
    }

    toggleShelfLife();

    document.addEventListener("change", function (e) {
        if (e.target.name === "is_perishable") toggleShelfLife();
        if (e.target.name === "contains_allergens" && e.target.checked) {
            qs('[name="ingredients"]')?.focus();
        }
    });

    /* =====================================================
       AJAX SUBMIT
    ===================================================== */

    form.addEventListener("submit", function (e) {

        // e.preventDefault();

        const submitBtn = form.querySelector("button[type='submit']");
        const formData = new FormData(form);

        qsa(".invalid-feedback").forEach(el => el.remove());
        qsa(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

        // submitBtn.disabled = true;
        // submitBtn.innerHTML = "Saving...";

        fetch(form.action, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(res => {
                alert(res.message || "Saved successfully");
                if (res.redirect) window.location.href = res.redirect;
            })
            .catch(err => {
                if (err.errors) showValidationErrors(err.errors);
                else console.error(err);
            })
            .finally(() => {
                // submitBtn.disabled = false;
                // submitBtn.innerHTML = config.isEdit ? "Update Product" : "Create Product";
            });
    });

    function showValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const messages = errors[field];
            let input = qs(`[name="${field}"]`);

            if (!input) {
                input = qs(`[name="${field.replace(/\./g, "[")}]`);
            }

            if (!input) return;

            input.classList.add("is-invalid");

            const errorDiv = document.createElement("div");
            errorDiv.classList.add("invalid-feedback");
            errorDiv.innerText = messages[0];

            input.closest(".mb-3, .col-md-2, .col-md-3, .col-md-4, .col-md-6")
                ?.appendChild(errorDiv);
        });
    }

});
