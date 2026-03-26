# NamCong ReturnShield

Vietnamese version: [README.vi.md](README.vi.md)

## Overview

`NamCong_ReturnShield` is a Magento 2 MVP module for reducing returns before checkout.

It does not manage RMAs or post-purchase return operations. Instead, it calculates a rule-based return risk score for products, exposes the result to storefront widgets, and provides a lightweight admin dashboard so merchants can spot risky products before the order is placed.

Current implementation goals:

- calculate a `0-100` return risk score
- generate plain-language reasons and recommendations
- render a PDP advisory block
- render a cart advisory block
- expose configuration and a simple admin dashboard
- stay server-rendered and CSP-safe on the storefront

Typical end-to-end workflow:

1. Enable the module and run `setup:upgrade`.
2. Configure thresholds, category mappings, and rule penalties in Magento Admin.
3. Populate product-level guidance fields created by the setup patch.
4. Let `RiskAnalyzer` evaluate each product from category, attribute, review, and customer-group context.
5. Show the result on the product page, cart page, and admin dashboard.

## Module Responsibilities

- Read store-level ReturnShield configuration and defaults.
- Add product attributes used by the rule engine.
- Analyze a product and return score, label, reasons, and recommendations.
- Prepare product and cart data for storefront templates through view models.
- Inject frontend templates into PDP and cart layouts.
- Provide an admin page that lists products whose score is at or above the medium-risk threshold.

## Architecture Map

### Core scoring

- `Service/RiskAnalyzer.php`
  Central rule engine. Accepts a product and optional customer group ID, then returns a `RiskAnalysis` value object.

- `Model/RiskAnalysis.php`
  Result container for:
  - score
  - label (`Low`, `Medium`, `High`)
  - reasons
  - recommendations

- `Model/Config.php`
  Reads module configuration from Magento config storage and applies defaults defined in `etc/config.xml`.

### Storefront integration

- `ViewModel/ProductRisk.php`
  Reads `current_product` from the registry, resolves customer group from `Http\Context`, and prepares PDP data.

- `ViewModel/CartRisk.php`
  Reads the current quote from checkout session, analyzes all visible cart items, and prepares flagged-item or low-risk fallback output for the cart template.

- `view/frontend/layout/catalog_product_view.xml`
  Injects the PDP widget block under `product.info.main`.

- `view/frontend/templates/product/risk.phtml`
  Renders the PDP advisory card.

- `view/frontend/layout/checkout_cart_index.xml`
  Injects the cart widget block into `checkout.cart.form.before`.

- `view/frontend/templates/cart/summary.phtml`
  Renders the cart advisory block.

- `view/frontend/layout/default.xml`
  Loads shared frontend CSS.

- `view/frontend/web/css/returnshield.css`
  Styles the PDP and cart cards.

### Admin integration

- `etc/adminhtml/system.xml`
  Declares all admin configuration fields.

- `etc/adminhtml/menu.xml`
  Adds the dashboard menu entry under `Marketing > ReturnShield`.

- `etc/acl.xml`
  Defines ACL resources for dashboard access and configuration access.

- `Controller/Adminhtml/Dashboard/Index.php`
  Opens the admin dashboard page.

- `Block/Adminhtml/Dashboard/Report.php`
  Loads a product collection, runs scoring, filters by medium threshold, and prepares rows for the template.

- `view/adminhtml/templates/dashboard/report.phtml`
  Renders the dashboard table.

### Setup

- `Setup/Patch/Data/AddReturnShieldAttributes.php`
  Adds the custom product attributes used by the module.

## Installation & Enablement

Place the module at:

```bash
app/code/NamCong/ReturnShield
```

Minimum commands:

```bash
bin/magento module:enable NamCong_ReturnShield
bin/magento setup:upgrade
bin/magento cache:flush
```

What `setup:upgrade` does for this module:

- registers the module
- executes the data patch that creates ReturnShield product attributes

Optional production commands:

```bash
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```

Useful cache clean commands during frontend/layout work:

```bash
bin/magento cache:clean layout block_html config
```

## Configuration

Admin path:

```text
Stores > Configuration > Catalog > ReturnShield
```

The config is entered at Default or Website scope in admin, and the module reads values at store scope with Magento inheritance.

Saving any field inside the `returnshield` section automatically clears `config`, `layout`, `block_html`, and `full_page` cache types so storefront output updates immediately.

### General

| Field | Purpose | Default |
| --- | --- | --- |
| Enable Module | Master on/off switch for storefront and dashboard behavior | `1` |
| Medium Risk Threshold | Minimum score for `Medium` label and dashboard/cart flagged state | `40` |
| High Risk Threshold | Minimum score for `High` label | `70` |
| Products In Dashboard | Maximum number of highest-risk products displayed in the dashboard snapshot | `25` |

