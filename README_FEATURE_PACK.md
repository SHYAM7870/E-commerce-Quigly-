# Quigly Feature Pack

This pack adds four upgrades:

1. Admin-manageable homepage banners and brand logos
2. Saved address management with default address support
3. Return / refund request flow for delivered orders
4. A premium support center UI

## Files included

- `database/feature_pack.sql`
- `admin/includes/feature_utils.php`
- `admin/Pages/site_assets.php`
- `admin/actions/site_assets_action.php`
- `site_assets_renderer.php`
- `saved_addresses.php`
- `address_action.php`
- `checkout_address_widget.php`
- `return_request.php`
- `return_request_action.php`
- `admin/Pages/return_requests.php`
- `admin/actions/return_action.php`
- `support_center.php`
- `admin/includes/sections/support.php` (replacement)

## Install order

1. Import `database/feature_pack.sql` into `college_db`.
2. Copy the PHP files into the same folders in your project.
3. Replace `admin/includes/sections/support.php` with the version in this pack.
4. Add these two lines to `index.php`:
   ```php
   <?php include 'site_assets_renderer.php'; ?>
   ```
   Place it right after the navbar include inside `<main>`.
5. Add this line inside the checkout section, right after the delivery address textarea:
   ```php
   <?php include __DIR__ . '/../../checkout_address_widget.php'; ?>
   ```
6. Add sidebar links for the new admin pages:
   - `admin/Pages/site_assets.php`
   - `admin/Pages/return_requests.php`

## Notes

- Uploads go into `upload/site-assets/` for banners and brands.
- Saved addresses are tied to the logged-in user.
- Return requests only allow orders that are already delivered.
- The support center still uses the existing `support_tickets` table.
