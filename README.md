A platform administration module for Tokenly services using the Laravel application framework.

## Installation

- `composer require tokenly/platform-admin`
- Add `Tokenly\PlatformAdmin\Provider\PlatformAdminServiceProvider::class,` to your list of service providers
- Publish the config with `./artisan vendor:publish --provider="Tokenly\PlatformAdmin\Provider\PlatformAdminServiceProvider"`
- Run the new migrations with `./artisan migrate`
- add `PLATFORM_ADMIN_ENABLED=true` to your `.env` file


## Usage

Create or modify a user to have the platformAdmin privilege
Visit http://mysite.com/platform/admin to see the admin