# Smartworking Customer Order Processing Module for Magento2
Magento2 extension build to process orders( change status) via API

## Setup and install 
1. Unzip the Module inside app/code
2. Run Magento commands (php bin/magento setup:upgrade, php bin/magento s:d:c, php bin/magento c:f )
3. Make sure the module is enabled
4. Generate Auth Key from Admin
    Goto System >> Integration >> Create new Integration >> select "Smartworking - Order Status update via API" 
5. Use Access Token as Bearer Auth in Postman
6. Make sure standalone auth token execution is enabled on configuration (bin/magento config:set oauth/consumer/enable_integration_as_bearer 1)
7. Make sure Queue processes are running  - php bin/magento queue:consumer:start smartworking.customorderprocessing

## API Reference

#### Update Order status

**URL** : <br />
rest/V1/orderprocessing/changestatus/ <br />
**Type** :  <br />
PUT
**Authentication**: <br />bearer token 
**Body** <br />
```
{
    "request": {
        "orders": [
            {
                "order_id": "000000009",
                "status": "shipped"
            },
            {
                "order_id": "00000001qwqw3",
                "status": "shipped"
            }
        ],
        "synchronous": false
    }
}
```
**Architectural decision details**
1. The module is designed and developed considering single-order processing("synchronous": false) and Multiple order processing ("synchronous": true).
2. When processing multiple orders one must pass ("synchronous": false) in the API which enables processing of the orders in the background through the Queue system ensuring API response time is not increased with the increase in the number of orders to be processed
3. When orders are updated via queue all the actions are registered in Magento's logs file to make sure users do not miss any updates and the same can be communicated to the system  calling the API
4. The module has been developed with a headless approach, making it ideal for the Hyva theme.
5. All the changes in the order status are recorded in order_status_change_log table 


