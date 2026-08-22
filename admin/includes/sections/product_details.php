<section id="product_details" class="content-section py-5" style="display:none;">
  <div class="container product-detail-shell">
    <button class="btn detail-back-btn mb-4" onclick="showSection('products')"><i class="fas fa-arrow-left"></i> Back to Products</button>

    <div class="detail-hero-card">
      <div class="row g-4 g-lg-5 align-items-start">

        <!-- ── LEFT: Image Gallery ── -->
        <div class="col-lg-5">
          <div class="product-gallery-layout">
            <!-- Vertical Thumbnail List on Left -->
            <div class="thumb-strip-vertical" id="thumbStrip" style="display:none;">
              <!-- filled by JS -->
            </div>

            <!-- Main Image Showcase on Right -->
            <div class="main-image-showcase-container">
              <!-- Share Icon top right -->
              <button class="gallery-share-btn" onclick="shareProduct()" title="Share Product">
                <i class="fa-regular fa-share-from-square"></i>
              </button>

              <div class="premium-image-card" id="mainImageCard">
                <span id="detailDiscount" class="detail-discount d-none">-20%</span>
                <img id="detailImage" src="" alt="Product Image"
                  onload="this.style.opacity='1'"
                  onerror="this.onerror=null;this.src='assets/images/no-image.png'">
              </div>

              <!-- Click to see full view link -->
              <div class="full-view-link-container">
                <a href="#" onclick="openFullViewImage(event)" class="full-view-link">
                  Click to see full view
                </a>
              </div>
            </div>
          </div>

          <div class="detail-trust-row mt-3">
            <div class="trust-item"><i class="fas fa-lock"></i> Secure</div>
            <div class="trust-item"><i class="fas fa-undo"></i> 7-Day Return</div>
            <div class="trust-item"><i class="fas fa-headset"></i> 24/7 Support</div>
          </div>
        </div>

        <!-- ── RIGHT: Info ── -->
        <div class="col-lg-7">
          <div class="detail-info-card">
            <div class="top-head">
              <span id="detailCategory" class="detail-category">CATEGORY</span>
              <span class="stock-live" id="detailStockBadge">
                <i class="fas fa-circle" id="detailStockIcon"></i>
                <span id="detailStockText">In Stock</span>
              </span>
              <button id="detailStockBtn" class="btn btn-secondary d-none" disabled>Out of Stock</button>
            </div>

            <h1 id="detailTitle" class="detail-title"></h1>

            <div class="detail-rating">
              <div id="detailStars" class="stars"></div>
              <span id="detailRatingText" class="rating-text-sm"></span>
            </div>

            <div class="detail-price-row">
              <span id="detailPrice" class="detail-price"></span>
              <span id="detailOldPrice" class="detail-old-price"></span>
              <span id="detailSavings" class="detail-savings-badge d-none"></span>
            </div>

            <p id="detailDescription" class="detail-description"></p>

            <div class="detail-delivery-box">
              <div class="delivery-head">
                <div>
                  <span class="delivery-label">Estimated Delivery</span>
                  <h5 id="detailDeliveryText" class="delivery-text">Loading...</h5>
                </div>
                <div class="delivery-badge"><i class="fas fa-truck-fast"></i><span id="detailDeliveryCountdown">--</span></div>
              </div>
              <div class="delivery-bar">
                <div id="detailDeliveryProgress" class="delivery-bar-fill"></div>
              </div>
            </div>

            <div class="detail-highlight-grid">
              <div class="detail-highlight-item"><i class="fas fa-truck"></i><strong>Free Delivery</strong><span>Fast Shipping</span></div>
              <div class="detail-highlight-item"><i class="fas fa-shield-alt"></i><strong>Secure Payment</strong><span>100% Protected</span></div>
              <div class="detail-highlight-item"><i class="fas fa-undo"></i><strong>7 Day Return</strong><span>Easy Returns</span></div>
            </div>

            <!-- Quantity -->
            <div class="detail-qty-row">
              <span class="detail-qty-label">Quantity</span>
              <div class="detail-qty-control">
                <button type="button" class="qty-btn qty-minus" onclick="changeDetailQty(-1)"><i class="fas fa-minus"></i></button>
                <span class="qty-value" id="detailQtyValue">1</span>
                <button type="button" class="qty-btn qty-plus" onclick="changeDetailQty(1)"><i class="fas fa-plus"></i></button>
              </div>
              <span class="detail-qty-total" id="detailQtyTotal"></span>
            </div>

            <!-- Variant Selector -->
            <div id="variantSection" class="detail-variant-box" style="display:none;">
              <div class="variant-group" id="sizeGroup" style="display:none;">
                <span class="variant-label">Size <span id="selectedSizeLabel" class="variant-selected-label"></span></span>
                <div id="sizeContainer" class="variant-options"></div>
              </div>
              <div class="variant-group" id="colorGroup" style="display:none;">
                <span class="variant-label">Color <span id="selectedColorLabel" class="variant-selected-label"></span></span>
                <div id="colorContainer" class="variant-options"></div>
              </div>
              <div id="variantStock" class="variant-stock-text"></div>
            </div>

            <input type="hidden" id="selectedVariantId">
            <input type="hidden" id="selectedSizeId">
            <input type="hidden" id="selectedColorId">

            <div class="detail-actions" style="grid-template-columns:1fr auto 1fr;">
              <button id="detailAddBtn" class="btn detail-cart-btn"><i class="fas fa-cart-plus"></i> Add To Cart</button>
              <button id="detailWishBtn" class="btn detail-wish-btn" title="Save to Wishlist"><i class="far fa-heart"></i></button>
              <button id="detailBuyBtn" class="btn detail-buy-btn"><i class="fas fa-bolt"></i> Buy Now</button>
            </div>

            <div class="detail-meta">
              <div><span>Availability</span><strong id="detailAvailability">In Stock</strong></div>
              <div><span>Delivery</span><strong id="deliveryTime">Today Delivery</strong></div>
              <div><span>Support</span><strong>24/7 Help</strong></div>
            </div>

            <!-- Reviews -->
            <div class="reviews-box">
              <div class="review-head">
                <h4><i class="fas fa-star"></i> Customer Reviews</h4>
                <div class="verified-review"><i class="fas fa-check-circle"></i> Verified</div>
              </div>
              <div class="review-summary">
                <div class="review-left">
                  <div class="review-score">
                    <span id="reviewAvgRating">0.0</span>
                    <div id="reviewAvgStars"></div>
                  </div>
                  <div class="review-total">
                    <span id="reviewCountText">0 Reviews</span>
                    <small>Verified customer reviews</small>
                  </div>
                </div>
                <div class="review-right">
                  <div class="review-badge"><i class="fas fa-shield-alt"></i> Trusted Product</div>
                </div>
              </div>
              <div id="productReviewsList" class="premium-review-list">
                <div class="review-loader" style="text-align:center;padding:20px;color:#9ca3af;font-size:13px;">
                  <i class="fas fa-spinner fa-spin"></i> Loading reviews...
                </div>
              </div>
              <button id="reviewToggleBtn" type="button" class="review-toggle-btn d-none"></button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ── RELATED PRODUCTS ── -->
    <div class="related-products-section">
      <div class="related-products-head">
        <div>
          <span class="related-pill"><i class="fas fa-layer-group"></i> You May Also Like</span>
          <h3 class="related-title">More in <span id="relatedCategoryName" class="related-category-name">this Category</span></h3>
          <p class="related-subtitle">Hand-picked picks similar to what you're viewing</p>
        </div>
        <a href="#" class="related-viewall" onclick="event.preventDefault(); showSection('products'); window.scrollTo({top:0,behavior:'smooth'});">
          View All <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
      <div id="relatedProductsGrid" class="related-products-grid"></div>
      <div id="relatedProductsEmpty" class="related-products-empty d-none">
        <i class="fas fa-box-open"></i>
        <p>No related products found right now.</p>
      </div>
    </div>
  </div>

