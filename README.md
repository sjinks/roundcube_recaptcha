# roundcube_recaptcha

Adds reCAPTCHA to the RoundCube login form

# Installation

See "Getting Started" on [https://plugins.roundcube.net/](https://plugins.roundcube.net/)

Plugin name is "wildwolf/recaptcha"

# Configuration

Edit `config.inc.php` file in <Your-roundcube-install-basepath>/plugins/recaptcha:

```php
<?php
// See https://www.google.com/recaptcha/
// See https://developers.google.com/recaptcha/docs/display
$rcmail_config['recaptcha_public_key'] = 'sitekey form https://www.google.com/recaptcha/admin';
$rcmail_config['recaptcha_secret_key'] = 'secret from https://www.google.com/recaptcha/admin';
$rcmail_config['recaptcha_theme']      = 'dark or light';
```