### Rule Engine

| Field | Purpose | Default |
| --- | --- | --- |
| High Risk Category IDs | Comma-separated category IDs that always add risk | empty |
| Fashion Category IDs | Comma-separated category IDs that activate size/material rules | empty |
| Electronics/Home Category IDs | Comma-separated category IDs that activate compatibility rules | empty |
| High Risk Category Penalty | Penalty added when a product belongs to a configured high-risk category | `20` |
| Configurable Product Penalty | Penalty added to configurable products | `10` |
| Missing Size Guidance Penalty | Penalty added when fashion guidance is missing | `20` |
| Missing Material Details Penalty | Penalty added when fashion material info is missing | `10` |
| Missing Compatibility Notes Penalty | Penalty added when compatibility notes are missing | `20` |
| Guest Penalty | Penalty added for guest shoppers on configurable products | `10` |
| Low Rating Threshold | Rating summary below this value counts as a warning signal | `65` |
| Low Rating Penalty | Penalty added when rating summary is below threshold | `15` |
| Negative Review Keywords | Comma-separated keywords scanned in the latest approved review details | `small,large,fit,color,darker,lighter,quality,cheap,broken,not as described` |
| Negative Review Penalty | Penalty added when any configured keyword is matched | `15` |

## Product Attributes Added By Setup Patch

The setup patch creates a dedicated `ReturnShield` group in the product form and adds these attributes:

| Attribute Code | Type | Purpose |
| --- | --- | --- |
| `return_risk_manual_adjustment` | int/text input | Manually increases the base score. Default is `0`. |
| `return_risk_override_note` | textarea | Merchant-defined explanation appended to the reasons list. |
| `return_size_guidance` | textarea | Fit guidance used by fashion-oriented rules. |
| `return_compatibility_notes` | textarea | Compatibility/setup guidance used by electronics/home rules. |

The scoring service also reads other existing data sources:

- product category IDs
- product type ID
- `material`
- `size_chart`
- rating summary
- latest approved review detail text
- customer group / guest status

## Scoring Workflow

`RiskAnalyzer::analyze()` runs the current rule pipeline in this order:

1. Resolve store ID and start from `return_risk_manual_adjustment`.
2. Read product category IDs and compare them against configured category lists.
3. Add `high_risk_category_penalty` if the product belongs to any configured high-risk category.
4. Add `configurable_penalty` if the product type is configurable.
5. Determine whether the product belongs to configured fashion categories.
6. If fashion logic is active:
   - add `size_guidance_penalty` when both `return_size_guidance` and `size_chart` are empty
   - add `material_penalty` when `material` is empty
7. Determine whether the product belongs to configured electronics/home categories.
8. If electronics/home logic is active:
   - add `compatibility_penalty` when `return_compatibility_notes` is empty
9. Add `guest_penalty` when the shopper is not logged in and the product is configurable.
10. Read rating summary:
    - if a numeric summary already exists on the product, use it
    - if not, safely load it through review summary append logic on a cloned product object
11. Add `low_rating_penalty` when rating summary is below `low_rating_threshold`.
12. Load the latest approved reviews for the product and scan the latest five review details for configured keywords.
13. Add `negative_review_penalty` once when any configured keyword is found.
14. Append `return_risk_override_note` to the reasons list if it exists.
15. Deduplicate reasons and recommendations.
16. Clamp the final score to `0-100`.
17. Map the score to:
    - `High` when score >= `high_threshold`
    - `Medium` when score >= `medium_threshold`
    - `Low` otherwise
18. Limit output payload to:
    - up to 4 reasons
    - up to 3 recommendations

Important implementation note:

- the module is rule-based only
- it does not train or store a predictive model
- it does not persist score history

## Frontend Rendering Workflow

### Shared storefront behavior

- The module loads CSS through `view/frontend/layout/default.xml`.
- Storefront output is rendered server-side.
- No inline JavaScript is used by the module templates.

### Product detail page

Layout injection:

- `view/frontend/layout/catalog_product_view.xml`
- block target: `product.info.main`

Data flow:

1. `ProductRisk` resolves the current product from Magento registry.
2. `ProductRisk` resolves customer group from `Magento\Framework\App\Http\Context`.
3. `RiskAnalyzer` returns a `RiskAnalysis` object.
4. `view/frontend/templates/product/risk.phtml` renders the advisory card.

Current render behavior:

- the PDP widget renders whenever:
  - the module is enabled
  - `current_product` exists
- the PDP widget no longer disappears when score is `0`
- when there are no meaningful warning signals, the template renders a low-risk snapshot message instead

### Cart page

Layout injection:

- `view/frontend/layout/checkout_cart_index.xml`
- container target: `checkout.cart.form.before`

Data flow:

