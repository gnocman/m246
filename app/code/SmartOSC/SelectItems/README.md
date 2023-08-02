# The Best Magento 2 Select Items In Checkout Cart Extension FREE

**Magento 2 Select Items In Checkout Cart** functionality with checkboxes enhances the shopping experience in Magento 2 by empowering customers with greater control over their cart contents during the checkout process.


## Highlight Features for Magento 2 Select Items In Checkout Cart
- Checkbox One item và Checkbox all
- Subtotal and Order Total update simultaneously when checkbox is checked
- Payment for selected items
- The items that are not selected will still remain in the cart when Place Order done



## 1. Magento 2 Select Items In Checkout Cart extension Documentation

- [Contribute on Github](https://github.com/gnocman/m246/tree/main/app/code/SmartOSC/SelectItems)
- [Contribute on Gitlab](https://gitlab.com/gnocman/magento2-select-items)



## 2. How to install Magento 2 Select Items In Checkout Cart extension

### Install module via composer (recommend)

Run the following command in Magento 2 root folder:

```
composer require smartosc/module-select-items
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```


## 3. Magento 2 Select Items In Checkout Cart extension FAQs

#### Q: I haven't selected any items, can I still 'Proceed to Checkout'?
A: Of course, that's an error. It will show an error message: "In order to proceed with the payment, you must select the items first."

#### Q: What do I need to do to buy all the items in the cart?
A: There is a checkbox labeled 'All' in the checkout/cart. Please check that checkbox, and it will select all items in the cart for you.


## 4. Contribute to this Select Items module

Feel free to **Fork** and contrinute to this module and create a pull request so we will merge your changes to `master` branch.


## 5. Magento 2 Select Items In Checkout Cart extension Introduction

In Magento 2, the "Select Items" functionality in the checkout cart is implemented using checkboxes. When you add products to your cart and proceed to the checkout page, each item in your cart will be displayed along with a corresponding checkbox. This checkbox allows you to select or deselect individual items for further actions, such as updating the quantity, removing from the cart, or applying specific discounts.

The "Select Items" checkboxes provide customers with the flexibility to customize their order before finalizing the purchase. By selecting specific items, customers can easily modify their cart contents based on their preferences, budget, or other factors.

To use the "Select Items" feature, customers can simply click on the checkboxes next to the products they wish to include or exclude from the checkout process. After making their selections, they can proceed with other checkout steps, such as providing shipping information, choosing payment options, and placing the order.


### Configuration

- Access to your Magento 2 Admin Panel, navigate to `Store tab > Open Settings > Configuration`
- Click `General > Select Items in Checkout Cart`, go to `General Configuration` section.

![Magento 2 Select Items in Checkout Cart extension configure](https://github.com/gnocman/m246/assets/55309917/188ae0e1-a520-4ef6-a7b4-8849c05870d6)

#### General

- **Enable**: Select `Yes` to enable the extension
```
php bin/magento c:f
```


### Frontend

- After you add items to your cart, you click on "View and Edit Cart" in the MiniCart, and you will be redirected to the checkout/cart page, where the Select Items interface will appear.

![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/448c1ec2-f130-45f8-b981-c485327497b7)

- Here, you will select the items you want to buy or can choose to select all items. In doing so, the Subtotal will be updated simultaneously as you select the items.

![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/083977ac-2582-46a9-80fe-3805881ca71f)
![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/7ef6e70d-b577-4435-a9a2-9c5fb1716e6f)


#### Proceed to Checkout

- At the checkout page, you will purchase the items that you have selected on the checkout/cart page.

![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/4003d6a5-6d90-4a64-8019-bb5cf03781e2)


#### Order Success

- At the thank you page, you will see the order number for your successful order, and in the MiniCart, it will display the items that you did not select.

![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/657317d5-a617-4f44-8b81-8a8fa4eadb92)


#### My Orders, Mail and Admin

- At the My Orders page, you will see the detailed order that you just selected the items you want to buy.
- Email and admin are also similar.

![Magento 2 Select Items](https://github.com/gnocman/m246/assets/55309917/ee3c7373-126d-470d-bf65-097a7cdb164f)
