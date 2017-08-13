# Installation

Fresa can be added to an existing WordPress theme or plugin by using [Composer](https://getcomposer.org) to add the package:

    composer require jplhomer/fresa

This will add Fresa to your project and install any necessary dependencies.

## Requirements

- Composer
- PHP 7.0+
- WordPress 4.8+
_Older versions of WordPress are untested, but they may work._

## WordPress Integration

Adding Fresa to your WordPress project is a breeze. If you are developing a plugin, you can install it directly into the plugin folder using the Composer command above. The last step is to import Composer's generated `autoload.php` file:

    // your-plugin-root-file.php
    require __DIR__ . '/vendor/autoload.php';

If you are developing a theme, you can run the Composer command inside the theme folder and then do the same thing, only in `functions.php`:

    // functions.php
    require __DIR__ . '/vendor/autoload.php';

## A Note About Dependencies

Typically when you are developing a project, it is not recommended to track your `vendor` folder in version control and instead to import dependencies at build time.

However, due to the nature of WordPress, you cannot ensure that dependencies in a nested directory are installed without writing a custom build script.

It may be easier to add the `vendor` folder to version control in this case.
