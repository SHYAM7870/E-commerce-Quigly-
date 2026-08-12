<section id="cart" class="content-section" style="display:none;">
    <div class="container py-5 cart-page">
        <div class="cart-header mb-4">
            <h2 class="section-title mb-2">Your Shopping Cart</h2>
            <p class="cart-subtitle">Review your selected items before checkout.</p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div id="cartItems" class="cart-items-list"></div>

                <div id="emptyCartMessage" class="cart-empty-state" style="display:none;">
                    <i class="fas fa-shopping-cart"></i>
                    <h4>Your cart is empty</h4>
                    <p>Add products from the catalog to see them here.</p>
                    <button class="btn btn-quigly px-4 py-2" onclick="showSection('products')">
                        View Products <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h4 class="summary-title">Order Summary</h4>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span id="subtotalAmount">₹0.00</span>
                    </div>

                    <div class="summary-line">
                        <span>Discount</span>
                        <span id="discountAmount">-₹0.00</span>
                    </div>

                    <div class="summary-line">
                        <span>Shipping</span>
                        <span id="shippingAmount">₹5.00</span>
                    </div>

                    <div class="summary-line">
                        <span>Tax</span>
                        <span id="taxAmount">₹0.00</span>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-total">
                        <span>Total</span>
                        <span id="totalAmount">₹0.00</span>
                    </div>

                    <button class="btn btn-quigly w-100 mt-3 py-3" id="checkoutBtn" onclick="checkout()">
                        Proceed to Checkout
                    </button>

                    <button class="btn btn-outline-secondary w-100 mt-2 py-3" onclick="showSection('products')">
                        Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>