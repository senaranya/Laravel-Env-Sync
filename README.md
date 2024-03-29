[![Tests](https://github.com/senaranya/Laravel-Env-Sync/workflows/Tests/badge.svg?branch=master&event=push)](https://github.com/senaranya/Laravel-Env-Sync/actions?query=branch%3Amaster)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/senaranya/Laravel-Env-Sync.svg?maxAge=3600)](https://scrutinizer-ci.com/g/senaranya/Laravel-Env-Sync/?branch=master)
![Scrutinizer coverage](https://img.shields.io/scrutinizer/coverage/g/senaranya/Laravel-Env-Sync?style=plastic)

# Laravel Env Sync

Keep your .env in sync with your .env.example or vice versa.

It reads the .env.example file and makes suggestions to fill your .env accordingly. 

## Installation via Composer

Start by requiring the package with composer

```
composer require aranyasen/laravel-env-sync
```


## Usage

### Sync your dotenv files

You can populate your .env file from the .env.example by using the `php artisan env:sync` command.

The command will tell you if there's anything not in sync between your files and will propose values to add into the .env file.

You can launch the command with the option `--reverse` to fill the .env.example file from the .env file

You can also use `--src` and `--dest` to specify which file you want to use. You must use either both flags, or none.

If you use the `--no-interaction` flag, the command will copy all new keys with their default values.

### Check for diff in your dotenv files

You can check if your .env is missing some variables from your .env.example by using the `php artisan env:check` command.

The command simply show you which keys are not present in your .env file. This command will return 0 if your files are in sync, and 1 if they are not, so you can use this in a script

Again, you can launch the command with the option `--reverse` or with `--src` and `--dest`.

The command will also dispatch event `Aranyasen\LaravelEnvSync\Events\MissingEnvVars`, which will contain the missing env variables, which could be used in automatic deployments. Event is only fired when there are missing env variables.

### Show diff between your dotenv files

You can show a table that compares the content of your env files by using the `php artisan env:diff` command.

The command will print a table that compares the content of both .env and .env.example files, and will highlight the missing keys.

You can launch the command with the options `--src` and `--dest`.
