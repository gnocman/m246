Request:

    query {
        getOrderCrossSell(orderId: 158) {
            items {
                sku
                name
                price
                image
            }
        }
    }

Response:

    {
        "data": {
            "getOrderCrossSell": {
                "items": [
                    {
                        "sku": "Test Configurable",
                        "name": "Test Configurable",
                        "price": 100,
                        "image": "/l/o/logo-2019.png"
                    },
                    {
                        "sku": "Alexander Mcqueen Hybrid Leather Sneaker",
                        "name": "Alexander Mcqueen Hybrid Leather Sneaker",
                        "price": 1234,
                        "image": "/6/2/627225whz62-alexandermcqueen-sneakers-01-768x1024.jpg"
                    },
                    {
                        "sku": "Louis Vuitton LV Trainers",
                        "name": "Louis Vuitton LV Trainers",
                        "price": 9999,
                        "image": "/1/a/1a9jg9-louis-vuitton-sneakers-01.jpg"
                    }
                ]
            }
        }
    }
