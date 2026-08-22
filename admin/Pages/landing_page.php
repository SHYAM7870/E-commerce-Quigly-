<?php
include_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// ── Auto-create table ──
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS landing_page_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── Save handler ──
$save_success = false;
$save_error   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_lp_save'])) {
    $allowed = [
        'hero_headline','hero_subtext','hero_cta_label','hero_cta_url','hero_cta2_label','hero_cta2_url',
        'feat1_icon','feat1_title','feat1_desc',
        'feat2_icon','feat2_title','feat2_desc',
        'feat3_icon','feat3_title','feat3_desc',
        'feat4_icon','feat4_title','feat4_desc',
        'stat1_num','stat1_label','stat2_num','stat2_label',
        'stat3_num','stat3_label','stat4_num','stat4_label',
        't1_init','t1_name','t1_role','t1_text',
        't2_init','t2_name','t2_role','t2_text',
        't3_init','t3_name','t3_role','t3_text',
        'cta_title','cta_sub','cta_btn','cta_btn_url','footer_tag',
    ];
    foreach ($allowed as $k) {
        if (!isset($_POST[$k])) continue;
        $val = mysqli_real_escape_string($conn, trim($_POST[$k]));
        $key = mysqli_real_escape_string($conn, $k);
        mysqli_query($conn, "INSERT INTO landing_page_settings (setting_key, setting_value)
            VALUES ('$key','$val')
            ON DUPLICATE KEY UPDATE setting_value='$val'");
    }
    $save_success = true;
}

// ── Load all settings ──
function lp_adm($conn, $key, $default = '') {
    $key = mysqli_real_escape_string($conn, $key);
    $r = mysqli_query($conn, "SELECT setting_value FROM landing_page_settings WHERE setting_key='$key' LIMIT 1");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        return $row['setting_value'] !== null ? htmlspecialchars($row['setting_value']) : htmlspecialchars($default);
    }
    return htmlspecialchars($default);
}

$v = [];
$keys_defaults = [
    'hero_headline'   => 'Shop the World.<br><span class="grad-text">Delivered to Your Door.</span>',
    'hero_subtext'    => 'Quigly brings you millions of products from top brands worldwide — with lightning-fast delivery, unbeatable prices, and a shopping experience you\'ll love.',
    'hero_cta_label'  => 'Start Shopping',
    'hero_cta_url'    => '/Quigly/register.php',
    'hero_cta2_label' => 'Sign In',
    'hero_cta2_url'   => '/Quigly/login.php',
    'feat1_icon'  => 'fas fa-shield-halved',
    'feat1_title' => 'Secure Shopping',
    'feat1_desc'  => 'Every transaction is encrypted and protected. Shop with complete confidence and zero worries.',
    'feat2_icon'  => 'fas fa-truck-fast',
    'feat2_title' => 'Express Delivery',
    'feat2_desc'  => 'Get your orders delivered fast — same-day, next-day or scheduled. We bring it right to your doorstep.',
    'feat3_icon'  => 'fas fa-tags',
    'feat3_title' => 'Best Prices',
    'feat3_desc'  => 'We compare thousands of sellers to guarantee you always get the best deal on every product.',
    'feat4_icon'  => 'fas fa-headset',
    'feat4_title' => '24/7 Support',
    'feat4_desc'  => 'Our dedicated support team is always available to help you with orders, returns, or any queries.',
    'stat1_num'   => '5M+', 'stat1_label' => 'Happy Customers',
    'stat2_num'   => '2M+', 'stat2_label' => 'Products Listed',
    'stat3_num'   => '99%',     'stat3_label' => 'Satisfaction Rate',
    'stat4_num'   => '180+',    'stat4_label' => 'Countries Served',
    't1_init' => 'S', 't1_name' => 'Sarah Johnson', 't1_role' => 'Verified Buyer, New York',
    't1_text' => 'Quigly is hands-down the best shopping experience I\'ve ever had. The delivery was super fast and the product quality exceeded my expectations!',
    't2_init' => 'R', 't2_name' => 'Ravi Kumar', 't2_role' => 'Verified Buyer, Mumbai',
    't2_text' => 'Amazing deals and genuinely great quality. I\'ve been shopping on Quigly for 6 months and haven\'t been disappointed once.',
    't3_init' => 'E', 't3_name' => 'Emily Chen',  't3_role' => 'Verified Buyer, London',
    't3_text' => 'The returns process is so smooth and the customer support is incredibly responsive. Quigly feels like the future of online shopping.',
    'cta_title'   => 'Start Shopping Smarter Today',
    'cta_sub'     => 'Join over 5 million satisfied customers and experience the world\'s most rewarding shopping platform.',
    'cta_btn'     => 'Create Free Account',
    'cta_btn_url' => '/Quigly/register.php',
    'footer_tag'  => 'Your Global Shopping Destination — Quality, Speed & Value.',
];
foreach ($keys_defaults as $k => $d) { $v[$k] = lp_adm($conn, $k, $d); }
?>

