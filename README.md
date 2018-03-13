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
PLATFORM_CONSOLE_QUEUE_COMMANDS=true
```

## Running console commands

A few console commands are available to run from the platform admin.  To enable application-specific commands to be run from the platform admin, implement the `Tokenly\PlatformAdmin\Console\Contracts\RunsInPlatformAdmin` interface in your command class.

The platform admin supports long-running console commands through the use of a queue.  To support long-running console commands, you must:
1) Enable pusher broadcasting.
2) Run a background process that processes the `platform_artisan_command` queue.
3) Set the `PLATFORM_CONSOLE_QUEUE_COMMANDS` environment variable to true.

## Usage

Create or modify a user to have the platformAdmin privilege
Visit http://mysite.com/platform/admin to see the admin
