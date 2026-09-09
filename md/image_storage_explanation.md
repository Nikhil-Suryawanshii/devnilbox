# Product Image Upload & Storage Flow

This document provides a comprehensive step-by-step breakdown of how the product image upload functionality works in this project. It covers the complete lifecycle of an image: from the moment it is uploaded, to where it physically lives on the server, to how it gets properly deleted when replaced.

## 1. The Upload Flow (Step-by-Step)

When a Seller or Admin uploads or updates a product image (like a thumbnail), the data follows this exact path through the code:

### Step 1: The Controller
The form submission is received by the relevant controller (e.g., `Admin\ProductController` or `API\Seller\ProductController`). The controller passes the raw request data directly into the Product Repository.

### Step 2: The Product Repository
Inside `app/Repositories/ProductRepository.php`, the system checks if a new image was uploaded (`$request->hasFile('thumbnail')`).
- If it's a **brand new product**, it calls `MediaRepository::storeByRequest()`.
- If it's an **updating product**, it calls `MediaRepository::updateByRequest()` to replace the old image.

### Step 3: The Media Repository
Inside `app/Repositories/MediaRepository.php`, the actual saving of the physical file happens. The code uses Laravel's Storage engine:
```php
$path = Storage::disk('public')->put('/'.trim($path, '/'), $file);
```
- It generates a secure, random string for the filename (e.g., `random_hash.jpg`).
- It physically saves the file to the custom storage location.

### Step 4: Database Storage
Finally, `MediaRepository` inserts a new row into the `media` database table. The `$media->src` column stores the relative path (e.g., `products/random_hash.jpg`). The returned `media_id` is then linked to the product in the `products` table.

---

## 2. The Custom "Direct Storage" Configuration

By default, Laravel relies on symbolic links (`php artisan storage:link`) to connect a hidden `storage/app/public` folder to a visible `public/storage` folder. 

However, this project is configured to use a **Custom Direct Storage Flow**. Images are stored *directly* into the physical `public/storage` directory, completely bypassing the need for symbolic links.

### How this was configured:
In `config/filesystems.php`, the `public` disk was modified to point directly to the public folder:
```php
'public' => [
    'driver' => 'local',
    'root' => public_path('storage'), // <--- Direct path to public/storage
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],
```
**Final Physical Location**: 
When an image is uploaded, it lands immediately in: 
`/Users/nikhilsuryawanshi/Project/devnilbox/public/storage/products/random_hash.jpg`

---

## 3. Image Replacement & Deletion Fix

When a user replaces an old product image with a new one, the old image must be deleted from the server to save space. 

### The Bug
Previously, the code used `Storage::delete($media->src)`. Because it didn't specify the `public` disk, Laravel defaulted to its hidden `local` disk (`storage/app/`) and failed to find the image, leaving the old image permanently stranded on the server.

### The Fix
To ensure the old flow is perfectly maintained, the deletion logic was updated across the project to explicitly target the `public` disk.

```php
if (Storage::disk('public')->exists($media->src)) {
    Storage::disk('public')->delete($media->src);
}
```

### Files updated to support this fix:
- `app/Repositories/MediaRepository.php`
- `app/Repositories/ProductRepository.php`
- `app/Http/Controllers/Admin/ProductController.php`
- `app/Http/Controllers/Shop/ProductController.php`
- `app/Http/Controllers/API/Seller/ProductController.php`
- `resources/views/shop/product/edit.blade.php` (Fixed frontend views that were hardcoded to default local disk)
- `app/Support/PublicMedia.php` (Fixed the helper class that was hardcoded to look in `storage/app/public` instead of dynamically using the `public` disk configuration. This was causing valid images to be hidden and replaced with default placeholders!)

**Conclusion**: Your image upload functionality perfectly supports your custom direct-to-public physical folder flow. New images appear instantly without symlinks, and old images are cleanly deleted when updated.