<style>
/* ════════════════════════════════════════
   LANDING PAGE ADMIN — PREMIUM v1
════════════════════════════════════════ */
.lp-page { padding: 28px 32px; max-width: 1100px; }
@media(max-width:768px){ .lp-page { padding: 16px; } }

/* ── Page Header ── */
.lp-page-header {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 14px;
  margin-bottom: 28px;
}
.lp-page-title { font-size: 22px; font-weight: 900; color: var(--adm-text); margin: 0; }
.lp-page-sub   { font-size: 13px; color: var(--adm-text-muted); margin: 3px 0 0; }
.preview-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px;
  border-radius: 12px;
  background: rgba(124,58,237,.12);
  border: 1px solid rgba(124,58,237,.3);
  color: #a78bfa;
  font-size: 13px; font-weight: 700;
  text-decoration: none;
  transition: .2s;
}
.preview-btn:hover { background: rgba(124,58,237,.22); color: #c4b5fd; }

/* ── Tabs ── */
.lp-tabs {
  display: flex; gap: 4px; flex-wrap: wrap;
  background: var(--adm-surface2);
  border: 1px solid var(--adm-border);
  border-radius: 14px;
  padding: 5px;
  margin-bottom: 24px;
}
.lp-tab-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 9px 18px;
  border-radius: 10px;
  border: none; cursor: pointer;
  background: transparent;
  color: var(--adm-text-muted);
  font-size: 13px; font-weight: 600;
  transition: .2s;
  white-space: nowrap;
}
.lp-tab-btn:hover { color: var(--adm-text); background: var(--adm-surface); }
.lp-tab-btn.active {
  background: linear-gradient(135deg,#7c3aed,#4f46e5);
  color: #fff;
  box-shadow: 0 4px 16px rgba(124,58,237,.3);
}
.lp-tab-btn i { font-size: 13px; }

/* ── Tab panels ── */
.lp-panel { display: none; }
.lp-panel.active { display: block; }

/* ── Cards ── */
.lp-card {
  background: var(--adm-surface);
  border: 1px solid var(--adm-border);
  border-radius: 20px;
  padding: 28px;
  margin-bottom: 20px;
  box-shadow: var(--adm-shadow);
}
.lp-card-title {
  font-size: 14px; font-weight: 800;
  color: var(--adm-text);
  margin-bottom: 20px;
  display: flex; align-items: center; gap: 8px;
}
.lp-card-title i { color: #7c3aed; }

/* ── Form fields ── */
.lp-field-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
  margin-bottom: 0;
}
.lp-field {
  display: flex; flex-direction: column; gap: 6px;
}
.lp-label {
  font-size: 12px; font-weight: 700;
  color: var(--adm-text-muted);
  text-transform: uppercase; letter-spacing: .5px;
}
.lp-input, .lp-textarea {
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid var(--adm-border);
  background: var(--adm-surface2);
  color: var(--adm-text);
  font-size: 13px; font-weight: 500;
  font-family: inherit;
  transition: .2s;
  outline: none;
}
.lp-input:focus, .lp-textarea:focus {
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,.15);
  background: var(--adm-surface);
}
.lp-textarea { resize: vertical; min-height: 80px; }
.lp-hint {
  font-size: 11px; color: var(--adm-text-faint);
}

/* ── Feature row ── */
.feat-row {
  padding: 20px;
  border-radius: 14px;
  background: var(--adm-surface2);
  border: 1px solid var(--adm-border);
  margin-bottom: 12px;
}
.feat-row-header {
  font-size: 12px; font-weight: 800;
  color: var(--adm-accent);
  text-transform: uppercase; letter-spacing: .5px;
  margin-bottom: 14px;
  display: flex; align-items: center; gap: 6px;
}
.feat-row-header i { font-size: 14px; }

