# NamCong_Loyalty

`NamCong_Loyalty` is a Magento 2 loyalty module that awards points for customer actions, tracks point history and customer level, lets admins manage loyalty rules and rewards, and provides a customer-facing dashboard for viewing balances and redeeming rewards.

This README describes the current implementation and workflow in the codebase.

## Module Scope

The module currently provides:

- Loyalty point balances stored per customer
- Point history tracking for earn, spend, and expiry actions
- Customer levels: `bronze`, `silver`, `gold`
- Admin management for loyalty rules and rewards
- Customer account page: `My Loyalty Points`
- GraphQL queries and mutation for loyalty data
- REST routes declared in `etc/webapi.xml`
- Daily cron job for expiring inactive balances

## Current Business Workflow

### 1. Default data on install or upgrade

During `php bin/magento setup:upgrade`, the module seeds these default records if they are not already present:

- Rules
  - `Registration Bonus`
  - `Product Review Reward`
  - `Purchase Points`
- Rewards
  - `$5 Discount Voucher`
  - `$10 Discount Voucher`
  - `Free Shipping`
  - `Premium Gift Product`

The module now keeps this seed process idempotent and includes a cleanup patch for legacy duplicated default rows.

### 2. How customers earn points

The module listens to three Magento events:

- `customer_register_success`
- `review_save_after`
- `sales_order_save_after`

Point-award flow:

1. The observer detects a supported customer action.
2. `RuleEngine` loads active loyalty rules and checks:
   - action type
   - customer group eligibility
   - date range through the repository filter
3. `PointManager` updates the customer balance and level.
4. A history row is inserted into `loyalty_history`.

Detailed action behavior:

- Registration
  - Triggered after customer registration succeeds.
  - Awards the sum of active rules with action type `registration`.

- Review
  - Triggered on review save.
  - Awards points only when the review becomes `approved`.
  - The same review is not rewarded again if it was already approved before.

- Order
  - Triggered on order save.
  - Awards points only when the order state is `complete`.
  - The module prevents double-awarding by checking `loyalty_history` for the same `order_id` and action type `order`.
  - Earned points are:
    - `floor(base_grand_total)`
    - plus the sum of active `order` rules

## Level Workflow

Customer level is recalculated every time the point balance changes.

- `bronze`: `0 - 499`
- `silver`: `500 - 1999`
- `gold`: `2000+`

The storefront dashboard also shows progress toward the next level.

## Reward Redemption Workflow

When a customer redeems a reward:

1. The module checks that the customer is logged in.
2. It loads the selected reward and verifies that it is active.
3. It checks whether the customer has enough points.
4. `RewardManager` creates a Magento sales rule coupon.
5. Only after coupon creation succeeds does `PointManager` deduct points.
6. A redemption history row is created with the generated coupon code.

Generated coupon codes use the format:

- `LOYALTY-XXXXXX`

Reward type behavior in the current implementation:

- `discount`
  - Creates a fixed-amount sales rule coupon.
- `free_shipping`
  - Creates a coupon and also sets Magento free-shipping behavior on the rule.
- `gift_product`
  - Is still handled as a coupon-based promotion placeholder.
  - It does not automatically add a catalog product to cart or reserve inventory.

## Point Expiry Workflow

The cron job `namcong_loyalty_expire_points` runs every day at `02:00`.

Current expiry logic:

- If `loyalty_points.updated_at` is older than `365` days
- and the customer still has a positive balance
- the module resets the balance to `0`
- recalculates the level back to `bronze`
- inserts an `expiration` entry into `loyalty_history`

Important note:

- Expiry is based on inactivity of the aggregate balance record.
- The module does not track expiry per earning transaction.

## Admin Workflow

Admin menu path:

- `Marketing > Loyalty Program > Loyalty Rules`
- `Marketing > Loyalty Program > Rewards Management`
- `Marketing > Loyalty Program > Customer Points`

### Loyalty Rules

Admins can configure:

- Rule name
- Point amount
- Action type
  - `order`
  - `registration`
  - `review`
- Active flag
- Customer groups
- From date
- To date

### Rewards

Admins can configure:

- Reward name
- Required points
- Reward type
- Reward value
- Active flag

### Customer Points

The admin listing is used to monitor:

- Customer balances
- Current level
- Point history through the related module data

## Storefront Workflow

The module adds a customer account navigation link:

- `My Loyalty Points`

Route:

- `loyalty/account/index`

The customer dashboard currently shows:

- Current point balance
- Current level
- Progress to next level
- Recent points history
- Active rewards available for redemption

Active rewards are served through a cache layer and cache is invalidated when rewards are saved or deleted.

## API Surface

### GraphQL

Queries:

- `loyaltyCustomerPoints`
- `loyaltyRewards`

Mutation:

- `loyaltyRedeemReward`

### REST routes declared by the module

- `GET /V1/loyalty/customer-points`
- `POST /V1/loyalty/apply-points`
- `GET /V1/loyalty/rewards`
- `POST /V1/loyalty/redeem`

## Data Model

The module stores data in four custom tables:

- `loyalty_points`
  - one balance row per customer
- `loyalty_history`
  - immutable history rows for point changes
- `loyalty_rule`
  - admin-defined point-award rules
- `loyalty_reward`
  - admin-defined redeemable rewards

## Installation

Enable the module and run Magento setup commands:

```bash
php bin/magento module:enable NamCong_Loyalty
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

If this module was already installed in an older build, run:

```bash
php bin/magento setup:upgrade
```

to apply the deduplication patch for default rules and rewards.

## Dependencies

The module depends on:

- `Magento_Customer`
- `Magento_Sales`
- `Magento_Catalog`
- `Magento_Review`
- `Magento_Store`
- `Magento_Backend`
- `Magento_Ui`

PHP requirement:

- `^8.1`

## Current Limitations

- Reward fulfillment is coupon-based; it is not a full gift-product engine.
- Point expiry is based on `loyalty_points.updated_at`, not transaction-level expiration.
- Order points are awarded when the order reaches `complete`, not at placement time.
- REST routes are declared, but if your project relies heavily on external integrations you should validate the exact request payloads and auth flow in your environment.

## Version

- Module name: `NamCong_Loyalty`
- Composer package: `namcong/module-loyalty`
- Version: `1.0.0`
