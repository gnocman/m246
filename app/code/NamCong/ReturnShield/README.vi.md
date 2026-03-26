# NamCong ReturnShield

English version: [README.md](README.md)

## Overview

`NamCong_ReturnShield` là một module Magento 2 MVP nhằm giảm tỷ lệ return trước khi khách hàng đặt đơn.

Module này không quản lý RMA hoặc các nghiệp vụ return sau mua hàng. Thay vào đó, nó tính toán `return risk score` theo rule, đưa kết quả ra storefront widget, và cung cấp một admin dashboard gọn nhẹ để merchant có thể nhìn thấy những sản phẩm có rủi ro cao trước khi đơn hàng được đặt.

Mục tiêu của implementation hiện tại:

- tính `return risk score` trong khoảng `0-100`
- sinh ra các lý do và khuyến nghị bằng ngôn ngữ dễ hiểu
- render một khối tư vấn trên PDP
- render một khối tư vấn trên trang cart
- cung cấp phần cấu hình và một admin dashboard đơn giản
- giữ storefront ở dạng server-rendered và CSP-safe

Luồng tổng thể end-to-end thông thường:

1. Enable module và chạy `setup:upgrade`.
2. Cấu hình threshold, mapping category, và các rule penalty trong Magento Admin.
3. Điền các trường hướng dẫn ở cấp product được tạo bởi setup patch.
4. Để `RiskAnalyzer` đánh giá từng product dựa trên category, attribute, review, và customer-group context.
5. Hiển thị kết quả trên trang product, trang cart, và admin dashboard.

## Module Responsibilities

- Đọc cấu hình ReturnShield theo store scope và các giá trị mặc định.
- Thêm các product attribute được sử dụng bởi rule engine.
- Phân tích product và trả về score, label, reasons, và recommendations.
- Chuẩn bị dữ liệu product và cart cho storefront templates thông qua view models.
- Inject frontend templates vào PDP và cart layouts.
- Cung cấp một trang admin liệt kê các product có score lớn hơn hoặc bằng medium-risk threshold.

## Architecture Map

### Core scoring

- `Service/RiskAnalyzer.php`
  Rule engine trung tâm. Nhận vào một product và customer group ID tùy chọn, sau đó trả về một `RiskAnalysis` value object.

- `Model/RiskAnalysis.php`
  Object chứa kết quả cho:
  - score
  - label (`Low`, `Medium`, `High`)
  - reasons
  - recommendations

- `Model/Config.php`
  Đọc cấu hình module từ Magento config storage và áp dụng các giá trị mặc định được định nghĩa trong `etc/config.xml`.

### Storefront integration

- `ViewModel/ProductRisk.php`
  Đọc `current_product` từ registry, lấy customer group từ `Http\Context`, và chuẩn bị dữ liệu cho PDP.

- `ViewModel/CartRisk.php`
  Đọc quote hiện tại từ checkout session, phân tích tất cả visible cart items, và chuẩn bị dữ liệu flagged-item hoặc low-risk fallback cho cart template.

- `view/frontend/layout/catalog_product_view.xml`
  Inject PDP widget block vào dưới `product.info.main`.

- `view/frontend/templates/product/risk.phtml`
  Render advisory card trên PDP.

- `view/frontend/layout/checkout_cart_index.xml`
  Inject cart widget block vào `checkout.cart.form.before`.

- `view/frontend/templates/cart/summary.phtml`
  Render advisory block trên cart.

- `view/frontend/layout/default.xml`
  Load CSS dùng chung cho frontend.

- `view/frontend/web/css/returnshield.css`
  Style cho PDP và cart cards.

### Admin integration

- `etc/adminhtml/system.xml`
  Khai báo tất cả các field cấu hình trong admin.

- `etc/adminhtml/menu.xml`
  Thêm menu dashboard dưới `Marketing > ReturnShield`.

- `etc/acl.xml`
  Định nghĩa ACL resources cho dashboard và phần configuration.

- `Controller/Adminhtml/Dashboard/Index.php`
  Mở trang admin dashboard.

- `Block/Adminhtml/Dashboard/Report.php`
  Load product collection, chạy scoring, lọc theo medium threshold, và chuẩn bị rows cho template.

- `view/adminhtml/templates/dashboard/report.phtml`
  Render bảng dashboard.

### Setup

- `Setup/Patch/Data/AddReturnShieldAttributes.php`
  Thêm các custom product attributes mà module sử dụng.

