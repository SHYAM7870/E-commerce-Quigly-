<section id="deals" class="content-section">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="section-title gradient-text">Today's Best Deals</h2>
            <div class="countdown-timer">
                <i class="fas fa-clock me-2"></i> Sale ends in:
                <span id="dealsCountdown">23:59:59</span>
            </div>
        </div>

        <!-- This will be filled by JavaScript with modern product cards -->
        <div id="dealsProducts" class="row g-4"></div>

        <!-- Bonus offer cards (keep if you like) -->
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <div class="offer-card">
                    <h5>Weekend Special</h5>
                    <p>Get 30% off on all headphones and speakers</p>
                    <div class="offer-code">WEEKEND30</div>
                    <small>Valid until Sunday</small>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="offer-card" style="background: var(--gradient-primary);">
                    <h5>Student Discount</h5>
                    <p>Students get 15% off on all products</p>
                    <div class="offer-code">STUDENT15</div>
                    <small>Valid with student ID</small>
                </div>
            </div>
        </div>
    </div>
</section>