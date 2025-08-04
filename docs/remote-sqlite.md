# Remote SQLite Database Configuration

This document explains how to configure and use a remote SQLite database with this Laravel application.

## Configuration

1. Open your `.env` file and update the remote SQLite database configuration:

```
# Remote SQLite Database Configuration
# Uncomment the line below to use remote SQLite by default
# DB_REMOTE_CONNECTION=sqlite_remote
DB_REMOTE_DATABASE=/path/to/remote/database.sqlite
# DB_REMOTE_FOREIGN_KEYS=true
# DB_REMOTE_BUSY_TIMEOUT=2000
# DB_REMOTE_JOURNAL_MODE=WAL
# DB_REMOTE_SYNCHRONOUS=NORMAL
```

Replace `/path/to/remote/database.sqlite` with the actual path to your remote SQLite database file. This could be:
- A network file system path (e.g., `/mnt/nfs/shared/database.sqlite`)
- A path to a file on a mounted remote filesystem
- A path accessible through other remote file access methods

## Usage in Code

You can switch between local and remote SQLite databases in your code using the `RemoteSqlite` facade:

```php
use App\Facades\RemoteSqlite;

// Switch to remote SQLite database
RemoteSqlite::use();

// Your database operations here...
$users = DB::table('users')->get();

// Switch back to local SQLite database
RemoteSqlite::useLocal();
```

You can also check which connection is currently being used:

```php
// Get current connection name
$connection = RemoteSqlite::current();

// Check if using remote connection
if (RemoteSqlite::isRemote()) {
    // Do something specific for remote connection
}
```

## Web Interface

The application provides web interfaces for managing both local and remote SQLite databases:

- Local SQLite database: `/sqlite`
- Remote SQLite database: `/sqlite-remote`

Both interfaces require authentication.

## Important Notes

1. SQLite is not designed for high-concurrency remote access. If you need a true client-server database, consider using MySQL, PostgreSQL, or another client-server database system.

2. Remote file access can be slow depending on network conditions. Consider the performance implications when using a remote SQLite database.

3. Make sure the remote SQLite database file is accessible and has the proper permissions for the web server user.

4. For production environments with multiple servers, a true client-server database is recommended instead of a remote SQLite file.
