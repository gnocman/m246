# NamCong_Loyalty

`NamCong_Loyalty` là module loyalty cho Magento 2 dùng để cộng điểm theo hành vi khách hàng, theo dõi lịch sử điểm và hạng thành viên, cho phép admin quản lý rule và reward, đồng thời cung cấp trang tài khoản để khách hàng xem điểm và redeem reward.

Tài liệu này mô tả đúng workflow đang được implement trong code hiện tại.

## Phạm vi module

Module hiện đang hỗ trợ:

- Lưu số dư điểm loyalty theo từng khách hàng
- Ghi lịch sử cộng, trừ và hết hạn điểm
- Phân hạng khách hàng: `bronze`, `silver`, `gold`
- Quản trị rule loyalty và reward trong admin
- Trang tài khoản khách hàng `My Loyalty Points`
- GraphQL query và mutation cho loyalty data
- REST route được khai báo trong `etc/webapi.xml`
- Cron job chạy hằng ngày để expire số dư không hoạt động

## Workflow nghiệp vụ hiện tại

### 1. Seed dữ liệu mặc định khi cài đặt hoặc upgrade

Khi chạy `php bin/magento setup:upgrade`, module sẽ tạo dữ liệu mặc định nếu chưa tồn tại:

- Rules
  - `Registration Bonus`
  - `Product Review Reward`
  - `Purchase Points`
- Rewards
  - `$5 Discount Voucher`
  - `$10 Discount Voucher`
  - `Free Shipping`
  - `Premium Gift Product`

Hiện tại phần seed đã được sửa để không chèn lặp lại và có thêm patch dọn dữ liệu mặc định bị duplicate từ các bản cũ.

### 2. Cách khách hàng được cộng điểm

Module lắng nghe 3 event Magento:

- `customer_register_success`
- `review_save_after`
- `sales_order_save_after`

Luồng cộng điểm:

1. Observer bắt được hành vi hỗ trợ.
2. `RuleEngine` load các rule đang active và kiểm tra:
   - action type
   - customer group
   - khoảng ngày hiệu lực qua repository
3. `PointManager` cập nhật số dư điểm và hạng khách hàng.
4. Một dòng lịch sử mới được ghi vào `loyalty_history`.

Chi tiết theo từng hành vi:

- Đăng ký tài khoản
  - Chạy sau khi đăng ký thành công.
  - Cộng tổng điểm từ các rule có action type `registration`.

- Viết review
  - Chạy khi review được save.
  - Chỉ cộng điểm khi review chuyển sang trạng thái `approved`.
  - Review đã approved trước đó sẽ không được cộng lại.

- Hoàn tất đơn hàng
  - Chạy khi order được save.
  - Chỉ cộng điểm khi state của order là `complete`.
  - Module chống cộng trùng bằng cách kiểm tra `loyalty_history` theo `order_id` và action type `order`.
  - Số điểm nhận được bằng:
    - `floor(base_grand_total)`
    - cộng thêm tổng điểm từ các rule loại `order`

## Workflow phân hạng

Hạng khách hàng được tính lại mỗi khi số dư điểm thay đổi.

- `bronze`: `0 - 499`
- `silver`: `500 - 1999`
- `gold`: `2000+`

Trang frontend cũng hiển thị progress tới hạng tiếp theo.

## Workflow redeem reward

Khi khách hàng redeem reward:

1. Module kiểm tra khách đã đăng nhập.
2. Load reward được chọn và xác nhận reward đang active.
3. Kiểm tra khách có đủ điểm hay không.
4. `RewardManager` tạo Magento sales rule coupon.
5. Chỉ sau khi tạo coupon thành công thì `PointManager` mới trừ điểm.
6. Module ghi một dòng lịch sử redemption kèm mã coupon.

Định dạng coupon sinh ra:

- `LOYALTY-XXXXXX`

Behavior hiện tại theo từng loại reward:

- `discount`
  - Tạo coupon giảm giá số tiền cố định.
- `free_shipping`
  - Tạo coupon và bật cờ free shipping trên sales rule.
