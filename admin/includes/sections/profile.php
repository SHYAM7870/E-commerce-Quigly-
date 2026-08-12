<section id="profile" class="content-section" style="display:none;">
<?php
$profileImage = !empty($data['image'] ?? '') ? 'upload/' . htmlspecialchars($data['image']) : 'assets/images/profile.jpg';
$profileName = htmlspecialchars($data['name'] ?? 'User');
$profileEmail = htmlspecialchars($data['email'] ?? 'NA');
$profilePhone = htmlspecialchars($data['number'] ?? 'NA');
$profileId = (int)($data['id'] ?? 0);
?>

<style>
    .profile-wrap {
        padding: 2rem 0;
    }

    .profile-hero {
        position: relative;
        overflow: hidden;
        border-radius: 32px;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #7c3aed 100%);
        padding: 2rem;
        color: #fff;
        box-shadow: 0 25px 70px rgba(15, 23, 42, .22);
        margin-bottom: 1.5rem;
    }

    .profile-hero::before {
        content: "";
        position: absolute;
        inset: auto -120px -120px auto;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
        filter: blur(10px);
    }

    .profile-hero::after {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 26px;
        pointer-events: none;
    }

    .profile-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .profile-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        line-height: 1.05;
    }

    .profile-subtitle {
        margin: .7rem 0 0;
        max-width: 650px;
        color: rgba(255, 255, 255, .78);
        font-size: .98rem;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        margin-top: 1rem;
        padding: .65rem 1rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        color: #fff;
        font-weight: 800;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .16);
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 1.5rem;
        align-items: stretch;
    }

    .profile-card,
    .detail-card {
        background: rgba(255, 255, 255, .95);
        border: 1px solid #eef2f7;
        border-radius: 28px;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .profile-card {
        padding: 1.5rem;
        position: relative;
    }

    .avatar-wrap {
        position: relative;
        width: 138px;
        height: 138px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        padding: 6px;
        background: linear-gradient(135deg, #7c3aed, #a855f7, #22c55e);
        box-shadow: 0 18px 38px rgba(124, 58, 237, .22);
    }

    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        background: #f8fafc;
    }

    .profile-name {
        text-align: center;
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin: .25rem 0 0;
    }

    .profile-email {
        text-align: center;
        color: #64748b;
        font-size: .95rem;
        margin-top: .35rem;
        word-break: break-word;
    }

    .profile-chip-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: .6rem;
        margin-top: 1rem;
    }

    .profile-chip {
        padding: .55rem .9rem;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 700;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    .profile-actions {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        margin-top: 1.25rem;
    }

    .profile-btn {
        flex: 1 1 150px;
        border: none;
        border-radius: 16px;
        padding: .9rem 1rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        transition: .25s ease;
    }

    .profile-btn:hover {
        transform: translateY(-2px);
    }

    .btn-edit {
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #fff;
    }

    .btn-logout {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .detail-card {
        padding: 1.5rem;
    }

    .detail-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .detail-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 900;
        color: #0f172a;
    }

    .detail-note {
        color: #64748b;
        font-size: .95rem;
        margin-top: .25rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .info-box {
        padding: 1rem 1.05rem;
        border-radius: 20px;
        border: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .info-label {
        color: #64748b;
        font-size: .84rem;
        font-weight: 700;
        margin-bottom: .35rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .info-value {
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        word-break: break-word;
    }

    .info-full {
        grid-column: 1 / -1;
    }

    .profile-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 1.25rem 0;
    }

    .member-banner {
        border-radius: 22px;
        padding: 1rem 1.1rem;
        background: linear-gradient(135deg, #ede9fe, #f5f3ff);
        border: 1px solid #ddd6fe;
        color: #4c1d95;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .member-banner i {
        font-size: 1.2rem;
    }

    @media (max-width: 992px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-wrap {
            padding: 1rem 0;
        }

        .profile-hero {
            padding: 1.3rem;
            border-radius: 24px;
        }

        .profile-card,
        .detail-card {
            border-radius: 24px;
            padding: 1.1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-btn {
            flex: 1 1 100%;
        }

        .avatar-wrap {
            width: 122px;
            height: 122px;
        }
    }
</style>

<div class="container profile-wrap">
    <div class="profile-hero">
        <div class="profile-hero-inner">
            <div>
                <h2 class="profile-title">My Profile</h2>
                <p class="profile-subtitle">
                    Manage your account details, keep your contact information updated, and access your profile settings in one place.
                </p>
                <div class="profile-badge">
                    <i class="fas fa-shield-heart"></i>
                    Secure Account
                </div>
            </div>
        </div>
    </div>

    <div class="profile-grid">
        <div class="profile-card">
            <div class="avatar-wrap">
                <img src="<?= $profileImage; ?>" class="profile-avatar" alt="Profile Image" id="image">
            </div>

            <div class="profile-name" id="profileName"><?= $profileName; ?></div>
            <div class="profile-email" id="profileEmail"><?= $profileEmail; ?></div>

            <div class="profile-chip-row">
                <span class="profile-chip">
                    <i class="fas fa-user me-1"></i> Member
                </span>
                <span class="profile-chip">
                    <i class="fas fa-circle-check me-1"></i> Active
                </span>
            </div>

            <div class="member-banner mt-4">
                <i class="fas fa-crown"></i>
                <div>Gold Member</div>
            </div>

            <div class="profile-actions">
                <a href="update2.php?id=<?= $profileId; ?>" class="profile-btn btn-edit">
                    <i class="fa-solid fa-pen"></i>
                    Edit Profile
                </a>

                <a href="logout.php" class="profile-btn btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <div>
                    <h3 class="detail-title">Account Details</h3>
                    <div class="detail-note">Your registered information shown below.</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= $profileName; ?></div>
                </div>

                <div class="info-box">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?= $profileEmail; ?></div>
                </div>

                <div class="info-box info-full">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">+91 <?= $profilePhone; ?></div>
                </div>

                <div class="info-box info-full">
                    <div class="info-label">Account ID</div>
                    <div class="info-value">#<?= $profileId; ?></div>
                </div>
            </div>

            <div class="profile-divider"></div>

            <div class="member-banner">
                <i class="fas fa-star"></i>
                <div>Premium account experience with fast support, order tracking, and secure checkout.</div>
            </div>
        </div>
    </div>
</div>
</section>