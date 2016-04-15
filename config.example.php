<?php
// Harvest credentials. Harvest calls are made using a https protocol so this info travels encrypted.
// NOTE! You might want to 'chmod 600 config.php' to make sure your credentials won't fall into the wrong hands.
// Format: email@domain.com:password.
$credentials    = "user@example.com:mypassword";

// Hours per day. Defaults to 7.5.
//$hours_per_day  = 7.5;

// Treshol in hours. Discard records that are equal or below this. 1-2 mins is often used when adding message records. Defaults to 2mins.
//$treshold       = 2/60;

// Default start date string. Can be overriden with -s or --start argument.
//$start_string   = 'first day of January';

// Default end date string. Can be overriden with -e or --end argument.
//$end_string     = 'today';