## Installation & Enablement

Đặt module tại:

```bash
app/code/NamCong/ReturnShield
```

Lệnh tối thiểu:

```bash
bin/magento module:enable NamCong_ReturnShield
bin/magento setup:upgrade
bin/magento cache:flush
```

`setup:upgrade` sẽ làm gì đối với module này:

- đăng ký module
- chạy data patch tạo ReturnShield product attributes

Lệnh tùy chọn cho production:

```bash
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```

Lệnh cache clean hữu ích khi làm frontend/layout:

```bash
bin/magento cache:clean layout block_html config
```

## Configuration

Đường dẫn trong admin:

```text
Stores > Configuration > Catalog > ReturnShield
```

Cấu hình được nhập ở Default hoặc Website scope trong admin, và module đọc giá trị ở store scope thông qua Magento inheritance.

Khi save bất kỳ field nào trong section `returnshield`, module sẽ tự động clear các cache type `config`, `layout`, `block_html`, và `full_page` để storefront cập nhật ngay.

### General

| Field | Ý nghĩa | Mặc định |
| --- | --- | --- |
| Enable Module | Công tắc bật/tắt tổng cho storefront và dashboard | `1` |
| Medium Risk Threshold | Mức score tối thiểu để nhận `Medium` và để được xem là flagged trong dashboard/cart | `40` |
| High Risk Threshold | Mức score tối thiểu để nhận `High` | `70` |
| Products In Dashboard | Số product rủi ro cao nhất tối đa được hiển thị trong dashboard snapshot | `25` |

### Rule Engine

| Field | Ý nghĩa | Mặc định |
| --- | --- | --- |
| High Risk Category IDs | Danh sách category ID phân tách bởi dấu phẩy, luôn cộng điểm rủi ro | empty |
| Fashion Category IDs | Danh sách category ID kích hoạt size/material rules | empty |
| Electronics/Home Category IDs | Danh sách category ID kích hoạt compatibility rules | empty |
| High Risk Category Penalty | Điểm penalty cộng khi product nằm trong high-risk category được cấu hình | `20` |
| Configurable Product Penalty | Điểm penalty cộng cho configurable products | `10` |
| Missing Size Guidance Penalty | Điểm penalty cộng khi fashion guidance bị thiếu | `20` |
| Missing Material Details Penalty | Điểm penalty cộng khi thông tin material của fashion product bị thiếu | `10` |
| Missing Compatibility Notes Penalty | Điểm penalty cộng khi compatibility notes bị thiếu | `20` |
| Guest Penalty | Điểm penalty cộng cho guest shopper trên configurable products | `10` |
| Low Rating Threshold | Rating summary thấp hơn mức này sẽ được xem là warning signal | `65` |
| Low Rating Penalty | Điểm penalty cộng khi rating summary thấp hơn threshold | `15` |
| Negative Review Keywords | Danh sách keyword phân tách bởi dấu phẩy, được quét trong latest approved review details | `small,large,fit,color,darker,lighter,quality,cheap,broken,not as described` |
| Negative Review Penalty | Điểm penalty cộng khi có bất kỳ keyword nào được match | `15` |

## Product Attributes Added By Setup Patch

Setup patch tạo một group `ReturnShield` riêng trong product form và thêm các attributes sau:

| Attribute Code | Type | Ý nghĩa |
| --- | --- | --- |
| `return_risk_manual_adjustment` | int/text input | Cộng thêm vào base score theo tay. Mặc định là `0`. |
| `return_risk_override_note` | textarea | Giải thích do merchant tự nhập, được append vào danh sách reasons. |
| `return_size_guidance` | textarea | Hướng dẫn fit/size được fashion-oriented rules sử dụng. |
| `return_compatibility_notes` | textarea | Hướng dẫn compatibility/setup được electronics/home rules sử dụng. |

Scoring service cũng đọc các nguồn dữ liệu có sẵn khác:

- product category IDs
- product type ID
- `material`
- `size_chart`
- rating summary
- latest approved review detail text
- customer group / guest status

## Scoring Workflow

`RiskAnalyzer::analyze()` chạy rule pipeline hiện tại theo thứ tự sau:

