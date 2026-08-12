<?php
include '../../function.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$cats = data("categories");
$catOptionsHtml = "<option value=''>Select Category</option>";
if ($cats) {
    foreach ($cats as $c) {
        $catOptionsHtml .= "<option value='" . (int)$c['id'] . "'>" . htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') . "</option>";
    }
}
?>

<style>
.premium-panel{
    background: linear-gradient(180deg,#0f172a,#111827);
    color:#e5e7eb;
    border:1px solid rgba(255,255,255,.08);
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
}
.premium-panel .form-control,
.premium-panel .form-select,
.premium-panel textarea{
    background:#0b1220;
    color:#fff;
    border:1px solid #263244;
}
.premium-panel .form-control::placeholder,
.premium-panel textarea::placeholder{ color:#94a3b8; }
.premium-panel .form-check-label,
.premium-panel .form-label{ color:#e5e7eb; }
.premium-panel .form-check-input:checked{
    background-color:#7c3aed;
    border-color:#7c3aed;
}
.bulk-card{
    border:1px solid rgba(124,58,237,.18);
    border-radius:22px;
    background: linear-gradient(180deg,rgba(15,23,42,.96),rgba(17,24,39,.96));
    box-shadow: 0 14px 40px rgba(0,0,0,.20);
    overflow:hidden;
}
.bulk-card-head{
    background: linear-gradient(135deg,#7c3aed,#2563eb);
    color:#fff;
    padding:16px 18px;
}
.bulk-row{
    border:1px solid rgba(148,163,184,.18);
    border-radius:18px;
    background: rgba(2,6,23,.35);
    padding:18px;
    margin-bottom:18px;
}
.row-title{ font-size:15px; font-weight:800; color:#fff; }
.row-remove{
    border:none;
    background: rgba(239,68,68,.16);
    color:#fecaca;
    border-radius:10px;
    padding:8px 10px;
}
.row-remove:hover{ background:#ef4444; color:#fff; }

/* ── 4-Image Gallery Upload ── */
.img-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}
.img-slot{
    position:relative;
    border:1.5px dashed rgba(148,163,184,.35);
    border-radius:14px;
    overflow:hidden;
    background:#0b1220;
    min-height:120px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    cursor:pointer;
    transition:.2s;
}
.img-slot:hover{ border-color:#7c3aed; }
.img-slot input[type=file]{
    position:absolute;
    inset:0;
    opacity:0;
    cursor:pointer;
    width:100%;
    height:100%;
}
.img-slot img{
    width:100%;
    height:120px;
    object-fit:cover;
    display:none;
    border-radius:12px;
}
.img-slot .slot-label{
    color:#64748b;
    font-size:11px;
    font-weight:700;
    text-align:center;
    pointer-events:none;
}
.img-slot .slot-icon{
    font-size:22px;
    color:#374151;
    margin-bottom:5px;
    pointer-events:none;
}
.img-slot .img-remove{
    position:absolute;
    top:5px;
    right:5px;
    background:rgba(239,68,68,.85);
    color:#fff;
    border:none;
    border-radius:50%;
    width:22px;
    height:22px;
    font-size:11px;
    display:none;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    z-index:5;
}

/* ── Variant Section ── */
.variant-panel{
    border:1px solid rgba(124,58,237,.22);
    border-radius:14px;
    background:rgba(124,58,237,.04);
    padding:14px;
    margin-top:14px;
}
.variant-chip-wrap{
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-top:8px;
    min-height:34px;
    align-items:center;
}
.variant-chip{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:rgba(124,58,237,.14);
    border:1px solid rgba(124,58,237,.28);
    color:#c4b5fd;
    border-radius:999px;
    padding:5px 12px;
    font-size:12px;
    font-weight:700;
}
.variant-chip .chip-remove{
    background:none;
    border:none;
    color:#f87171;
    padding:0;
    line-height:1;
    cursor:pointer;
    font-size:13px;
}
.color-dot{
    width:14px;
    height:14px;
    border-radius:50%;
    border:1.5px solid rgba(255,255,255,.3);
    display:inline-block;
    flex-shrink:0;
}
.variant-add-row{
    display:flex;
    gap:8px;
    margin-top:10px;
    align-items:center;
}
.variant-add-row input{
    flex:1;
    min-width:0;
}
.variant-add-btn{
    background:linear-gradient(135deg,#7c3aed,#4f46e5);
    color:#fff;
    border:none;
    border-radius:10px;
    padding:8px 14px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}
.variant-toggle-btn{
    background:none;
    border:1px solid rgba(124,58,237,.3);
    color:#a78bfa;
    border-radius:10px;
    padding:6px 14px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    transition:.2s;
    margin-top:10px;
}
.variant-toggle-btn:hover{ background:rgba(124,58,237,.12); }
.variant-body{ display:none; }
.variant-body.open{ display:block; }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0" style="color:#7c3aed;">Add Products</h3>
            <small class="text-muted">Add 1 to 8 products in one submit. Empty rows are skipped.</small>
        </div>
    </div>

    <?php
    if (isset($_GET['msg'])) {
        $msg = $_GET['msg'];
        $count = isset($_GET['count']) ? (int)$_GET['count'] : 0;
        if ($msg === 'success')           echo "<div class='alert alert-success'>Added successfully. Total inserted: {$count}</div>";
        elseif ($msg === 'partial')       echo "<div class='alert alert-warning'>Some rows were skipped. Total inserted: {$count}</div>";
        elseif ($msg === 'no_valid_products') echo "<div class='alert alert-danger'>No valid product rows found.</div>";
        elseif ($msg === 'invalid_input') echo "<div class='alert alert-danger'>Please fill required fields.</div>";
        elseif ($msg === 'upload_failed') echo "<div class='alert alert-danger'>One or more images failed to upload.</div>";
    }
    ?>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="bulk-card-head">
            <h4 class="mb-0 fw-bold"><i class="fa-solid fa-boxes-stacked me-2"></i>Bulk Product Add</h4>
            <small class="opacity-75">Each product supports 4 view images, optional sizes & colors.</small>
        </div>

        <div class="card-body p-4 premium-panel">
            <form action="../actions/insert_product.php" method="POST" enctype="multipart/form-data" id="bulkProductForm">
                <div id="productRows"></div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <button type="button" class="btn btn-outline-light" id="addRowBtn">
                        <i class="fa-solid fa-plus me-2"></i>Add More Product
                    </button>
                    <button type="submit" class="btn btn-lg text-white fw-semibold" style="background:linear-gradient(135deg,#7c3aed,#2563eb);">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Products
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const categoryOptionsHtml = <?= json_encode($catOptionsHtml) ?>;
let rowCounter = 0;
const MAX_ROWS = 8;

function updateAddButtonState() {
    document.getElementById('addRowBtn').disabled = document.querySelectorAll('.bulk-row').length >= MAX_ROWS;
}

function addProductRow() {
    const rowsWrap = document.getElementById('productRows');
    const currentRows = document.querySelectorAll('.bulk-row').length;
    if (currentRows >= MAX_ROWS) return;

    const rowId = 'row_' + (++rowCounter);
    const n = currentRows + 1;

    const html = `
    <div class="bulk-row" id="${rowId}">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="row-title">Product Row ${n}</div>
            <button type="button" class="row-remove" onclick="removeProductRow('${rowId}')">
                <i class="fa-solid fa-trash-can me-1"></i> Remove
            </button>
        </div>

        <div class="row g-3">
            <!-- LEFT: text fields -->
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Product Name</label>
                        <input type="text" name="name[]" class="form-control" placeholder="Product Name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id[]" id="category_${rowId}" class="form-select" onchange="loadSubcategories('${rowId}')">
                            ${categoryOptionsHtml}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subcategory</label>
                        <select name="subcategory_id[]" id="subcategory_${rowId}" class="form-select">
                            <option value="">Select Subcategory</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Price (₹)</label>
                        <input type="number" step="0.01" name="price[]" id="price_${rowId}" class="form-control" placeholder="Selling Price" oninput="calcDiscount('${rowId}')">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Original Price</label>
                        <input type="number" step="0.01" name="original_price[]" id="original_price_${rowId}" class="form-control" placeholder="MRP" oninput="calcDiscount('${rowId}')">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Discount (%)</label>
                        <input type="number" name="discount[]" id="discount_${rowId}" class="form-control" value="0" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description[]" class="form-control" rows="3" placeholder="Product description..."></textarea>
                    </div>

                    <!-- Variants: Sizes & Colors -->
                    <div class="col-12">
                        <button type="button" class="variant-toggle-btn" onclick="toggleVariants('${rowId}')">
                            <i class="fa-solid fa-sliders me-1"></i> + Add Sizes &amp; Colors (optional)
                        </button>
                        <div class="variant-panel variant-body" id="variantBody_${rowId}">
                            <!-- Hidden JSON fields -->
                            <input type="hidden" name="sizes_json[]" id="sizes_json_${rowId}" value="[]">
                            <input type="hidden" name="colors_json[]" id="colors_json_${rowId}" value="[]">

                            <!-- Sizes -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#a78bfa;">Sizes</label>
                                <div class="variant-add-row">
                                    <input type="text" id="sizeInput_${rowId}" class="form-control" placeholder="e.g. S, M, L, XL, 42, Free Size" style="font-size:13px;">
                                    <button type="button" class="variant-add-btn" onclick="addSize('${rowId}')"><i class="fa-solid fa-plus me-1"></i>Add</button>
                                </div>
                                <div class="variant-chip-wrap" id="sizeChips_${rowId}"></div>
                            </div>

                            <!-- Colors -->
                            <div>
                                <label class="form-label fw-semibold" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#a78bfa;">Colors</label>
                                <div class="variant-add-row">
                                    <input type="text" id="colorInput_${rowId}" class="form-control" placeholder="e.g. Red, #FF0000, Sky Blue" style="font-size:13px;">
                                    <input type="color" id="colorPicker_${rowId}" title="Pick color" style="width:42px;height:38px;border-radius:10px;border:1px solid #374151;background:#0b1220;cursor:pointer;padding:2px 4px;" value="#7c3aed">
                                    <button type="button" class="variant-add-btn" onclick="addColor('${rowId}')"><i class="fa-solid fa-plus me-1"></i>Add</button>
                                </div>
                                <div class="variant-chip-wrap" id="colorChips_${rowId}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: stock + 4 images -->
            <div class="col-lg-4">
                <div class="premium-panel p-3 rounded-4 h-100">
                    <h6 class="fw-bold mb-3">Visibility &amp; Stock</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Featured</label>
                        <select name="featured[]" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trending</label>
                        <select name="trending[]" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stock Qty</label>
                        <input type="number" name="stock_qty[]" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stock Status</label>
                        <select name="stock_status[]" class="form-select">
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>

                    <!-- 4 Image Slots -->
                    <label class="form-label fw-semibold mb-2">Product Images <small class="text-muted">(up to 4 views)</small></label>
                    <div class="img-grid">
                        ${[1,2,3,4].map(i => `
                        <div class="img-slot" id="slot_${rowId}_${i}">
                            <input type="file" name="images_${i}[]" accept=".jpg,.jpeg,.png,.webp"
                                onchange="previewSlot('${rowId}', ${i}, this)" ${i===1?'':''}> 
                            <img id="slotImg_${rowId}_${i}" alt="">
                            <div class="slot-icon"><i class="fa-solid fa-image"></i></div>
                            <div class="slot-label">View ${i}${i===1?' (Main)':''}</div>
                            <button type="button" class="img-remove" id="slotRm_${rowId}_${i}"
                                onclick="clearSlot('${rowId}',${i},event)">✕</button>
                        </div>`).join('')}
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    rowsWrap.insertAdjacentHTML('beforeend', html);

    // Size input enter key
    const si = document.getElementById('sizeInput_' + rowId);
    if (si) si.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addSize(rowId); }});
    const ci = document.getElementById('colorInput_' + rowId);
    if (ci) ci.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addColor(rowId); }});

    updateAddButtonState();
}

function removeProductRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) row.remove();
    const rows = document.querySelectorAll('.bulk-row');
    rows.forEach((r, idx) => {
        const t = r.querySelector('.row-title');
        if (t) t.textContent = `Product Row ${idx+1}`;
    });
    if (rows.length === 0) addProductRow();
    updateAddButtonState();
}

function loadSubcategories(rowId) {
    const cat = document.getElementById('category_' + rowId);
    const sub = document.getElementById('subcategory_' + rowId);
    const catId = cat ? cat.value : '';
    sub.innerHTML = "<option value=''>Loading...</option>";
    if (!catId) { sub.innerHTML = "<option value=''>Select Subcategory</option>"; return; }
    fetch("../actions/fetch_subcat.php?cat_id=" + encodeURIComponent(catId))
        .then(r => r.text()).then(d => { sub.innerHTML = d; })
        .catch(() => { sub.innerHTML = "<option value=''>Select Subcategory</option>"; });
}

function calcDiscount(rowId) {
    const price    = parseFloat(document.getElementById('price_' + rowId)?.value || 0);
    const original = parseFloat(document.getElementById('original_price_' + rowId)?.value || 0);
    let discount = 0;
    if (original > 0 && price > 0 && original > price) {
        discount = Math.round(((original - price) / original) * 100);
    }
    const d = document.getElementById('discount_' + rowId);
    if (d) d.value = discount;
}

/* ── 4-image slots ── */
function previewSlot(rowId, slotN, input) {
    const file = input.files && input.files[0];
    const img = document.getElementById('slotImg_' + rowId + '_' + slotN);
    const rm  = document.getElementById('slotRm_' + rowId + '_' + slotN);
    const slot = document.getElementById('slot_' + rowId + '_' + slotN);
    if (!file) { clearSlot(rowId, slotN); return; }
    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        img.style.display = 'block';
        slot.querySelector('.slot-icon').style.display = 'none';
        slot.querySelector('.slot-label').style.display = 'none';
        rm.style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function clearSlot(rowId, slotN, e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const img  = document.getElementById('slotImg_' + rowId + '_' + slotN);
    const rm   = document.getElementById('slotRm_' + rowId + '_' + slotN);
    const slot = document.getElementById('slot_' + rowId + '_' + slotN);
    if (!slot) return;
    const input = slot.querySelector('input[type=file]');
    if (input) {
        const fresh = input.cloneNode();
        fresh.value = '';
        fresh.onchange = function() { previewSlot(rowId, slotN, this); };
        input.parentNode.replaceChild(fresh, input);
    }
    img.src = ''; img.style.display = 'none';
    rm.style.display = 'none';
    slot.querySelector('.slot-icon').style.display = '';
    slot.querySelector('.slot-label').style.display = '';
}

/* ── Variant helpers ── */
function toggleVariants(rowId) {
    const body = document.getElementById('variantBody_' + rowId);
    const btn  = body?.previousElementSibling;
    if (!body) return;
    body.classList.toggle('open');
    if (btn) btn.innerHTML = body.classList.contains('open')
        ? '<i class="fa-solid fa-chevron-up me-1"></i> Hide Sizes &amp; Colors'
        : '<i class="fa-solid fa-sliders me-1"></i> + Add Sizes &amp; Colors (optional)';
}

function getSizes(rowId) {
    try { return JSON.parse(document.getElementById('sizes_json_' + rowId)?.value || '[]'); }
    catch { return []; }
}
function getColors(rowId) {
    try { return JSON.parse(document.getElementById('colors_json_' + rowId)?.value || '[]'); }
    catch { return []; }
}
function saveSizes(rowId, arr) {
    const el = document.getElementById('sizes_json_' + rowId);
    if (el) el.value = JSON.stringify(arr);
}
function saveColors(rowId, arr) {
    const el = document.getElementById('colors_json_' + rowId);
    if (el) el.value = JSON.stringify(arr);
}

function renderSizeChips(rowId) {
    const wrap = document.getElementById('sizeChips_' + rowId);
    if (!wrap) return;
    const sizes = getSizes(rowId);
    wrap.innerHTML = sizes.map((s, i) => `
        <span class="variant-chip">${escHtml(s)}
            <button type="button" class="chip-remove" onclick="removeSize('${rowId}',${i})">✕</button>
        </span>`).join('');
}
function renderColorChips(rowId) {
    const wrap = document.getElementById('colorChips_' + rowId);
    if (!wrap) return;
    const colors = getColors(rowId);
    wrap.innerHTML = colors.map((c, i) => `
        <span class="variant-chip">
            <span class="color-dot" style="background:${escHtml(c.hex || '#999')}"></span>
            ${escHtml(c.name)}
            <button type="button" class="chip-remove" onclick="removeColor('${rowId}',${i})">✕</button>
        </span>`).join('');
}

function addSize(rowId) {
    const input = document.getElementById('sizeInput_' + rowId);
    const val = (input?.value || '').trim();
    if (!val) return;
    const sizes = getSizes(rowId);
    if (!sizes.includes(val)) { sizes.push(val); saveSizes(rowId, sizes); renderSizeChips(rowId); }
    if (input) input.value = '';
}
function removeSize(rowId, idx) {
    const sizes = getSizes(rowId);
    sizes.splice(idx, 1);
    saveSizes(rowId, sizes);
    renderSizeChips(rowId);
}

function addColor(rowId) {
    const input  = document.getElementById('colorInput_' + rowId);
    const picker = document.getElementById('colorPicker_' + rowId);
    const name   = (input?.value || '').trim();
    const hex    = picker ? picker.value : '#000000';
    if (!name) return;
    const colors = getColors(rowId);
    if (!colors.find(c => c.name === name)) {
        colors.push({ name, hex });
        saveColors(rowId, colors);
        renderColorChips(rowId);
    }
    if (input) input.value = '';
}
function removeColor(rowId, idx) {
    const colors = getColors(rowId);
    colors.splice(idx, 1);
    saveColors(rowId, colors);
    renderColorChips(rowId);
}

function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('addRowBtn').addEventListener('click', addProductRow);
addProductRow();
updateAddButtonState();
</script>

<?php include '../includes/footer.php'; ?>
