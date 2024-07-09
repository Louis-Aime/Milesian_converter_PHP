<?php
/** Line mode comparison routine between Easter Day computation methods.
* Usage: php -f (this file's name) calendar_rule start_year end_year
* calendar_rule: 
 * 0 or CAL_EASTER_DEFAULT: Default under PHP convention, i.e. Julian up to 1752 (inclusive), Gregorian later
 * 1 or CAL_EASTER_ROMAN: Julian up to 1582 (inclusive), Gregorian later.
 * 3 or CAL_EASTER_ALWAYS_GREGORIAN: Follow Gregorian rules for all years
 * 4 or CAL_EASTER_ALWAYS_JULIAN: Follow Julian rules for all years
* start year: the first year for which the comparison is performed.
* end year: the last year for which the comparison is performed.
* The results are directly printed.
*/
include "Easter_lean.php";
echo "Show differences between easter_days() and easter_days_miletus() computation methods:\n filename calendar_rule_number start_year end_year: ",$argv ["0"]," ", 
	$argv ["1"]," ", $argv ["2"]," ", $argv ["3"]," ", PHP_EOL;
for ($year = $argv["2"]; $year < $argv ["3"];$year++) {
	$ph = easter_days ($year, $argv["1"]);
	$mil = easter_days_miletus ($year, $argv["1"]); 
	if ($ph != $mil) echo "Method: ",$argv["1"],", year: ",$year,", PHP: ", $ph , ", milesian: ", $mil, PHP_EOL;
}
echo "End processing.", PHP_EOL;
?>