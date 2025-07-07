# Not-Laravel

A lightweight setup using core Laravel components like routing, controllers, and Blade—not a full framework, but just enough to build small apps and APIs without the full Laravel stack.

### 📂 Project Structure

```
not-laravel/
├── app/                # App logic & controllers
│   └── Controllers/
│        └── HomeController.php
├── public/             # Entry point (bootstrapping happens here)
│   └── index.php
├── routes/             # Route definitions
│   └── web.php
├── views/              # Blade templates
│   └── home.blade.php
├── composer.json        # Dependencies
└── README.md

```

### 🚀 Getting Started

1. **Clone the repo** 
   ```bash
   git clone git@github.com:levintoo/not-laravel.git
   ```
2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Run the server**

   ```bash
   php -S localhost:8000 -t public
   ```

4. **Visit in browser**

    * [http://localhost:8000](http://localhost:8000)


### 🛠 Example Routes

Define clean and simple routes with controller actions or inline closures:

```php
$router->get('/', [HomeController::class, 'index']);

$router->get('/hello/{name}', function ($name) {
    return new Response("Hello, $name");
});

$router->get('/json', function () {
    return new JsonResponse(['message' => 'Hello API']);
});
```

### ⚡ Why?

Laravel is amazing, but sometimes you just want a **barebones setup**:

* No ORM
* No queue system
* No extra layers
