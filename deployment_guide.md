# cPanel / Shared Hosting Deployment Guide

This guide describes how to deploy the Laravel 11 project to a standard cPanel shared hosting environment with phpMyAdmin (MySQL).

---

## Prerequisites
- Hosting plan supporting **PHP 8.2+** with extensions enabled: `openssl`, `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `intl`.
- MySQL Database created via cPanel Database Wizard.

---

## Method 1: The Symbolic Link Method (Recommended & Clean)

If you have SSH access or can run a PHP script to create symlinks, this keeps your project structure clean and identical to local development.

### Step 1: Upload the Code
1. Zip your entire project folder **excluding** the `node_modules` and `vendor` directories.
2. Upload the zip file using cPanel File Manager to your hosting home directory (e.g., `/home/your_username/dakhoacantho_web`).
3. Extract the zip file there.

### Step 2: Create the Symlink
If your hosting main domain points to `/home/your_username/public_html`, you need to direct it to the Laravel `/public` folder.
* **If you have SSH access**, run:
  ```bash
  rmdir /home/your_username/public_html
  ln -s /home/your_username/dakhoacantho_web/public /home/your_username/public_html
  ```
* **If you do NOT have SSH access**, create a file named `symlink.php` inside `/home/your_username/public_html` with the following content:
  ```php
  <?php
  symlink('/home/your_username/dakhoacantho_web/public', '/home/your_username/public_html/temp_public');
  // Then move files manually or rename public_html using File Manager.
  ```
  Alternatively, you can delete `public_html` (if empty) and create a symlink using cPanel Cron Jobs by executing:
  ```bash
  ln -s /home/your_username/dakhoacantho_web/public /home/your_username/public_html
  ```
  Run the cron job once, and delete it immediately after it completes.

---

## Method 2: The Folder Split Method (Most Secure, Works Everywhere)

This is the standard approach for shared hostings that do not support SSH or custom symbolic links.

### Step 1: Structure Uploads
1. Create a folder named `dakhoacantho_core` in your home directory (`/home/your_username/dakhoacantho_core`).
2. Upload all files and folders of the Laravel project **except the `public` folder** into `dakhoacantho_core`.
3. Upload the **contents** of the Laravel `public` folder directly into your hosting's `public_html` folder.

### Step 2: Configure Paths
Open `public_html/index.php` and edit the paths pointing to autoload and bootstrap files:

```php
// Change from:
// require __DIR__.'/../vendor/autoload.php';
// $app = require_once __DIR__.'/../bootstrap/app.php';

// Change to:
require __DIR__.'/../dakhoacantho_core/vendor/autoload.php';
$app = require_once __DIR__.'/../dakhoacantho_core/bootstrap/app.php';
```

For asset paths, add this helper to `dakhoacantho_core/app/Providers/AppServiceProvider.php` inside the `register` method to tell Laravel where public path resides:
```php
$this->app->bind('path.public', function() {
    return '/home/your_username/public_html';
});
```

---

## Step 3: Environment Setup (.env)
1. In cPanel MySQL Database Wizard, create a database, create a user, assign the user to the database, and grant all privileges.
2. Edit the `.env` file inside your core folder:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

---

## Step 4: Storage Link & Assets
To make Filament rich-text uploads and thumbnail images accessible on shared hosting:
* Create a symlink for storage inside `public_html`:
  ```bash
  ln -s /home/your_username/dakhoacantho_web/storage/app/public /home/your_username/public_html/storage
  ```
  Or create a PHP file `link.php` inside `public_html` and visit it in the browser:
  ```php
  <?php
  symlink('/home/your_username/dakhoacantho_web/storage/app/public', '/home/your_username/public_html/storage');
  echo "Storage linked successfully";
  ```
* Run `npm run build` locally and make sure to upload the `/public/build` folder to the server's public directory.
