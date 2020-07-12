# Kwalbum Without Albums
It's Kwalbum, which was a digital photo album in the early 2000s, except without albums.
Photos are groups by location, date, people, and tags instead.

## Installation
1. Install Koseven from https://github.com/koseven/koseven
1. Make sure Koseven's database module has been enabled and configured.
1. `npm install` inside the kwalbum directory
1. Move the kwalbum directory to Koseven's modules directory.
1. Configure Koseven to use the Kwalbum module.
   In your app/bootstrap.php file add
   `'kwalbum' => MODPATH.'kwalbum',`
   after
   `Koseven::modules(array(`
   Also make sure that
   `'database'   => MODPATH.'database',   // Database access`
   and
   `'image'      => MODPATH.'image',      // Image manipulation`
   are uncommented and enabled for those modules.
1. Create a writable directory for items to be uploaded to.  The default
   configured location is application/items
1. Optionally, copy modules/kwalbum/config/kwalbum.php to
   app/config/kwalbum.php and edit.  The reason to copy the
   file to app/config is to ensure it is not overwritten when the
   Kwalbum module is updated.
1. Open Kwalbum in your web browser and go to the install page.
   If Kwalbum is at http://localhost/kwalbum then the install page
   is at http://localhost/kwalbum/~install

## Requirements
- Koseven 3.3.9+
- PHP 7.1+
- GD module for PHP
- MySQL 5.1+