<style>
/* ═══════════════════════════════════════════════
   PRODUCT DETAILS — COMPLETE STYLES
═══════════════════════════════════════════════ */
#product_details {
  position: relative;
  padding: 2rem 0 2.5rem;
  overflow: hidden;
}
#product_details::before {
  content: "";
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 12% 8%, rgba(124,58,237,.10), transparent 28%),
    radial-gradient(circle at 86% 16%, rgba(59,130,246,.08), transparent 22%),
    radial-gradient(circle at 50% 85%, rgba(168,85,247,.07), transparent 24%);
  pointer-events: none;
}
.product-detail-shell {
  position: relative;
  z-index: 1;
  max-width: 1440px;
  margin: 0 auto;
}
.detail-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 999px;
  border: 1px solid rgba(124,58,237,.22);
  background: linear-gradient(135deg, rgba(124,58,237,.10), rgba(139,92,246,.08));
  color: #7c3aed;
  font-size: 13px;
  font-weight: 700;
  box-shadow: 0 10px 25px rgba(124,58,237,.08);
  backdrop-filter: blur(10px);
  transition: .25s;
}
.detail-back-btn:hover {
  transform: translateY(-1px);
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  color: #fff;
  box-shadow: 0 18px 34px rgba(124,58,237,.22);
}
.detail-hero-card {
  background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.88));
  border: 1px solid rgba(124,58,237,.10);
  border-radius: 30px;
  padding: 22px;
  box-shadow: 0 24px 70px rgba(15,23,42,.10);
  backdrop-filter: blur(18px);
}

/* ── Image Gallery Layout Overhaul ── */
.product-gallery-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.thumb-strip-vertical {
  display: flex;
  flex-direction: column;
  gap: 12px;
  flex-shrink: 0;
  max-height: 440px;
  overflow-y: auto;
  padding-right: 4px;
}
.thumb-strip-vertical::-webkit-scrollbar {
  width: 3px;
}
.thumb-strip-vertical::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 99px;
}
.thumb-btn-vertical {
  flex-shrink: 0;
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #cbd5e1;
  cursor: pointer;
  padding: 2px;
  background: #ffffff;
  transition: .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.thumb-btn-vertical img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  display: block;
}
.thumb-btn-vertical:hover {
  border-color: #0b57d0;
}
.thumb-btn-vertical.active {
  border-color: #0b57d0;
  box-shadow: 0 0 0 1px #0b57d0;
}
.thumb-btn-vertical.more-indicator::after {
  content: attr(data-more);
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.82);
  color: #1e293b;
  font-weight: 700;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
}
body.dark-mode .thumb-btn-vertical.more-indicator::after {
  background: rgba(15, 23, 42, 0.85);
  color: #f1f5f9;
}

