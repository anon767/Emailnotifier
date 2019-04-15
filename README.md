# Emailnotifier Chrome Extension

Get notified if someone reads your Emails.
This extension attaches a invisible image file whenever you create a gmail-email, so that the backend gets notified if the .png file gets accessed.
The chromium extension pulls the backend to see if there was a read-event.

## Installation
For the backend you need
- PHP
- Apache
- Composer (https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)

```
cd api
composer install
```

and adjust the config.php

For the chrome extension open extension/src/inject/inject.js and adjust APIPATH