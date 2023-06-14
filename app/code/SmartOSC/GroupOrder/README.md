# Magento 2 Group Order extension

Group Order Extension is based on the idea of Share Cart Extension

Group Order Extension helps customers in ordering their shopping cart with friends and family as well. This is a supportive method to promote store’s conversion rate via the existing users, and this can significantly contribute to the revenue of the store.

- Share shopping cart quickly
- Shortly review purchasing cart

## 1. Documentation

- [Contribute on Github](https://github.com/gnocman/m246/tree/main/app/code/SmartOSC/GroupOrder)

## 2. FAQs

**Q: I got an error: Mageplaza_Core has been already defined**

A: Read solution [here](https://github.com/mageplaza/module-core/issues/3)

**Q: How can customers use share button?**

A: Customers only need to click on the button and paste the automated URL to anywhere they want to share.

**Q: Where will the Share button appear on the website?**

A: Share button can be seen on **Minicart** and **Shopping Cart** page.

**Q: What if I want to inform customers that the price possibly will change later?**

A: You can leave a message on **Warning Message** box (from the admin backend).

## 3. How to install Group Order extension for Magento 2

- Install via composer (recommend)

Run the following command in Magento 2 root folder:

With Marketing Automation (recommend):
```
composer require smartosc/module-group-order
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

## 4. Highlight Features

### Quick share by copy-and-paste

**Group Order Extension** allows the store owners to add an extra button which is **Group Order** while a customer is processing their purchasing.

The button can be displayed in the **Minicart** section and **Shopping Cart Page**. By clicking this button, the customer can copy their shopping cart’ s URL and paste to a destination just in the blink of an eye. When the URL recipient clicks on the shared URL, their current shopping cart will be automatically added with the same items.

![Quick share by copy-and-paste](https://github.com/gnocman/m246/assets/55309917/4e40e892-4e62-4542-8d90-cc5d35344e05)

## 5. More features

### Update function

**Update** button is for updating the shopping cart with the latest changes from the original cart.

### Mobile responsive ability

The module is properly responsive with both mobile and desktop devices.

## 6. Full Magento 2 Group Order Features

### For store owners
- Enable/disable the extension

### For customers
- Quickly and easily share the shopping cart
- Briefly view the shared shopping cart

## 7. How to configure Group Order in Magento 2

### 7.1 Configuration

- Access to your Magento 2 Admin Panel, navigate to `Store tab > Open Settings > Configuration `
- Click `Mageplaza Extensions > Group Order > Configuration`, go to `General Configuration` section.

![Magento 2 Group Order extension configure](https://github.com/gnocman/m246/assets/55309917/287f6d41-ecca-47f7-b5b1-3dd9a1f40f7f)

#### 7.1.1. General

- **Enable**: Select `Yes` to enable the extension
```
php bin/magento c:f
```

### 7.2 Frontend
**IMPORTANT NOTE:** Customer must login and add 1 product to the cart for the button 'Create A Group Order' to be displayed

After activating the module, customers can use **Group Order** button to deliver the URL to people which they want to share the cart. After sharing, there will be already-added items in the cart of the URL recipient.

- **Group Order** button displays in the **Minicart** section when adding items to cart.

![Magento 2 Group Order module](https://github.com/gnocman/m246/assets/55309917/4e40e892-4e62-4542-8d90-cc5d35344e05)

- After adding items to the cart with Group Order, those who have the share link will see the products in the shared cart.
- Required to login to be able to add items to Group Order.

- To see what items the cart has added, in **Minicart** we click the **View Cart Group Order** button and can see the names of people who have added items to the Group Order

![View Cart  Group Order](https://github.com/gnocman/m246/assets/55309917/5a1cdb40-9f99-4c55-bbee-ca8ec4269b50)
