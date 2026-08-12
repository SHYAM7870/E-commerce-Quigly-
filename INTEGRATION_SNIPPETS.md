# Integration Snippets

## 1) Homepage banners + brands

In `index.php`, right after:

```php
<?php include __DIR__ . '/admin/includes/navbar.php'; ?>
```

add:

```php
<?php include __DIR__ . '/site_assets_renderer.php'; ?>
```

## 2) Saved address auto-fill in checkout

In `admin/includes/sections/checkout.php`, right after the delivery address textarea block, add:

```php
<?php include __DIR__ . '/../../../checkout_address_widget.php'; ?>
```

## 3) Admin sidebar links

Add these links anywhere inside the sidebar menu:

```php
<li>
  <a href="<?= BASE_URL ?>Pages/site_assets.php" class="sb-link <?= ($current == 'site_assets.php') ? 'active' : '' ?>">
    <i class="fas fa-panorama"></i>
    <span class="sb-text">Homepage Assets</span>
  </a>
</li>

<li>
  <a href="<?= BASE_URL ?>Pages/return_requests.php" class="sb-link <?= ($current == 'return_requests.php') ? 'active' : '' ?>">
    <i class="fas fa-rotate-left"></i>
    <span class="sb-text">Returns & Refunds</span>
  </a>
</li>
```

## 4) Checkout address page link

Add a quick link to `saved_addresses.php` from your profile menu or navbar so users can manage addresses before checkout.

## 5) Support center

Use `support_center.php` as the premium customer-facing support page.
