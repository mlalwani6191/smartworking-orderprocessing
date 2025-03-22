
# Smartworking Customer Order Processing Module for Magento2

Custom Magento2 Module for Order processing status via API




## Setup and install 
1. Unzip the Module inside app/code
2. Run php bin/magento setup:upgrade
3. Make sure Module is enabled
4. Generate Auth Key from Admin
    Goto System >> Integration >> Create new Integration >> select "Smartworking - Order Status update via API" 
5. Use Access Token as Bearer Auth in Postman


## API Reference

#### Update Order status

```http
  PUT V1/orderprocessing/changestatus/
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `bearer token` | `string` | **Required**. Integration Token generated in setup section
|Body  | JSON |         
                        {
                        "orders":[
                            {
                                "order_id":"000000013",
                                "status":"shipped"
                            },
                            {
                                "order_id":"00000009",
                                "status":"processing"
                            }
                        ]
                        }