1. Xác định store ID và bắt đầu từ `return_risk_manual_adjustment`.
2. Đọc product category IDs và đối chiếu với các category list được cấu hình.
3. Cộng `high_risk_category_penalty` nếu product thuộc bất kỳ high-risk category nào đã cấu hình.
4. Cộng `configurable_penalty` nếu product type là configurable.
5. Xác định xem product có thuộc các fashion category đã cấu hình hay không.
6. Nếu fashion logic đang active:
   - cộng `size_guidance_penalty` khi cả `return_size_guidance` và `size_chart` đều rỗng
   - cộng `material_penalty` khi `material` rỗng
7. Xác định xem product có thuộc các electronics/home category đã cấu hình hay không.
8. Nếu electronics/home logic đang active:
   - cộng `compatibility_penalty` khi `return_compatibility_notes` rỗng
9. Cộng `guest_penalty` khi shopper chưa đăng nhập và product là configurable.
10. Đọc rating summary:
    - nếu product đã có rating summary dạng số, sử dụng trực tiếp
    - nếu chưa có, tải rating summary một cách an toàn thông qua review summary append logic trên một cloned product object
11. Cộng `low_rating_penalty` nếu rating summary thấp hơn `low_rating_threshold`.
12. Load latest approved reviews của product và quét detail text của năm review mới nhất theo các keyword được cấu hình.
13. Cộng `negative_review_penalty` một lần khi có bất kỳ keyword nào được tìm thấy.
14. Append `return_risk_override_note` vào reasons list nếu có.
15. Loại bỏ duplicate trong reasons và recommendations.
16. Giới hạn final score trong khoảng `0-100`.
17. Mapping score thành:
    - `High` khi score >= `high_threshold`
    - `Medium` khi score >= `medium_threshold`
    - `Low` trong các trường hợp còn lại
18. Giới hạn payload output:
    - tối đa 4 reasons
    - tối đa 3 recommendations

Lưu ý implementation quan trọng:

- module này chỉ là rule-based
- nó không train hoặc lưu predictive model
- nó không persist score history

## Frontend Rendering Workflow

### Shared storefront behavior

- Module load CSS thông qua `view/frontend/layout/default.xml`.
- Storefront output được render theo kiểu server-side.
- Module templates không dùng inline JavaScript.

### Product detail page

Layout injection:

- `view/frontend/layout/catalog_product_view.xml`
- block target: `product.info.main`

Data flow:

1. `ProductRisk` lấy current product từ Magento registry.
2. `ProductRisk` lấy customer group từ `Magento\Framework\App\Http\Context`.
3. `RiskAnalyzer` trả về một `RiskAnalysis` object.
4. `view/frontend/templates/product/risk.phtml` render advisory card.

Current render behavior:

- PDP widget render khi:
  - module được enable
  - `current_product` tồn tại
- PDP widget không còn biến mất khi score là `0`
- khi không có warning signal đáng kể, template sẽ render một low-risk snapshot message thay vì không hiện gì

### Cart page

Layout injection:

- `view/frontend/layout/checkout_cart_index.xml`
- container target: `checkout.cart.form.before`

Data flow:

1. `CartRisk` load quote hiện tại từ checkout session.
2. View model phân tích tất cả visible quote items bằng cùng `RiskAnalyzer` service.
3. `getFlaggedItems()` lọc các items có score ít nhất bằng medium threshold.
4. `view/frontend/templates/cart/summary.phtml` render advisory block.

Current render behavior:

- cart widget render khi:
  - module được enable
  - quote có visible items
- cart template ưu tiên flagged items trước
- nếu không có cart item nào đạt medium threshold, template sẽ fallback sang render toàn bộ items đã được analyze dưới dạng low-risk snapshot
- điều này có nghĩa là cart block vẫn xuất hiện trên cart đã có sản phẩm ngay cả khi tất cả items đều low risk

## Admin Dashboard Workflow

Đường dẫn menu:

```text
Marketing > ReturnShield > Return Risk Dashboard
```

Luồng hiện tại:

1. `Controller/Adminhtml/Dashboard/Index.php` mở trang dashboard.
2. `Block/Adminhtml/Dashboard/Report.php` quét các enabled products của current store theo từng batch.
3. Block select các attributes cần thiết cho scoring service và dashboard output.
4. Từng product được analyze bằng cùng `RiskAnalyzer` được storefront sử dụng.
5. Các product có score nhỏ hơn medium threshold sẽ bị bỏ qua.
6. Block giữ lại các rows có score cao nhất đã gặp và cắt kết quả về `max_dashboard_products`.
7. Template hiển thị:
   - SKU
   - tên product
   - score
   - level
   - reasons
   - recommendations