/* Testimonial row */
.testi-row {
  padding: 20px;
  border-radius: 14px;
  background: var(--adm-surface2);
  border: 1px solid var(--adm-border);
  margin-bottom: 12px;
}

/* ── Stat grid ── */
.stat-grid-adm {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
}
.stat-row {
  padding: 16px;
  border-radius: 12px;
  background: var(--adm-surface2);
  border: 1px solid var(--adm-border);
  display: flex; flex-direction: column; gap: 10px;
}

/* ── Save button ── */
.lp-save-bar {
  position: sticky; bottom: 0;
  background: var(--adm-surface);
  border-top: 1px solid var(--adm-border);
  padding: 16px 0;
  display: flex; align-items: center; gap: 12px;
  z-index: 100;
  margin-top: 24px;
}
.btn-save {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 28px;
  border-radius: 12px;
  background: linear-gradient(135deg,#7c3aed,#4f46e5);
  color: #fff;
  font-size: 14px; font-weight: 700;
  border: none; cursor: pointer;
  box-shadow: 0 4px 20px rgba(124,58,237,.35);
  transition: .2s;
  font-family: inherit;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(124,58,237,.5); }
.btn-save:active { transform: none; }

/* ── Toast ── */
.lp-toast {
  display: none;
  align-items: center; gap: 10px;
  padding: 10px 18px;
  border-radius: 10px;
  background: rgba(34,197,94,.15);
  border: 1px solid rgba(34,197,94,.3);
  color: #4ade80;
  font-size: 13px; font-weight: 600;
}
.lp-toast.show { display: inline-flex; }

/* ── Icon preview ── */
.icon-preview {
  display: inline-flex; align-items: center; gap: 8px;
  margin-top: 6px;
  font-size: 13px; color: var(--adm-text-muted);
}
.icon-preview i { font-size: 18px; color: #a78bfa; }

/* ── Divider ── */
.lp-divider { height: 1px; background: var(--adm-border); margin: 20px 0; }

/* Responsive */
@media(max-width:600px) {
  .lp-tabs { gap: 2px; }
  .lp-tab-btn { padding: 8px 12px; font-size: 12px; }
}
</style>

<div class="lp-page">

  <!-- Page header -->
  <div class="lp-page-header">
    <div>
      <h4 class="lp-page-title"><i class="fas fa-globe" style="color:#7c3aed;margin-right:8px;"></i>Landing Page Manager</h4>
      <p class="lp-page-sub">Edit all sections of your public landing page. Changes save instantly.</p>
    </div>
    <a href="/Quigly/landing.php" target="_blank" class="preview-btn" id="preview-landing-btn">
      <i class="fas fa-external-link-alt"></i> Preview Landing Page
    </a>
  </div>

  <?php if ($save_success): ?>
  <div style="padding:12px 18px;border-radius:12px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#4ade80;font-size:13px;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <i class="fas fa-check-circle"></i> Landing page settings saved successfully!
  </div>
  <?php endif; ?>

  <form method="POST" id="lpForm">
    <input type="hidden" name="_lp_save" value="1">

    <!-- ── Tabs ── -->
    <div class="lp-tabs" role="tablist">
      <button type="button" class="lp-tab-btn active" onclick="lpTab('hero',this)" id="tab-hero">
        <i class="fas fa-wand-magic-sparkles"></i> Hero
      </button>
      <button type="button" class="lp-tab-btn" onclick="lpTab('features',this)" id="tab-features">
        <i class="fas fa-th-large"></i> Features
      </button>
      <button type="button" class="lp-tab-btn" onclick="lpTab('stats',this)" id="tab-stats">
        <i class="fas fa-chart-bar"></i> Stats
      </button>
      <button type="button" class="lp-tab-btn" onclick="lpTab('testimonials',this)" id="tab-testimonials">
        <i class="fas fa-quote-left"></i> Testimonials
      </button>
      <button type="button" class="lp-tab-btn" onclick="lpTab('cta',this)" id="tab-cta">
        <i class="fas fa-bullhorn"></i> CTA &amp; Footer
      </button>
    </div>

    <!-- ═══ TAB: HERO ═══ -->
    <div class="lp-panel active" id="panel-hero">
      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-star"></i> Hero Section</div>
        <div class="lp-field-group" style="grid-template-columns:1fr;">
          <div class="lp-field">
            <label class="lp-label">Headline (HTML allowed — use &lt;br&gt;, &lt;span class="grad-text"&gt;)</label>
            <textarea name="hero_headline" class="lp-textarea" rows="3" id="hero_headline_input"><?= $v['hero_headline'] ?></textarea>
            <span class="lp-hint">Tip: Wrap words in &lt;span class="grad-text"&gt;...&lt;/span&gt; for gradient effect.</span>
          </div>
          <div class="lp-field">
            <label class="lp-label">Subtext / Description</label>
            <textarea name="hero_subtext" class="lp-textarea" rows="3"><?= $v['hero_subtext'] ?></textarea>
          </div>
        </div>
        <div class="lp-divider"></div>
        <div class="lp-field-group">
          <div class="lp-field">
            <label class="lp-label">Primary CTA Button Text</label>
            <input type="text" name="hero_cta_label" class="lp-input" value="<?= $v['hero_cta_label'] ?>">
          </div>
          <div class="lp-field">
            <label class="lp-label">Primary CTA Button URL</label>
            <input type="text" name="hero_cta_url" class="lp-input" value="<?= $v['hero_cta_url'] ?>">
          </div>
          <div class="lp-field">
            <label class="lp-label">Secondary CTA Button Text</label>
            <input type="text" name="hero_cta2_label" class="lp-input" value="<?= $v['hero_cta2_label'] ?>">
          </div>
          <div class="lp-field">
            <label class="lp-label">Secondary CTA Button URL</label>
            <input type="text" name="hero_cta2_url" class="lp-input" value="<?= $v['hero_cta2_url'] ?>">
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ TAB: FEATURES ═══ -->
    <div class="lp-panel" id="panel-features">
      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-th-large"></i> Feature Cards (4 cards)</div>
        <p style="font-size:12px;color:var(--adm-text-muted);margin-bottom:20px;">
          Use Font Awesome class names for icons (e.g. <code>fas fa-shield-halved</code>). 
          <a href="https://fontawesome.com/icons" target="_blank" style="color:#a78bfa;">Browse Icons →</a>
        </p>
        <?php
        $featLabels = ['Feature 1','Feature 2','Feature 3','Feature 4'];
        for ($fi = 1; $fi <= 4; $fi++): ?>
        <div class="feat-row">
          <div class="feat-row-header">
            <i class="<?= $v["feat{$fi}_icon"] ?>"></i>
            <?= $featLabels[$fi-1] ?>
          </div>
          <div class="lp-field-group">
            <div class="lp-field">
              <label class="lp-label">Icon Class</label>
              <input type="text" name="feat<?= $fi ?>_icon" class="lp-input icon-field"
                     value="<?= $v["feat{$fi}_icon"] ?>"
                     oninput="previewIcon(this,'feat<?= $fi ?>_preview')"
                     placeholder="fas fa-shield-halved">
              <div class="icon-preview" id="feat<?= $fi ?>_preview">
                <i class="<?= $v["feat{$fi}_icon"] ?>"></i>
                <span>Icon preview</span>
              </div>
            </div>
            <div class="lp-field">
              <label class="lp-label">Title</label>
              <input type="text" name="feat<?= $fi ?>_title" class="lp-input" value="<?= $v["feat{$fi}_title"] ?>">
            </div>
            <div class="lp-field" style="grid-column: span 2;">
              <label class="lp-label">Description</label>
              <textarea name="feat<?= $fi ?>_desc" class="lp-textarea" rows="2"><?= $v["feat{$fi}_desc"] ?></textarea>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- ═══ TAB: STATS ═══ -->
    <div class="lp-panel" id="panel-stats">
      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-chart-bar"></i> Stats Bar (4 stats)</div>
        <div class="stat-grid-adm">
          <?php for ($si = 1; $si <= 4; $si++): ?>
          <div class="stat-row">
            <div class="lp-field">
              <label class="lp-label">Stat <?= $si ?> — Number / Value</label>
              <input type="text" name="stat<?= $si ?>_num" class="lp-input"
                     value="<?= $v["stat{$si}_num"] ?>"
                     placeholder="e.g. 10,000+ or 98%">
              <span class="lp-hint">Supports text like "10,000+" or "98%"</span>
            </div>
            <div class="lp-field">
              <label class="lp-label">Label</label>
              <input type="text" name="stat<?= $si ?>_label" class="lp-input"
                     value="<?= $v["stat{$si}_label"] ?>"
                     placeholder="e.g. Students Registered">
            </div>
          </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>

    <!-- ═══ TAB: TESTIMONIALS ═══ -->
    <div class="lp-panel" id="panel-testimonials">
      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-quote-left"></i> Testimonials (3 reviews)</div>
        <?php
        $tLabels = ['Testimonial 1','Testimonial 2','Testimonial 3'];
        for ($ti = 1; $ti <= 3; $ti++): ?>
        <div class="testi-row" style="<?= $ti < 3 ? 'margin-bottom:12px;' : '' ?>">
          <div class="feat-row-header" style="margin-bottom:12px;font-size:12px;font-weight:800;color:var(--adm-accent);text-transform:uppercase;letter-spacing:.5px;">
            <i class="fas fa-user-circle"></i> <?= $tLabels[$ti-1] ?>
          </div>
          <div class="lp-field-group">
            <div class="lp-field">
              <label class="lp-label">Avatar Initial (single letter)</label>
              <input type="text" name="t<?= $ti ?>_init" class="lp-input"
                     value="<?= $v["t{$ti}_init"] ?>" maxlength="2" placeholder="A">
            </div>
            <div class="lp-field">
              <label class="lp-label">Full Name</label>
              <input type="text" name="t<?= $ti ?>_name" class="lp-input" value="<?= $v["t{$ti}_name"] ?>">
            </div>
            <div class="lp-field">
              <label class="lp-label">Role / Department</label>
              <input type="text" name="t<?= $ti ?>_role" class="lp-input" value="<?= $v["t{$ti}_role"] ?>" placeholder="Computer Science, Year 3">
            </div>
            <div class="lp-field" style="grid-column: span 2;">
              <label class="lp-label">Review Text</label>
              <textarea name="t<?= $ti ?>_text" class="lp-textarea" rows="3"><?= $v["t{$ti}_text"] ?></textarea>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- ═══ TAB: CTA & FOOTER ═══ -->
    <div class="lp-panel" id="panel-cta">
      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-bullhorn"></i> CTA Banner Section</div>
        <div class="lp-field-group" style="grid-template-columns:1fr;">
          <div class="lp-field">
            <label class="lp-label">CTA Headline</label>
            <input type="text" name="cta_title" class="lp-input" value="<?= $v['cta_title'] ?>">
          </div>
          <div class="lp-field">
            <label class="lp-label">CTA Subtext</label>
            <input type="text" name="cta_sub" class="lp-input" value="<?= $v['cta_sub'] ?>">
          </div>
          <div class="lp-field-group" style="grid-template-columns: repeat(auto-fit, minmax(200px,1fr));">
            <div class="lp-field">
              <label class="lp-label">CTA Button Text</label>
              <input type="text" name="cta_btn" class="lp-input" value="<?= $v['cta_btn'] ?>">
            </div>
            <div class="lp-field">
              <label class="lp-label">CTA Button URL</label>
              <input type="text" name="cta_btn_url" class="lp-input" value="<?= $v['cta_btn_url'] ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="lp-card">
        <div class="lp-card-title"><i class="fas fa-circle-info"></i> Footer Tagline</div>
        <div class="lp-field">
          <label class="lp-label">Footer Tagline</label>
          <input type="text" name="footer_tag" class="lp-input" value="<?= $v['footer_tag'] ?>">
          <span class="lp-hint">Shown below the logo in the footer.</span>
        </div>
      </div>
    </div>

    <!-- ── Save Bar ── -->
    <div class="lp-save-bar">
      <button type="submit" class="btn-save" id="saveBtn">
        <i class="fas fa-save"></i> Save All Changes
      </button>
      <div class="lp-toast" id="lpToast">
        <i class="fas fa-check-circle"></i> Saved successfully!
      </div>
    </div>

  </form>
</div>

<script>
// Tab switching
function lpTab(tab, btn) {
  document.querySelectorAll('.lp-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.lp-tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + tab).classList.add('active');
  btn.classList.add('active');
}

// Icon live preview
function previewIcon(input, previewId) {
  const el = document.getElementById(previewId);
  if (!el) return;
  const i = el.querySelector('i');
  if (i) i.className = input.value.trim() || 'fas fa-question';
}

// Save toast
document.getElementById('lpForm').addEventListener('submit', function() {
  const btn = document.getElementById('saveBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
  btn.disabled = true;
});

// Auto show toast if saved
<?php if ($save_success): ?>
(function(){
  const t = document.getElementById('lpToast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 4000);
})();
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