- `gift_product`
  - Hiện tại vẫn đang được xử lý theo kiểu coupon/promotion placeholder.
  - Chưa tự động thêm sản phẩm quà tặng vào cart và chưa xử lý tồn kho quà.

## Workflow hết hạn điểm

Cron job `namcong_loyalty_expire_points` chạy mỗi ngày lúc `02:00`.

Logic hiện tại:

- Nếu `loyalty_points.updated_at` cũ hơn `365` ngày
- và khách vẫn còn số dư điểm dương
- module sẽ reset điểm về `0`
- tính lại hạng về `bronze`
- ghi một dòng `expiration` vào `loyalty_history`

Lưu ý quan trọng:

- Điểm hết hạn theo thời gian không hoạt động của toàn bộ số dư.
- Module chưa theo dõi hạn dùng riêng cho từng lần earn point.

## Workflow phía admin

Menu admin:

- `Marketing > Loyalty Program > Loyalty Rules`
- `Marketing > Loyalty Program > Rewards Management`
- `Marketing > Loyalty Program > Customer Points`

### Loyalty Rules

Admin có thể cấu hình:

- Tên rule
- Số điểm
- Action type
  - `order`
  - `registration`
  - `review`
- Trạng thái active
- Customer groups
- Từ ngày
- Đến ngày

### Rewards

Admin có thể cấu hình:

- Tên reward
- Số điểm yêu cầu
- Loại reward
- Giá trị reward
- Trạng thái active

### Customer Points

Màn hình admin hiện được dùng để theo dõi:

- Số dư điểm khách hàng
- Hạng hiện tại
- Dữ liệu loyalty liên quan trong module

## Workflow phía frontend

Module thêm link vào customer account navigation:

- `My Loyalty Points`

Route:

- `loyalty/account/index`

Dashboard của khách hàng hiện hiển thị:

- Tổng điểm hiện tại
- Hạng hiện tại
- Tiến độ lên hạng
- Lịch sử điểm gần đây
- Danh sách reward đang active để redeem

Danh sách reward active được load qua cache và cache sẽ được clear khi reward được save hoặc delete.

## API hiện có

### GraphQL

Query:

- `loyaltyCustomerPoints`
- `loyaltyRewards`

Mutation:

- `loyaltyRedeemReward`

### REST route được khai báo

- `GET /V1/loyalty/customer-points`
- `POST /V1/loyalty/apply-points`
- `GET /V1/loyalty/rewards`
- `POST /V1/loyalty/redeem`

## Cấu trúc dữ liệu

Module dùng 4 bảng custom:

- `loyalty_points`
  - lưu một dòng số dư cho mỗi customer
- `loyalty_history`
  - lưu lịch sử thay đổi điểm
- `loyalty_rule`
  - lưu rule cộng điểm do admin cấu hình
- `loyalty_reward`
  - lưu reward mà khách có thể redeem

## Cài đặt

Enable module và chạy các lệnh setup:

```bash
php bin/magento module:enable NamCong_Loyalty
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

Nếu module đã được cài ở bản cũ trước đó, chỉ cần chạy:

```bash
php bin/magento setup:upgrade
```

để áp dụng patch dọn duplicate default rules và rewards.

## Dependencies

Module phụ thuộc vào:

- `Magento_Customer`
- `Magento_Sales`
- `Magento_Catalog`
- `Magento_Review`
- `Magento_Store`
- `Magento_Backend`
- `Magento_Ui`

Yêu cầu PHP:

- `^8.1`

## Giới hạn hiện tại

- Reward fulfillment hiện vẫn là coupon-based, chưa phải engine quà tặng hoàn chỉnh.
- Điểm hết hạn đang tính theo `loyalty_points.updated_at`, không phải theo từng transaction earn point.
- Điểm đơn hàng chỉ được cộng khi order sang state `complete`, không phải lúc đặt hàng.
- REST route đã được khai báo, nhưng nếu project dùng nhiều integration ngoài hệ thống thì nên test lại payload và auth flow trong môi trường thực tế.

## Version

- Module name: `NamCong_Loyalty`
- Composer package: `namcong/module-loyalty`
- Version: `1.0.0`
