# Mail address Creaner

This plugin automatically cleans up the part of display name and comment in distination email addresses such as 'To', 'Cc' and 'Bcc' when sending the email by Roundcube webmail. So it makes to be only email address specifications. It has a similar function to [Auto Address Cleaner extension](https://addons.mozilla.org/thunderbird/addon/auto-address-cleaner/) of Thunderbird.

## Requirements

Roundcube >= 1.3.0

## Installation

1. Install this plugin code into a subdirectory 'plugins/address_cleaner/' in the plugin directory, or execute the following command in the plugin directory 'plugins/'.

   ```sh
   git clone https://github.com/m10i/roundcube_plugin_address_cleaner.git address_cleaner
   ```


2. Add this plugin to the configuration file of Roundcube. If you create the plugin directory named as "address_cleaner", you must appropriately insert into the 'plugins' array of config.inc.php (formerly called main.inc.php) in the directory 'config/' as follows. 

   ```php
   $config['plugins'] = array('OTHER_PLUGIN1', 'address_cleaner', 'OTHER_PLUGIN2');
   ```

## License

GPLv3 (GNU General Public License v3.0)

See also LICENSE file.

This plugin is affected by Roundcube webmail license GPLv3 because this plugin includes a part of the source code of Roundcube webmail.
