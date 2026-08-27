# My Peptide Core — WordPress Theme

A custom WooCommerce storefront theme for **My Peptide Core**, built with a
clinical/lab-tech look (dark navy + teal) and research-use-only (RUO)
compliance baked in from the start.

## What's included

- `theme/` — a complete, original custom WordPress theme (no page builder
  dependency). Works with WooCommerce for the shop/cart/checkout.
- Homepage (`front-page.php`) with hero, trust bar, category grid, featured
  products, "why us" section, and a newsletter signup block.
- WooCommerce integration (`theme/inc/woocommerce.php`):
  - Theme-matched product grid styling (4-column, card layout).
  - A "Research Use Only" disclaimer automatically shown on every product page.
  - A **required consent checkbox at checkout** ("I confirm I am 18+ and
    purchasing for laboratory research only...") — validated server-side.
- Auto-provisioned starter pages on theme activation
  (`theme/inc/compliance-pages.php`): Research Use Disclaimer,
  Terms & Conditions, Shipping & Returns, About Us, Contact, and a Home page.
  **This copy is placeholder legal boilerplate** — have it reviewed by a
  lawyer in your jurisdiction before you rely on it.
- Mobile-responsive nav, sticky header, footer with policy links and a
  persistent RUO disclaimer bar.

## Compliance note (read this)

Peptides sold for anything other than laboratory/research use are subject to
drug and consumer-protection regulation in most countries. This theme assumes
a **research-use-only business model**: no dosing claims, no medical claims,
no instructions for human/animal use, clear disclaimers on every product and
at checkout. Before launch:

- Have a lawyer review the Research Use Disclaimer, Terms & Conditions, and
  your actual product listings/marketing copy for compliance in every
  jurisdiction you ship to.
- Do not add content implying these products are safe or intended for human
  consumption, injection, or medical use.
- Consider requiring a business/institutional name at checkout in addition to
  the consent checkbox.

## How to install this on WordPress

### 1. Package the theme
From the repo root:
```bash
cd theme
zip -r ../my-peptide-core.zip . -x ".*"
```

### 2. Upload it
In your WordPress admin: **Appearance → Themes → Add New → Upload Theme**,
choose `my-peptide-core.zip`, install, then **Activate**.

(Alternatively, upload the `theme/` folder's contents via SFTP into
`wp-content/themes/my-peptide-core/` on your host, then activate it from
Appearance → Themes.)

### 3. Install WooCommerce
**Plugins → Add New**, search "WooCommerce", install and activate. Run through
its setup wizard — it will create the Shop, Cart, Checkout, and My Account
pages automatically.

### 4. Activate the theme (creates starter content)
Activating the theme (step 2) automatically creates the Home page and the
legal/marketing pages listed above, and sets Home as your front page. If you
re-activate later, it skips pages that already exist so nothing is duplicated.

### 5. Set your menu
**Appearance → Menus** — create a menu with Shop, About Us, Contact, and the
policy pages, and assign it to the "Primary Menu" location.

### 6. Add your logo
**Appearance → Customize → Site Identity → Logo** — upload your real logo.
Until you do, the header falls back to a styled "My Peptide Core" text logo.

### 7. Add products
**Products → Add New** for each peptide. Upload your real product photos.
The theme shows an "RUO" tag on every product card automatically and adds
the disclaimer block on single product pages — no extra setup needed per
product.

### 8. Review the placeholder legal pages
Edit the auto-created pages (Research Use Disclaimer, Terms & Conditions,
Shipping & Returns, About Us, Contact) with your real, lawyer-reviewed
copy before going live.

## Customizing the palette

Colors live as CSS custom properties at the top of `theme/style.css`
(`:root { --mpc-navy: ...; --mpc-teal: ...; }`). Change those values to
retheme the entire site consistently.
