reated an restufl API app for a sample  store.

The app has the following endpoints:

GET /api/products = lists all products 

GET /api/product/{id} = gets product details POST /api/product = adds a product, expects the following type of payload: { "name": "milk", "category": "diary", "sku": "A0001", "price": 69.99}

POST /api/product/{id} = updates a product, expects the following type of payload: { "name": "skim milk", "category": "dairy", "sku": "A0001", "price": 69.99 }

PUT /api/product/{id} = updates a product, expects the following type of payload: { "name": "skim milk", "category": "dairy", "sku": "A0001", "price": 69.99 }

DELETE /api/product/{id} = deletes a product

Category endpoints:

GET /api/categories = lists all categories and related products GET /api/category/{id} = gets category 

POST /api/category = adds a category, expects the following type of payload: { "name": "Category name", }

PUT /api/category/{id} = updates a category, expects the following type of payload: { "name": "Category name", }

DELETE /api/category/{id} = deletes a category

NOTE: I did a mistake and created one to many product category relationship instead of many to many. I didn't have time to fix that.

