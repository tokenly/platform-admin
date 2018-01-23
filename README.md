A platform administration module for Tokenly services using the Laravel application framework.

## Installation

- Install with `composer require tokenly/platform-admin` 
- Install the Laravel Collective HTML package.  Follow the installation instructions at https://laravelcollective.com/docs/master/html.
- Add `Tokenly\PlatformAdmin\Provider\PlatformAdminServiceProvider::class,` to your list of service providers
- Publish the config with `./artisan vendor:publish --provider="Tokenly\PlatformAdmin\Provider\PlatformAdminServiceProvider"`
- Run the new migrations with `./artisan migrate`
- add `PLATFORM_ADMIN_ENABLED=true` to your `.env` file

## Promote an admin

- make sure these environment variables are set in your `.env` file
    `PLATFORM_CONTROL_ENABLED=1`
    `PLATFORM_CONTROL_AUTH_USERNAME=myusername`
    `PLATFORM_CONTROL_AUTH_PASSWORD=s3kreTP@$$w0rd`
- set `PLATFORM_CONTROL_PROMOTE_ADMIN_ENABLED=1` in your `.env` file
- visit `https://mysite.com/platform/control/promote-platform-admin?email=leroyjenkins@email.com` to make user leroyjenkins a platform admin
- set `PLATFORM_CONTROL_PROMOTE_ADMIN_ENABLED=0` in your `.env` file

## Optional environment vars

```ini
PLATFORM_ADMIN_REDIRECT_TO="/home"
```

## Usage

Create or modify a user to have the platformAdmin privilege
Visit http://mysite.com/platform/admin to see the admin