/* Main Image Showcase on Right */
.main-image-showcase-container {
  flex-grow: 1;
  position: relative;
}
.gallery-share-btn {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  color: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  z-index: 10;
  transition: all 0.2s;
}
.gallery-share-btn:hover {
  background: #f8fafc;
  transform: scale(1.04);
}
body.dark-mode .gallery-share-btn {
  background: #1f2937;
  border-color: #374151;
  color: #f3f4f6;
}
body.dark-mode .gallery-share-btn:hover {
  background: #374151;
}

.premium-image-card {
  position: relative;
  min-height: 440px;
  overflow: hidden;
  border-radius: 12px;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  transition: .3s;
}
.premium-image-card img {
  position: relative;
  z-index: 1;
  max-width: 100%;
  max-height: 360px;
  width: auto;
  height: auto;
  object-fit: contain;
  transition: opacity .25s, transform .3s;
}
.premium-image-card img.switching {
  opacity: 0 !important;
  transform: scale(.96);
}

/* Full View link */
.full-view-link-container {
  text-align: center;
  margin-top: 14px;
}
.full-view-link {
  color: #0b57d0;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}
.full-view-link:hover {
  color: #002d84;
  text-decoration: underline;
}
body.dark-mode .full-view-link {
  color: #3b82f6;
}
body.dark-mode .full-view-link:hover {
  color: #60a5fa;
}

/* ── Detail right side ── */
.detail-discount {
  position: absolute;
  top: 14px; left: 14px;
  z-index: 3;
  padding: 6px 12px;
  border-radius: 6px;
  background: #ef4444;
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
.detail-trust-row {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}
.trust-item {
  flex: 1;
  text-align: center;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  transition: border-color 0.2s;
}
.trust-item:hover {
  border-color: #cbd5e1;
}
.trust-item i { font-size: 16px; color: #7c3aed; }
body.dark-mode .trust-item {
  background: #1f2937;
  border-color: #374151;
  color: #9ca3af;
}

.detail-info-card {
  height: 100%;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 32px;
}
body.dark-mode .detail-info-card {
  background: #1f2937;
  border-color: #374151;
}
.top-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.detail-category {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  color: #7c3aed;
  background: rgba(124,58,237,.06);
  border: 1px solid rgba(124,58,237,.15);
  text-transform: uppercase;
}
.stock-live {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  color: #059669;
  background: rgba(16,185,129,.08);
  border: 1px solid rgba(16,185,129,.15);
}
.stock-live i { font-size: 8px; color: #10b981; animation: pulseDot 1.6s ease-in-out infinite; }
@keyframes pulseDot { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.25);opacity:.6} }

.detail-title {
  font-size: 26px;
  line-height: 1.2;
  letter-spacing: -0.5px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 12px;
}
body.dark-mode .detail-title {
  color: #f1f5f9;
}
.detail-rating { display:flex; align-items:center; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
.stars { display:inline-flex; gap:3px; font-size:14px; color:#f59e0b; }
.rating-text-sm { font-size:13px; color:#64748b; font-weight:600; }
body.dark-mode .rating-text-sm { color: #94a3b8; }

.detail-price-row { display:flex; align-items:baseline; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
.detail-price { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; color: #7c3aed; }
.detail-old-price { font-size: 15px; font-weight: 600; color: #94a3b8; text-decoration: line-through; }
.detail-savings-badge {
  font-size: 11px; font-weight: 700;
  background: rgba(16,185,129,.08); color: #059669;
  padding: 5px 10px; border-radius: 6px;
  border: 1px solid rgba(16,185,129,.15);
}
.detail-description { margin: 0 0 20px; color: #475569; font-size: 14.5px; line-height: 1.7; }
body.dark-mode .detail-description { color: #cbd5e1; }

.detail-delivery-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
}
body.dark-mode .detail-delivery-box {
  background: #1e293b;
  border-color: #334155;
}
.delivery-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
.delivery-label { display:block; font-size:10px; font-weight:700; color:#64748b; letter-spacing:.5px; text-transform:uppercase; margin-bottom:3px; }
body.dark-mode .delivery-label { color: #94a3b8; }
.delivery-text { margin:0; font-size:14px; font-weight:700; color:#0f172a; }
body.dark-mode .delivery-text { color: #f1f5f9; }
.delivery-badge {
  display:inline-flex; align-items:center; gap:6px; padding:6px 12px;
  border-radius:6px; font-size:11px; font-weight:700; color:#7c3aed;
  background:rgba(124,58,237,.06); border:1px solid rgba(124,58,237,.15); white-space:nowrap;
}
.delivery-bar { width:100%; height:6px; border-radius:99px; overflow:hidden; background:#e2e8f0; }
body.dark-mode .delivery-bar { background: #334155; }
.delivery-bar-fill { width:0; height:100%; border-radius:99px; background:linear-gradient(90deg,#7c3aed,#4f46e5); }

.detail-highlight-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:20px; }
.detail-highlight-item {
  background: #ffffff;
  border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 8px;
  text-align: center; transition: border-color 0.2s;
}
.detail-highlight-item:hover {
  border-color: #cbd5e1;
}
body.dark-mode .detail-highlight-item {
  background: #1f2937;
  border-color: #374151;
}
.detail-highlight-item i { font-size: 16px; color: #7c3aed; margin-bottom: 6px; display: block; }
.detail-highlight-item strong { display: block; color: #0f172a; font-size: 12px; font-weight: 700; margin-bottom: 2px; }
body.dark-mode .detail-highlight-item strong { color: #f1f5f9; }
.detail-highlight-item span { display: block; color: #64748b; font-size: 10px; font-weight: 500; }
body.dark-mode .detail-highlight-item span { color: #94a3b8; }

.detail-qty-row {
  display: flex; align-items: center; gap: 16px; margin-bottom: 20px;
  padding: 12px 16px; border-radius: 12px;
  background: #f8fafc; border: 1px solid #e2e8f0;
}
body.dark-mode .detail-qty-row {
  background: #1e293b;
  border-color: #334155;
}
.detail-qty-label { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; min-width: 64px; }
body.dark-mode .detail-qty-label { color: #94a3b8; }
.detail-qty-control {
  display: inline-flex; align-items: center; border-radius: 8px; overflow: hidden;
  border: 1px solid #cbd5e1; background: #ffffff;
}
body.dark-mode .detail-qty-control {
  border-color: #4b5563;
  background: #1f2937;
}
.qty-btn {
  width: 36px; height: 36px; border: none; background: transparent; color: #475569;
  font-size: 12px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; transition: background-color 0.2s;
}
body.dark-mode .qty-btn { color: #cbd5e1; }
.qty-btn:hover { background-color: #f1f5f9; }
body.dark-mode .qty-btn:hover { background-color: #374151; }
.qty-value {
  min-width: 44px; height: 36px; display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: #0f172a;
  border-left: 1px solid #cbd5e1; border-right: 1px solid #cbd5e1;
  user-select: none;
}
body.dark-mode .qty-value {
  color: #f1f5f9;
  border-color: #4b5563;
}
.detail-qty-total { font-size: 13px; font-weight: 700; color: #7c3aed; margin-left: auto; }

/* ── Variant section ── */
.detail-variant-box {
  margin-bottom: 20px;
  padding: 16px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #ffffff;
}
body.dark-mode .detail-variant-box {
  background: #1f2937;
  border-color: #374151;
}
.variant-group { margin-bottom: 12px; }
.variant-group:last-of-type { margin-bottom: 0; }
.variant-label {
  display: block;
  font-size: 11px; font-weight: 700; color: #475569;
  text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
}
body.dark-mode .variant-label { color: #cbd5e1; }
.variant-selected-label {
  font-size: 11px; font-weight: 700; color: #7c3aed;
  text-transform: none; letter-spacing: 0;
}
.variant-options { display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
.variant-option-btn {
  border: 1px solid #cbd5e1; background: #ffffff; padding: 6px 14px;
  border-radius: 6px; cursor: pointer; transition: all 0.15s; font-size: 13px; font-weight: 600;
  color: #334155; min-width: 44px; text-align: center;
}
body.dark-mode .variant-option-btn {
  background: #1f2937;
  border-color: #4b5563;
  color: #cbd5e1;
}
.variant-option-btn:hover { border-color: #7c3aed; color: #7c3aed; }
.variant-option-btn.active { border-color: #7c3aed; background: #7c3aed; color: #fff; font-weight: 700; }
.color-option-btn {
  border: 2px solid #fff !important; padding: 0 !important;
  box-shadow: 0 0 0 1px #cbd5e1 !important;
  transition: transform 0.15s !important; position: relative; border-radius: 50% !important;
  width: 32px; height: 32px; flex-shrink: 0;
}
body.dark-mode .color-option-btn {
  border-color: #1f2937 !important;
  box-shadow: 0 0 0 1px #4b5563 !important;
}
.color-option-btn:hover { transform: scale(1.1); }
.color-option-btn.active { box-shadow: 0 0 0 2px #7c3aed !important; transform: scale(1.05); }
.color-option-btn.active::after { content:""; position:absolute; inset:2px; border-radius:50%; border:2px solid #fff; }
.color-swatch-wrap { display:flex; flex-direction:column; align-items:center; gap:4px; }
.color-swatch-label { font-size:10px; font-weight:600; color:#64748b; max-width:60px; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.variant-stock-text { margin-top:10px; font-size:13px; font-weight:700; min-height:18px; }

/* ── Actions ── */
.detail-actions { display:grid; grid-template-columns: 1fr auto 1fr; gap:12px; margin-bottom:20px; }
.detail-cart-btn, .detail-buy-btn {
  min-height: 48px; border-radius: 8px; font-size: 14px; font-weight: 700;
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  transition: 0.2s; border: 1px solid transparent;
}
.detail-cart-btn { background: #ffffff; color: #0f172a; border-color: #cbd5e1; }
body.dark-mode .detail-cart-btn { background: #1e293b; color: #f1f5f9; border-color: #475569; }
.detail-cart-btn:hover { background: #f8fafc; border-color: #7c3aed; color: #7c3aed; }
body.dark-mode .detail-cart-btn:hover { background: #334155; }
.detail-buy-btn { background: #7c3aed; color: #fff; }
.detail-buy-btn:hover { background: #6d28d9; color: #fff; }
.detail-wish-btn {
  min-height: 48px; width: 48px; border-radius: 8px; font-size: 16px;
  display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;
  border: 1px solid #fecaca; background: #fef2f2; color: #ef4444;
  flex-shrink: 0;
}
body.dark-mode .detail-wish-btn {
  background: rgba(239,68,68,0.1);
  border-color: rgba(239,68,68,0.2);
}
.detail-wish-btn:hover, .detail-wish-btn.wishlisted { background: #ef4444; border-color: #ef4444; color: #fff; }
.detail-wish-btn.wishlisted i::before { content:"\f004"; font-family:"Font Awesome 6 Free"; font-weight:900; }

/* ── Meta ── */
.detail-meta {
  display: flex; gap: 16px; justify-content: space-between;
  margin-bottom: 20px; padding: 16px 0; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .detail-meta { border-color: #334155; }
.detail-meta div { display: flex; flex-direction: column; gap: 4px; }
.detail-meta span { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }
.detail-meta strong { font-size: 13px; font-weight: 700; color: #334155; }
body.dark-mode .detail-meta strong { color: #cbd5e1; }

/* ── Reviews ── */
.reviews-box {
  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(248,250,252,.90));
  border:1px solid rgba(124,58,237,.10); border-radius:22px;
  padding:18px; box-shadow:0 12px 28px rgba(15,23,42,.05);
}
.review-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.review-head h4 { margin:0; font-size:18px; font-weight:900; color:#0f172a; display:inline-flex; align-items:center; gap:10px; }
.review-head h4 i { color:#f59e0b; }
.verified-review,.review-badge { display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:9px 14px; font-size:12px; font-weight:800; white-space:nowrap; }
.verified-review { color:#059669; background:rgba(16,185,129,.10); border:1px solid rgba(16,185,129,.16); }
.review-summary { display:flex; align-items:stretch; justify-content:space-between; gap:16px; padding:16px; margin-bottom:14px; border-radius:18px; background:rgba(124,58,237,.04); border:1px solid rgba(124,58,237,.08); }
.review-left { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
.review-score { display:flex; align-items:center; gap:12px; }
#reviewAvgRating { font-size:40px; line-height:1; font-weight:900; letter-spacing:-.05em; color:#0f172a; }
#reviewAvgStars { display:inline-flex; gap:4px; color:#f59e0b; font-size:15px; }
.review-total { display:flex; flex-direction:column; gap:3px; }
.review-total span { font-size:15px; font-weight:800; color:#0f172a; }
.review-total small { color:#64748b; font-size:12px; font-weight:600; }
.review-right { display:flex; align-items:center; justify-content:center; }
.review-badge { color:#7c3aed; background:rgba(124,58,237,.10); border:1px solid rgba(124,58,237,.16); }
.premium-review-list { border-top:1px solid rgba(148,163,184,.16); padding-top:16px; }
.review-card { padding:14px 0; border-bottom:1px solid rgba(148,163,184,.14); }
.review-card:last-child { border-bottom:0; }
.review-card-top { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:6px; }
.review-user { font-weight:900; color:#0f172a; }
.review-date { color:#64748b; font-size:.86rem; }
.review-rating { color:#f59e0b; font-size:.92rem; margin-bottom:8px; }
.review-text { color:#475569; line-height:1.75; margin:0; }
.reviews-empty { padding:16px; border-radius:16px; background:#f8fafc; border:1px dashed rgba(148,163,184,.35); color:#64748b; text-align:center; }
.review-collapsed { display:none !important; }

.review-toggle-btn {
  margin-top:12px; width:100%;
  border:1px solid rgba(124,58,237,.18); background:rgba(124,58,237,.06);
  color:#7c3aed; border-radius:14px; padding:12px 14px; font-weight:800; transition:.25s;
  display:flex; align-items:center; justify-content:center; gap:8px;
}
.review-toggle-btn:hover { background:rgba(124,58,237,.12); }

/* Rating bars */
.review-breakdown { padding:12px 0 4px; }
.rb-row { display:flex; align-items:center; gap:10px; margin-bottom:7px; font-size:13px; }
.rb-label { flex-shrink:0; width:36px; font-weight:700; color:#0f172a; font-size:12px; }
.rb-bar { flex:1; height:8px; border-radius:999px; background:rgba(148,163,184,.18); overflow:hidden; }
.rb-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,#f59e0b,#ef4444); transition:width .5s ease; }
.rb-count { flex-shrink:0; width:22px; text-align:right; font-weight:700; color:#64748b; font-size:12px; }

.review-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#7c3aed,#2563eb); color:#fff; font-size:14px; font-weight:900; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* ── Related Products ── */
.related-products-section {
  position: relative;
  margin-top: 42px;
  padding: 26px 26px 30px;
  border-radius: 28px;
  background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.88));
  border: 1px solid rgba(124,58,237,.10);
  box-shadow: 0 20px 50px rgba(15,23,42,.06);
  backdrop-filter: blur(18px);
}
.related-products-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 22px;
}
.related-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: .4px;
  text-transform: uppercase;
  color: #7c3aed;
  background: rgba(124,58,237,.10);
  border: 1px solid rgba(124,58,237,.16);
  margin-bottom: 10px;
}
.related-title {
  font-size: 1.6rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0 0 4px;
  letter-spacing: -.01em;
}
.related-category-name { color: #7c3aed; text-transform: capitalize; }
.related-subtitle { margin: 0; color: #64748b; font-size: 13.5px; font-weight: 600; }
.related-viewall {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 11px 20px;
  border-radius: 999px;
  font-weight: 800;
  font-size: 13.5px;
  color: #7c3aed;
  background: linear-gradient(135deg, rgba(124,58,237,.10), rgba(139,92,246,.08));
  border: 1px solid rgba(124,58,237,.20);
  text-decoration: none;
  white-space: nowrap;
  transition: .25s;
  box-shadow: 0 10px 25px rgba(124,58,237,.08);
  flex-shrink: 0;
}
.related-viewall:hover {
  color: #fff;
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  box-shadow: 0 18px 34px rgba(124,58,237,.22);
  transform: translateY(-1px);
}

/* Grid of related products */
.related-products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 22px;
}
.related-products-grid .product-card {
  height: 100%;
  display: flex;
  flex-direction: column;
}
.related-products-grid .product-img { height: 190px; }
.related-products-empty {
  text-align: center;
  padding: 48px 20px;
  border-radius: 22px;
  background: rgba(255,255,255,.6);
  border: 1px dashed rgba(148,163,184,.35);
  color: #94a3b8;
}
.related-products-empty i { font-size: 34px; margin-bottom: 10px; display: block; opacity: .6; }
.related-products-empty p { margin: 0; font-weight: 700; }

/* ── Dark mode ── */
body.dark-mode .detail-hero-card,
body.dark-mode .detail-info-card,
body.dark-mode .reviews-box,
body.dark-mode .detail-highlight-item,
body.dark-mode .detail-delivery-box { background:rgba(17,24,39,.88); border-color:rgba(148,163,184,.16); color:#f9fafb; }
body.dark-mode .detail-title,
body.dark-mode .detail-meta strong,
body.dark-mode .review-head h4,
body.dark-mode .review-total span,
body.dark-mode #reviewAvgRating { color:#f9fafb; }
body.dark-mode .detail-description,
body.dark-mode .rating-text-sm,
body.dark-mode .review-total small,
body.dark-mode .detail-highlight-item span,
body.dark-mode .detail-meta span { color:#cbd5e1; }
body.dark-mode .detail-cart-btn { background:rgba(15,23,42,.78); color:#f9fafb; border-color:rgba(148,163,184,.2); }
body.dark-mode .detail-buy-btn { background:linear-gradient(135deg,#8b5cf6,#7c3aed); }
body.dark-mode .premium-image-card { background:linear-gradient(180deg,rgba(124,58,237,.18),rgba(17,24,39,.84)),linear-gradient(135deg,#111827,#1f2937); }
body.dark-mode .trust-item { background:rgba(17,24,39,.82); color:#cbd5e1; border-color:rgba(148,163,184,.14); }
body.dark-mode .delivery-text,.body.dark-mode .detail-meta strong { color:#f9fafb; }
body.dark-mode .detail-qty-row { background:rgba(124,58,237,.10); border-color:rgba(124,58,237,.20); }
body.dark-mode .qty-btn,.body.dark-mode .qty-value { background:#1f2937; color:#c4b5fd; }
body.dark-mode .detail-qty-label { color:#f9fafb; }
body.dark-mode .detail-wish-btn { background:rgba(239,68,68,.10); border-color:rgba(239,68,68,.20); }
body.dark-mode .detail-variant-box { background:rgba(124,58,237,.08); border-color:rgba(124,58,237,.20); }
body.dark-mode .variant-option-btn { background:#1f2937; border-color:#374151; color:#e2e8f0; }
body.dark-mode .variant-option-btn.active { background:linear-gradient(135deg,#7c3aed,#8b5cf6); color:#fff; }
body.dark-mode .variant-label { color:#f1f5f9; }
body.dark-mode .rb-label,.body.dark-mode .review-user { color:#f9fafb; }
body.dark-mode .thumb-btn { background:rgba(124,58,237,.12); border-color:rgba(124,58,237,.20); }
body.dark-mode .reviews-empty { background:#1f2937; color:#9ca3af; }
body.dark-mode .related-title { color:#f9fafb; }
body.dark-mode .related-subtitle { color:#cbd5e1; }
body.dark-mode .related-products-section { background:rgba(17,24,39,.88); border-color:rgba(148,163,184,.16); }
body.dark-mode .related-products-empty { background:rgba(17,24,39,.82); border-color:rgba(148,163,184,.2); color:#9ca3af; }

/* ══════════════════════════════════════════════
   TABLET — ≤ 991px
══════════════════════════════════════════════ */
@media (max-width:991.98px) {
  .detail-hero-card { padding:18px; border-radius:24px; }
  .premium-image-card { min-height:360px; }
  .detail-highlight-grid { grid-template-columns:repeat(3,1fr); }
  .detail-actions { grid-template-columns:1fr auto 1fr; }
  .detail-meta { grid-template-columns:repeat(3,1fr); }
  .review-summary { flex-direction:column; }
  .delivery-head { flex-direction:column; align-items:flex-start; }
  .related-products-section { padding:22px 18px 26px; border-radius:24px; }
  .related-products-grid { grid-template-columns:repeat(auto-fill, minmax(190px,1fr)); gap:16px; }
  .related-products-grid .product-img { height:170px; }
  .related-title { font-size:1.35rem; }
}

/* ══════════════════════════════════════════════
   PHONE — ≤ 767px  (full premium overhaul)
══════════════════════════════════════════════ */
@media (max-width:767.98px) {

  /* ─ Page shell ─ */
  #product_details { padding:1rem 0 1.5rem; }
  .product-detail-shell { padding:0 12px; }

  /* ─ Back button ─ */
  .detail-back-btn {
    margin-bottom: 14px;
    padding: 9px 16px;
    font-size: 12.5px;
  }

  /* ─ Hero card: flat, edge-to-edge ─ */
  .detail-hero-card {
    padding: 14px;
    border-radius: 22px;
    box-shadow: 0 12px 36px rgba(15,23,42,.09);
  }

  /* ─ Image panel ─ */
  .premium-image-card {
    min-height: 260px;
    max-height: 300px;
    border-radius: 18px;
    padding: 14px;
  }
  .premium-image-card img {
    max-height: 240px;
    filter: drop-shadow(0 16px 28px rgba(15,23,42,.18));
  }

  /* ─ Thumbnails ─ */
  .thumb-strip { margin-top: 10px; gap: 7px; }
  .thumb-btn { width: 52px; height: 52px; border-radius: 10px; }

  /* ─ Trust row under image ─ */
  .detail-trust-row { gap: 7px; margin-top: 10px; }
  .trust-item {
    padding: 9px 6px;
    border-radius: 12px;
    font-size: 10px;
    gap: 5px;
  }
  .trust-item i { font-size: 13px; }

  /* ─ Info card ─ */
  .detail-info-card {
    padding: 16px 14px;
    border-radius: 20px;
    margin-top: 12px;
    box-shadow: 0 10px 30px rgba(124,58,237,.08);
  }

  /* ─ Top head: category + stock pill ─ */
  .top-head { gap: 8px; margin-bottom: 10px; }
  .detail-category { font-size: 10px; padding: 6px 11px; letter-spacing: .9px; }
  .stock-live { font-size: 11px; padding: 6px 11px; }

  /* ─ Title ─ */
  .detail-title {
    font-size: 1.55rem;
    letter-spacing: -.03em;
    line-height: 1.1;
    margin-bottom: 8px;
  }

  /* ─ Rating row ─ */
  .detail-rating { gap: 8px; margin-bottom: 10px; }
  .stars { font-size: 13px; }
  .rating-text-sm { font-size: 12px; }

  /* ─ Price row ─ */
  .detail-price-row { gap: 10px; margin-bottom: 12px; }
  .detail-price { font-size: 1.75rem; }
  .detail-old-price { font-size: 0.9rem; }
  .detail-savings-badge { font-size: 11px; padding: 5px 11px; }

  /* ─ Description ─ */
  .detail-description {
    font-size: 13.5px;
    line-height: 1.7;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* ─ Delivery box ─ */
  .detail-delivery-box { padding: 13px; border-radius: 16px; margin-bottom: 12px; }
  .delivery-head { flex-direction: row; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
  .delivery-label { font-size: 10px; margin-bottom: 3px; }
  .delivery-text { font-size: 14px; }
  .delivery-badge { padding: 7px 10px; font-size: 11px; gap: 6px; }

  /* ─ Highlights: 3 across on phone ─ */
  .detail-highlight-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 12px;
  }
  .detail-highlight-item {
    padding: 11px 6px;
    border-radius: 14px;
  }
  .detail-highlight-item i { font-size: 15px; margin-bottom: 4px; }
  .detail-highlight-item strong { font-size: 11px; margin-bottom: 2px; }
  .detail-highlight-item span { font-size: 9.5px; }

  /* ─ Qty row ─ */
  .detail-qty-row {
    padding: 10px 13px;
    border-radius: 14px;
    gap: 12px;
    margin-bottom: 12px;
  }
  .detail-qty-label { font-size: 11px; min-width: 54px; }
  .qty-btn { width: 36px; height: 36px; font-size: 12px; }
  .qty-value { min-width: 44px; height: 36px; font-size: 14px; }
  .detail-qty-total { font-size: 12px; }

  /* ─ Variant section ─ */
  .detail-variant-box { padding: 13px; border-radius: 14px; margin-bottom: 12px; }
  .variant-label { font-size: 11px; margin-bottom: 8px; }
  .variant-option-btn { padding: 6px 13px; font-size: 12px; border-radius: 9px; }
  .color-option-btn { width: 34px !important; height: 34px !important; }

  /* ─ Action buttons: 3-col grid ─ */
  .detail-actions {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 10px;
    margin-bottom: 14px;
  }
  .detail-cart-btn, .detail-buy-btn {
    min-height: 50px;
    font-size: 14px;
    border-radius: 14px;
    gap: 8px;
  }
  .detail-wish-btn {
    min-height: 50px;
    width: 50px;
    border-radius: 14px;
    font-size: 17px;
  }

  /* ─ Meta row: 3-col ─ */
  .detail-meta {
    grid-template-columns: repeat(3,1fr);
    gap: 8px;
    padding-top: 12px;
    margin-bottom: 14px;
  }
  .detail-meta div { padding: 10px 8px; border-radius: 12px; gap: 4px; }
  .detail-meta span { font-size: 9.5px; }
  .detail-meta strong { font-size: 11.5px; }

  /* ─ Reviews box ─ */
  .reviews-box { padding: 14px; border-radius: 18px; }
  .review-head { margin-bottom: 12px; }
  .review-head h4 { font-size: 15px; gap: 8px; }
  .verified-review { font-size: 11px; padding: 7px 11px; }
  .review-summary { padding: 12px; gap: 12px; border-radius: 14px; margin-bottom: 12px; }
  #reviewAvgRating { font-size: 34px; }
  #reviewAvgStars { font-size: 13px; }
  .review-total span { font-size: 13px; }
  .review-total small { font-size: 11px; }
  .review-badge { font-size: 11px; padding: 7px 11px; }
  .review-card { padding: 12px 0; }
  .review-user { font-size: 13.5px; }
  .review-date { font-size: 11.5px; }
  .review-rating { font-size: 12px; }
  .review-text { font-size: 13px; line-height: 1.65; }
  .review-avatar { width: 32px; height: 32px; font-size: 12px; }
  .review-toggle-btn { padding: 10px 12px; font-size: 13px; }

  /* ─ Related products: horizontal carousel ─ */
  .related-products-section {
    margin-top: 16px;
    padding: 18px 0 22px;
    border-radius: 20px;
  }
  .related-products-head {
    padding: 0 14px;
    margin-bottom: 14px;
    align-items: flex-start;
    gap: 10px;
  }
  .related-pill { font-size: 11px; padding: 5px 12px; }
  .related-title { font-size: 1.18rem; margin-bottom: 2px; }
  .related-subtitle { font-size: 12px; }
  .related-viewall { padding: 9px 15px; font-size: 12.5px; }

  .related-products-grid {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    gap: 12px;
    padding: 4px 14px 8px;
  }
  .related-products-grid::-webkit-scrollbar { display: none; }

  /* Related card: 58% wide, peek of next card */
  .related-products-grid .product-card {
    flex: 0 0 auto;
    width: 58%;
    max-width: 220px;
    scroll-snap-align: start;
    border-radius: 16px;
  }
  .related-products-grid .product-img { height: 150px; }
  .related-products-grid .product-card .product-body { padding: 10px 10px 8px; gap: 5px; }
  .related-products-grid .product-card .product-title { font-size: 0.82rem; }
  .related-products-grid .product-card .current-price { font-size: 1rem; }
  .related-products-grid .product-card .tags-group { display: none; }

  /* Related card action buttons: always side-by-side */
  .related-products-grid .product-card .product-actions {
    flex-direction: row !important;
    gap: 6px;
    padding-top: 8px;
  }
  .related-products-grid .product-card .product-action-btn {
    flex: 1;
    height: 36px;
    font-size: 0.74rem;
    border-radius: 10px;
    width: auto !important;
    gap: 4px;
  }

  .related-products-empty { margin: 0 14px; }
}

/* ══════════════════════════════════════════════
   SMALL PHONE — ≤ 420px
══════════════════════════════════════════════ */
@media (max-width:420px) {
  .product-detail-shell { padding: 0 10px; }
  .detail-hero-card { padding: 12px; border-radius: 18px; }
  .premium-image-card { min-height: 230px; max-height: 270px; padding: 12px; border-radius: 16px; }
  .premium-image-card img { max-height: 210px; }

  .detail-title { font-size: 1.35rem; }
  .detail-price { font-size: 1.55rem; }

  .detail-highlight-grid { gap: 6px; }
  .detail-highlight-item { padding: 9px 4px; }
  .detail-highlight-item i { font-size: 13px; }
  .detail-highlight-item strong { font-size: 10px; }
  .detail-highlight-item span { font-size: 9px; }

  .detail-cart-btn, .detail-buy-btn { min-height: 46px; font-size: 13px; }
  .detail-wish-btn { min-height: 46px; width: 46px; font-size: 16px; }

  .detail-meta { gap: 6px; }
  .detail-meta div { padding: 9px 6px; }
  .detail-meta span { font-size: 9px; }
  .detail-meta strong { font-size: 11px; }

  .related-products-grid .product-card { width: 68%; }
  .related-products-grid .product-img { height: 140px; }
}
</style>
</section>