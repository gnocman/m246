# Module SmartOSC GraphQl

    ``smartosc/module-graphql``

## Main Functionalities
Return according customer info and all of that customer orders. 

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/SmartOSC`
 - Enable the module by running `php bin/magento module:enable SmartOSC_GraphQl`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public GitHub repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require smartosc/module-graphql`
 - enable the module by running `php bin/magento module:enable SmartOSC_GraphQl`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: GraphQl
Request:

    query {
        customer_orders(customer_email: "giochem22@gmail.com") {
            customer_name
            customer_email
            orders {
                increment_id
                order_status
                order_grand_total
            }
            customer_total_amount
        }
    }


Response:

    {
        "data": {
            "customer_orders": {
                "customer_name": "Nam Công",
                "customer_email": "giochem22@gmail.com",
                "orders": [
                    {
                        "increment_id": "000000039",
                        "order_status": "pending",
                        "order_grand_total": 105
                    },
                    {
                        "increment_id": "000000040",
                        "order_status": "complete",
                        "order_grand_total": 105
                    },
                    {
                        "increment_id": "000000041",
                        "order_status": "pending",
                        "order_grand_total": 105
                    }
                ],
                "customer_total_amount": 315
            }
        }
    }