Đặc điểm hiện tại:

- dashboard theo kiểu snapshot, không phải historical
- dashboard quét catalog của store tại thời điểm request và chỉ hiển thị top `max_dashboard_products` rows có score cao nhất
- dashboard không phải là UI component grid
- dashboard không phải analytics report có persisted before/after metrics

## Current Behavior / Render Conditions

Sử dụng các quy tắc này khi validate output:

- Nếu module bị disable, storefront blocks ngừng render và dashboard trả về rỗng.
- Category-based penalties không có tác dụng cho đến khi category ID fields được cấu hình.
- Fashion rules chỉ chạy khi product thuộc một fashion category đã cấu hình.
- Electronics/home rules chỉ chạy khi product thuộc một electronics/home category đã cấu hình.
- Review keyword logic chỉ chạy khi:
  - keywords được cấu hình
  - có approved reviews
- Guest penalty chỉ áp dụng cho configurable products với not-logged-in shoppers.
- Merchant override note thêm một lý do dạng văn bản, nhưng không tự thay đổi score.
- PDP hiển thị low-risk snapshot ngay cả khi score là `0`.
- Cart hiển thị flagged items trước, nhưng vẫn render low-risk snapshot nếu không có item nào bị flag.
- Admin dashboard chỉ hiển thị các product có score lớn hơn hoặc bằng medium threshold.

## Limitations / Not Yet Implemented

Module hiện tại có chủ ý không bao gồm các tính năng sau:

- chưa ingest RMA thật sự
- chưa ingest credit memo hoặc return-order history
- chưa có AI model hoặc SaaS inference layer
- chưa có per-variant configurable-product scoring UI
- chưa có Hyva compatibility module
- chưa có checkout-page widget
- chưa có enterprise model training
- chưa có persisted analytics cho score trends hoặc return-rate deltas
- chưa có exportable UI component grid cho dashboard
- chưa có ML-based prediction; scoring hiện tại chỉ dựa trên rules, reviews, và rating signals

## Troubleshooting / Debug Checklist

### Module and setup

- Kiểm tra module đã được enable:

  ```bash
  bin/magento module:status NamCong_ReturnShield
  ```

- Chạy lại upgrade nếu attributes hoặc configuration có vẻ bị thiếu:

  ```bash
  bin/magento setup:upgrade
  ```

### Product attributes

- Mở một product trong admin và kiểm tra `ReturnShield` attribute group đã tồn tại.
- Xác nhận các field sau có hiện diện:
  - `Return Risk Manual Adjustment`
  - `Return Risk Merchant Note`
  - `Return Size Guidance`
  - `Return Compatibility Notes`

### Configuration

- Xác nhận `Stores > Configuration > Catalog > ReturnShield > General > Enable Module` đang bật.
- Xác nhận category ID lists khớp với category IDs thực tế được gán cho products.
- Xác nhận threshold và penalty values không bị đặt quá cao khiến gần như mọi thứ đều nằm dưới medium threshold.

### Product page not showing expected warnings

- Xác nhận product page có `current_product` hợp lệ trong Magento context.
- Xác nhận product thực sự nằm trong các category được cấu hình trong ReturnShield.
- Xác nhận product có các field bị thiếu hoặc review/rating signals có thể kích hoạt penalties.
- Lưu ý rằng PDP hiện đã render low-risk snapshots ngay cả khi score là `0`, nên nếu block biến mất hoàn toàn thì thường là:
  - module bị disable
  - layout cache đã cũ
  - product context bị thiếu

### Cart block not rendering

- Xác nhận quote có visible items.
- Guest session với cart rỗng sẽ không render cart block.
- Nếu cart có items nhưng không có medium-risk items, block vẫn nên render dưới dạng low-risk snapshot.

### Dashboard empty

- Xác nhận module đang bật.
- Xác nhận có ít nhất một analyzed product đạt medium threshold.
- Giảm tạm medium threshold nếu cần validate output nhanh.

### Cache after template/layout changes

- Clean layout và block HTML cache sau khi sửa layout XML hoặc `.phtml` files:

  ```bash
  bin/magento cache:clean layout block_html config
  ```

- Nếu thay đổi CSS không thấy ở production mode, hãy redeploy static content.

### Review-based signals

- Xác nhận reviews đã được approve.
- Xác nhận keywords trong config match với substrings trong latest review detail text.
- Implementation hiện tại quét tối đa năm approved review records mới nhất.
