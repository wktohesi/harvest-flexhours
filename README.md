# Harvest flexhours checker
A proof-of-concept php cli script to check your Harvest flex hours.

Within the given time range:
- Gets your submitted hours from Harvest.
- Calculates week days and multiplies by hours-per-day setting.
- Spits out the difference.

## Install
**Disclaimer**: *You need to save your Harvest credentials into a config file, unencrypted. This means you should be extra careful in setting up your config file access permissions.*

1. Just clone this shit.
2. Well you do need php and a way to run it on a shell (OSX terminal, Linux shell, Windows not-so-lucky).
3. Copy `config.example.php` to `config.php` and **make it private** by `chmod 600 config.php`.
4. Add your Harvest credentials to `config.php`. Did I mention you should make the `config.php` private? You really should.
5. Make sure flexhours.php is executable by `chmod 700 flexhours.php`.

## Use
Just run `./flexhours.php`. This will check the balance using configured / default settings. Built in time range is from *first day of current year* to *today*.

**Options**
- Help: `./flexhours.php -h`
- Set a start date: `./flexhours.php -s date-string`. Example: `./flexhours.php -s 1.3.2016`.
- Set an end date: `./flexhours.php -e date-string`. Example: `./flexhours.php -e yesterday`.

## Configuration
Configuration options:
- `$credentials`: Harvest credentials. Harvest calls are made using a https protocol so this info travels encrypted. But you should be extra careful in setting your config file access permissions to your eyes only by running `chmod 600 config.php`.
- `$hours_per_day`: Hours per day. Defaults to 7.5.
- `$treshold`: Treshol in hours. Discard records that are equal or below this. 1-2 mins might be used when adding message records. Defaults to 2mins.
- `$start_string`: Default start date string. Can be overriden with -s or --start argument. Defaults to 'first day of January'.
- `$end_string`: Default end date string. Can be overriden with -e or --end argument. Defaults to 'today'.

### Dates in options and configuration
You can give dates in any php-supported date and time format: http://php.net/manual/en/datetime.formats.php

