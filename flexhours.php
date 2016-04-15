#!/usr/local/opt/php56/bin/php
<?php

// date() will nag without this.
date_default_timezone_set('Europe/Helsinki');
// Exit function.
register_shutdown_function(function() {
  // Always print one empty line at the end.
  echo PHP_EOL;
});

// Defaults.
$credentials    = NULL;
$hours_per_day  = 7.5;
$treshold       = 2/60; // 2 mins. 
$start_string   = 'first day of January';
$end_string     = 'today';

// Load config file.
include dirname(__FILE__) . '/config.php';

// Nowhere to go without credentials.
if (!$credentials) {
  echo "Credentials missing. You did check the configuration in config.php didn't you?" . PHP_EOL;
  exit();
}

/**
 * Get arguments.
 *
 * -s "date dtring", --start="date string": Range start.
 * -e "date string", --end="date string": Range end.
 * -h, --help: Show help.
 */
$opts = getopt('s:e:h', array('start:', 'end:', 'help'));

// Start date.
if (!empty($opts['s']) || !empty($opts['start'])) {
  $start_string = !empty($opts['s']) ? $opts['s'] : $opts['start'];
}
// End date.
if (!empty($opts['e']) || !empty($opts['end'])) {
  $end_string = !empty($opts['e']) ? $opts['e'] : $opts['end'];
}
// Help.
if (isset($opts['h']) || isset($opts['help'])) {
  showHelp();
  exit;
}

// Notify range.
echo "Checking flex hours from $start_string to $end_string." . PHP_EOL;

// Headers.
$headers = array(
  "Content-type: application/json",
  "Accept: application/json",
  "Authorization: Basic " . base64_encode($credentials),
);

// Fetch profile info.
$url = "https://mearra.harvestapp.com/account/who_am_i";
$data = doCurl($url);

if (!empty($data->user->id)) {
  $user_id = $data->user->id;

  // Range.
  $start = new DateTime($start_string);
  $end = new DateTime($end_string);

  // Fetch hours.
  $url = "https://mearra.harvestapp.com/people/" . $user_id . "/entries?from=" . $start->format('Ymd') . "&to=" . $end->format('Ymd');
  $data = doCurl($url);
  $tracked = 0;
  foreach ($data as $record) {
    if ($record->day_entry->hours > $treshold) {
      $tracked += $record->day_entry->hours;
    }
  }

  // Calculate week hours so far.
  $calculated = 0;
  $oneday = new DateInterval("P1D");
  foreach(new DatePeriod($start, $oneday, $end->add($oneday)) as $day) {
    $day_num = $day->format("N"); /* 'N' number days 1 (mon) to 7 (sun) */
    if($day_num < 6) { /* weekday */
      $calculated += $hours_per_day;
    } 
  }

  $flex = $tracked - $calculated;
  echo "Flex hour status: " . convertTime($flex) . PHP_EOL;
}
else {
  echo "Error: " . (!empty($data->message) ? $data->message : 'No idea why..');
}

/**
 * Helper function for curl calls.
 */
function doCurl($url) {
  global $headers;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $data = curl_exec($ch);

  if (curl_errno($ch)) {
    echo "Curl error: " . curl_error($ch) . PHP_EOL;
  }
  else if ($data_var = json_decode($data)) {
    return $data_var;
  }
  else {
    print "Error: Empty or invalid data received!" . PHP_EOL;
  }

  curl_close($ch);
}

/**
 * Helper function to convert decimal hours into hours and minutes.
 */
function convertTime($dec)
{
  $hour = floor($dec);
  $min = round(60*($dec - $hour));
  if ($hour > 0) {
    $hour = '+' . $hour;
  }
  if ($min < 10) {
    $min = '0' . $min;
  }

  return $hour . ':' . $min;
}

/**
 * Helper function to show help.
 */
function showHelp() {
  // Help triggers.
  $triggers = array(
    '-s <date string>, --start <date string>' => 'Start date string',
    '-e <date string>, --end <date string>' => 'End date string',
    '-h, --help' => 'Show this help',
  );

  // First column width.
  $lengths = array_map('strlen', array_keys($triggers));
  $col_width = max($lengths) + 4;

  echo "Usage: ./flexhours.php [options]" . PHP_EOL;
  echo "Options:" . PHP_EOL;
  foreach ($triggers as $trigger => $info) {
    echo "  " . str_pad($trigger, $col_width, " ") . $info . PHP_EOL;
  }
}

?>