1. `CartRisk` loads the current quote from checkout session.
2. The view model analyzes all visible quote items with the same `RiskAnalyzer` service.
3. `getFlaggedItems()` filters items whose score is at least the medium threshold.
4. `view/frontend/templates/cart/summary.phtml` renders the advisory block.

Current render behavior:

- the cart widget renders whenever:
  - the module is enabled
  - the quote has visible items
- the cart template prioritizes flagged items first
- if no cart item reaches the medium threshold, the template falls back to rendering all analyzed items as a low-risk snapshot
- this means the cart block is visible for populated carts even when all items are low risk

## Admin Dashboard Workflow

Menu path:

```text
Marketing > ReturnShield > Return Risk Dashboard
```

Current flow:

1. `Controller/Adminhtml/Dashboard/Index.php` opens the dashboard page.
2. `Block/Adminhtml/Dashboard/Report.php` scans enabled products for the current store in batches.
3. The block selects the attributes needed by the scoring service and dashboard output.
4. Each product is analyzed by the same `RiskAnalyzer` used on the storefront.
5. Products with score below the medium threshold are skipped.
6. The block keeps the highest-scoring rows seen so far and trims the result to `max_dashboard_products`.
7. The template displays:
   - SKU
   - product name
   - score
   - level
   - reasons
   - recommendations

Current characteristics:

- dashboard scope is snapshot-based, not historical
- dashboard scans the store catalog at request time and displays only the top `max_dashboard_products` scored rows
- dashboard is not a UI component grid
- dashboard is not an analytics report with persisted before/after metrics

## Current Behavior / Render Conditions

Use these rules when validating expected output:

- If the module is disabled, storefront blocks stop rendering and the dashboard returns no rows.
- Category-based penalties do nothing until category ID fields are configured.
- Fashion rules only run when the product belongs to a configured fashion category.
- Electronics/home rules only run when the product belongs to a configured electronics/home category.
- Review keyword logic only runs when:
  - keywords are configured
  - approved reviews exist
- Guest penalty only applies to configurable products for not-logged-in shoppers.
- Merchant override note adds a textual reason, but does not change score by itself.
- PDP shows a low-risk snapshot even when score is `0`.
- Cart shows flagged items first, but still renders a low-risk snapshot when no item is flagged.
- Admin dashboard only shows products with score greater than or equal to the medium threshold.

## Limitations / Not Yet Implemented

The current module intentionally does not include these features yet:

- no real RMA ingestion
- no credit memo or return-order history ingestion
- no AI model or SaaS inference layer
- no per-variant configurable-product scoring UI
- no Hyva compatibility module
- no checkout-page widget
- no enterprise model training
- no persisted analytics for score trends or return-rate deltas
- no exportable UI component grid for the dashboard
- no ML-based prediction; scoring is based on rules, reviews, and rating signals only

## Troubleshooting / Debug Checklist

### Module and setup

- Verify the module is enabled:

  ```bash
  bin/magento module:status NamCong_ReturnShield
  ```

- Re-run upgrade if attributes or configuration seem missing:

  ```bash
  bin/magento setup:upgrade
  ```

### Product attributes

- Open a product in admin and verify the `ReturnShield` attribute group exists.
- Confirm these fields are present:
  - `Return Risk Manual Adjustment`
  - `Return Risk Merchant Note`
  - `Return Size Guidance`
  - `Return Compatibility Notes`

### Configuration

- Confirm `Stores > Configuration > Catalog > ReturnShield > General > Enable Module` is enabled.
- Confirm the category ID lists match real category IDs assigned to products.
- Confirm threshold and penalty values are not set so high that almost everything stays below the medium threshold.

### Product page not showing expected warnings

- Confirm the product page has a valid `current_product` in Magento context.
- Confirm the product actually belongs to categories configured in ReturnShield.
- Confirm the product has missing fields or review/rating signals that can trigger penalties.
- Remember that PDP now renders low-risk snapshots even at score `0`, so a fully missing block usually means:
  - module disabled
  - layout cache stale
  - product context missing

### Cart block not rendering

- Confirm the quote has visible items.
- A guest session with an empty cart will not render the cart block.
- If the cart has items but no medium-risk items, the block should still render as a low-risk snapshot.

### Dashboard empty

- Confirm the module is enabled.
- Confirm at least one analyzed product reaches the medium threshold.
- Lower the medium threshold temporarily if you need to validate output quickly.

### Cache after template/layout changes

- Clean layout and block HTML cache after editing layout XML or `.phtml` files:

  ```bash
  bin/magento cache:clean layout block_html config
  ```

- If CSS changes are not visible in production mode, redeploy static content.

### Review-based signals

- Confirm reviews are approved.
- Confirm keywords in config match substrings in the latest review detail text.
- Current implementation scans up to the latest five approved review records.
