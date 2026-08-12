<?php
if (!isset($_SESSION['email'])) {
    return;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$ticketQ = mysqli_query($conn, "SELECT * FROM support_tickets WHERE user_email='{$email}' ORDER BY id DESC LIMIT 4");
?>
<section id="support" class="content-section" style="display:none; background: radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 24%), radial-gradient(circle at top right, rgba(124,58,237,.10), transparent 22%), linear-gradient(180deg,#f8fbff 0%, #eef4ff 100%);">
    <div class="container py-5">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 28px; overflow:hidden;">
                    <div class="card-body p-4 p-lg-5 text-white" style="background: linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#7c3aed 100%);">
                        <span class="badge bg-white text-dark rounded-pill mb-3">24/7 Support</span>
                        <h2 class="fw-black mb-3">Contact Support</h2>
                        <p class="mb-4 text-white-50">Open a ticket for order issues, payment problems, delivery delays, address corrections, or account help.</p>

                        <div class="d-grid gap-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,255,255,.12);"><i class="fas fa-headset"></i></div>
                                <div>
                                    <div class="fw-bold">Fast response</div>
                                    <div class="small text-white-50">Messages stay organized in the admin panel.</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 align-items-start">
                                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,255,255,.12);"><i class="fas fa-shield-heart"></i></div>
                                <div>
                                    <div class="fw-bold">Premium handling</div>
                                    <div class="small text-white-50">Pending, replied, and closed status tracking.</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 align-items-start">
                                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,255,255,.12);"><i class="fas fa-bolt"></i></div>
                                <div>
                                    <div class="fw-bold">Quick ticketing</div>
                                    <div class="small text-white-50">Share the issue and submit in seconds.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                            <div class="small text-white-50">Support email</div>
                            <div class="fw-bold">support@quigly.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white shadow-lg h-100" style="border-radius:28px; overflow:hidden;">
                    <div class="p-4 border-bottom">
                        <span class="badge text-bg-primary rounded-pill mb-2">Raise a Ticket</span>
                        <h4 class="fw-bold mb-0">Tell us what happened</h4>
                    </div>
                    <div class="p-4">
                        <form method="POST" action="admin/actions/support_action.php">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject</label>
                                <input type="text" name="subject" class="form-control form-control-lg" placeholder="Order issue, refund request, account problem..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Message</label>
                                <textarea name="message" class="form-control" rows="8" placeholder="Add every important detail..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-3 fw-bold">Submit Ticket</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-0 shadow-lg h-100" style="border-radius:28px; overflow:hidden;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Recent Tickets</h5>
                        <?php if ($ticketQ && mysqli_num_rows($ticketQ) > 0): ?>
                            <div class="d-grid gap-3">
                                <?php while ($t = mysqli_fetch_assoc($ticketQ)): ?>
                                    <div class="p-3 rounded-4 border">
                                        <div class="fw-bold mb-1"><?= htmlspecialchars($t['subject']) ?></div>
                                        <div class="small text-muted mb-2"><?= htmlspecialchars($t['message']) ?></div>
                                        <span class="badge <?= strtolower($t['status'] ?? 'pending') === 'replied' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= htmlspecialchars($t['status'] ?? 'pending') ?>
                                        </span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border mb-0">No tickets yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
