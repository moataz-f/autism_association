# InfinityFree Deployment Guide

The project has been configured for production (`APP_ENV=prod`). Follow these steps to deploy effectively:

## 1. Database Setup on InfinityFree
1. Log in to your InfinityFree control panel.
2. Go to **MySQL Databases**.
3. Create a new database (e.g., `volunteering_db`).
4. Copy the connection details:
   - **DB_HOST**: (e.g., `sql305.infinityfree.com`)
   - **DB_USER**: (Your generated username)
   - **DB_PASS**: (Your account password)
   - **DB_NAME**: (The name of the database you created)

## 2. Update Configuration
Update your `.env` file (or create a `.env.local` on the server) with the new database URL:
```
DATABASE_URL="mysql://if0_41101147:muCTqN1wLvT@sql209.infinityfree.com:3306/if0_41101147_autism?serverVersion=10.11&charset=utf8mb4"
```
*Note: The project is already configured for production (`APP_ENV=prod`).*

## 3. Upload Files via FTP
1. Use an FTP client (like **FileZilla**).
2. Upload **all project files** into the `htdocs` folder on InfinityFree.
   > [!IMPORTANT]
   > Make sure to upload hidden files like `.htaccess` and `.env`.

## 4. Import Database
1. Export your local database as a `.sql` file.
2. In InfinityFree, go to **phpMyAdmin**.
3. Select your database and **Import** the `.sql` file.

## Troubleshooting 403 Forbidden
If you see a "403 Forbidden" error, it's usually because the `.htaccess` files are missing on the server.

1. **Hidden Files**: In FileZilla, go to **Server** -> **Force showing hidden files**.
2. **Missing .htaccess**: Ensure there is a `.htaccess` file in your root folder AND inside the `public/` folder.
3. **Check index.php**: Verify that `public/index.php` exists on the server.
4. **Folder Permissions**: Make sure the folders on the server have `755` permissions and files have `644`.

## Notes
- **PHP Version**: Ensure InfinityFree is set to PHP 8.1 or higher.
- **Cache**: If changes don't appear, delete everything inside `var/cache/`.
