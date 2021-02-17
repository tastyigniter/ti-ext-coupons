## Coupon history

This endpoint allows you to `list` coupon history.

### The history object

The history object contains key information about the coupon transaction. 

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `coupon_id`           | `integer`  | The coupon id associated with the history log     
| `order_id`           | `integer`  | The order id associated with the history log        |
| `customer_id`           | `integer `  | The customer id associated with the history log         |
| `code`           | `string `  | The code used in the transaction        |
| `amount`           | `float `  | The total value of the order        |
| `min_total`           | `float`  | The minimum total required for the order   |
| `date_used`           | `datetime`  | Timestamp of transaction
| `status`           | `boolean`  | Defaults to true, might change in future   |

#### History object example

```json
{
  "coupon_id": 1,
  "order_id": 1,
  "customer_id": 1,
  "code": "discount-code",
  "amount": 100.00,
  "min_total": 0,
  "date_used": "2020-12-20 09:00:00",
  "status": true
}
```

### List history

Returns a list of coupon history.

Required abilities: `couponhistory:read`

```
GET /api/couponhistory
```

#### Parameters

| Key                  | Type      | Description          |
| -------------------- | --------- | ------------------------- |
| `page`           | `integer`  | The page number.         |
| `pageLimit`           | `integer`  | The number of items per page.         |
| `order_id`           | `integer`  | The order ID you want to view history for         |
| `customer_id`           | `integer`  | The customer ID you want to view history for         |


#### Response

```html
Status: 201 Created
```

```json
{
  "data": [
    {
      "type": "couponhistory",
      "id": "1",
      "attributes": {
         "coupon_id": 1,
         "order_id": 1,
         "customer_id": 1,
         "code": "discount-code",
         "amount": 100.00,
         "min_total": 0,
         "date_used": "2020-12-20 09:00:00",
         "status": true
      }
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "count": 1,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 1
    }
  },
  "links": {
    "self": "https://your.url/api/couponhistory?page=1",
    "first": "https://your.url/api/couponhistory?page=1",
    "last": "https://your.url/api/couponhistory?page=1"
  }
]
```
