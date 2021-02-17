## Coupons

This endpoint allows you to `list`, `create`, `retrieve`, `update` and `delete` your coupons.

The endpoint responses are formatted according to the [JSON:API specification](https://jsonapi.org).

### The Coupon object

#### Attributes

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `name`           | `string`  | **Required**. The coupon's name         |
| `code`           | `string`  | **Required**. The coupon's code.         |
| `type`           | `character`  | **Required**. F for fixed value, P for percentage         |
| `discount`           | `float`  | **Required**. The discount value or percentage         |
| `min_total`           | `float`  | The minimum basket total for the coupon to be valid        |
| `redemptions`           | `integer`  | *Required**. The total number of times this coupon can be redeemed       |
| `customer_redemptions`           | `integer`  | *Required**. The number of times a specific customer can redeem this coupon      |
| `description`           | `string`  | A description of the coupon         |
| `status`           | `boolean`  | Has the value `true` if the coupon is enabled or the value `false` if the coupon is disabled.         |

#### Coupon object example

```json
{
  "name": "Appetizer",
  "permalink_slug": "appetizer",
  "parent_id": null,
  "locations": [],
  "priority": null,
  "status": true,
  "description": "Sed consequat, sapien in scelerisque egestas",
  "thumb": null
}
```

### List coupons

Returns a list of coupons you’ve previously created.

Required abilities: `coupons:read`

```
GET /api/coupons
```

#### Parameters

| Key                  | Type      | Description          |
| -------------------- | --------- | ------------------------- |
| `page`           | `integer`  | The page number.         |
| `pageLimit`           | `integer`  | The number of items per page.         |
| `include`           | `string`  | What relations to include in the response. Options are `media`, `menus`, `locations`. To include multiple seperate by comma (e.g. ?include=media,menus) |

#### Response

```html
Status: 200 OK
```

```json
{
  "data": [
    {
      "type": "coupons",
      "id": "1",
      "attributes": {
        "name": "Appetizer",
        "permalink_slug": "appetizer",
        "parent_id": null,
        "priority": null,
        "status": true,
        "description": "Sed consequat, sapien in scelerisque egestas",
        "thumb": null,
        "media": [...],
        "menus": [...],
        "locations": [...]
      },
      "relationships": {
        "menus": {
          "data": [...]
        },
        "locations": {
          "data": [...]
        }
      }
    }
  ],
  "included": [
    ...
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
    "self": "https://your.url/api/coupons?page=1",
    "first": "https://your.url/api/coupons?page=1",
    "last": "https://your.url/api/coupons?page=1"
  }
}
```

### Create a coupon

Creates a new coupon.

Required abilities: `coupons:write`

```
POST /api/coupons
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `name`           | `string`  | **Required**. The coupon's name         |
| `permalink_slug`           | `string`  | The coupon's permalink slug.         |
| `parent_id`           | `integer`  | The Unique identifier of the parent coupon, if any.         |
| `locations`           | `array`  | The coupon's locations, if any.         |
| `priority`           | `integer`  | The coupon's priority.         |
| `status`           | `boolean`  | Has the value `true` if the coupon is enabled or the value `false` if the coupon is disabled.         |
| `description`           | `string`  | An arbitrary string attached to the coupon.         |
| `thumb`           | `string`  | The URL where the coupon's thumbnail can be accessed.         |

#### Payload example

```json
{
  "name": "Appetizer",
  "status": true
}
```

#### Response

```html
Status: 201 Created
```

```json
{
  "data": [
    {
      "type": "coupons",
      "id": "1",
      "attributes": {
        "name": "Appetizer",
        "permalink_slug": "appetizer",
        "parent_id": null,
        "priority": null,
        "status": true,
        "description": "Sed consequat, sapien in scelerisque egestas",
        "thumb": null,
      }
    }
  ]
}
```

### Retrieve a coupon

Retrieves a coupon.

Required abilities: `coupons:read`

```
GET /api/coupons/:coupon_id
```

#### Parameters

| Key                  | Type      | Description          |
| -------------------- | --------- | ------------------------- |
| `include`           | `string`  | What relations to include in the response. Options are `media`, `menus`, `locations`. To include multiple seperate by comma (e.g. ?include=media,menus) |

#### Response

```html
Status: 200 OK
```

```json
{
  "data": [
    {
      "type": "coupons",
      "id": "1",
      "attributes": {
        "name": "Appetizer",
        "permalink_slug": "appetizer",
        "parent_id": null,
        "priority": null,
        "status": true,
        "description": "Sed consequat, sapien in scelerisque egestas",
        "thumb": null,
        "media": [...],
        "menus": [...],
        "locations": [...]
      },
      "relationships": {
        "menus": {
          "data": [...]
        },
        "locations": {
          "data": [...]
        }
      }
    }
  ],
  "included": [
	...
  ]
}
```

### Update a coupon

Updates a coupon.

Required abilities: `coupons:write`

```
PATCH /api/coupons/:coupon_id
```

#### Parameters

| Key                  | Type      | Description                                                  |
| -------------------- | --------- | ------------------------------------------------------------ |
| `name`           | `string`  | **Required**. The coupon's name         |
| `permalink_slug`           | `string`  | The coupon's permalink slug.         |
| `parent_id`           | `integer`  | The Unique identifier of the parent coupon, if any.         |
| `locations`           | `array`  | The coupon's locations, if any.         |
| `priority`           | `integer`  | The coupon's priority.         |
| `status`           | `boolean`  | Has the value `true` if the coupon is enabled or the value `false` if the coupon is disabled.         |
| `description`           | `string`  | An arbitrary string attached to the coupon.         |
| `thumb`           | `string`  | The URL where the coupon's thumbnail can be accessed.         |

#### Payload example

```json
{
  "description": "Vivamus interdum erat ac aliquam porttitor. ",
  "parent_id": 2
}
```

#### Response

```html
Status: 200 OK
```

```json
{
  "data": [
    {
      "type": "coupons",
      "id": "1",
      "attributes": {
        "name": "Appetizer",
        "permalink_slug": "appetizer",
        "parent_id": null,
        "priority": null,
        "status": true,
        "description": "Sed consequat, sapien in scelerisque egestas",
        "thumb": null,
      }
    }
  ]
}
```

### Delete a coupon

Permanently deletes a coupon. It cannot be undone.

Required abilities: `coupons:write`

```
DELETE /api/coupons/:coupon_id
```

#### Parameters

No parameters.

#### Response

Returns an object with a deleted parameter on success. If the coupon ID does not exist, this call returns an error.

```html
Status: 200 OK
```

```json
{
  "id": 1,
  "object": "coupon",
  "deleted": true
}
```
