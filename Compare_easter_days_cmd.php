<?php 
// Compare several Easter Days computations
// Value for method: Default: 0, Roman: 1, Gregorian: 2, Julian: 3;
include "Easter_lean.php";
echo "Test Easter computation with file, method, start and end year: ",$argv ["0"]," ", 
	$argv ["1"]," ", $argv ["2"]," ", $argv ["3"]," ", PHP_EOL;
for ($year = $argv["2"]; $year < $argv ["3"];$year++) {
	$ph = easter_days ($year, $argv["1"]);
	$mil = easter_days_miletus ($year, $argv["1"]); 
	if ($ph != $mil) echo "Method: ",$argv["1"],", year: ",$year,", PHP: ", $ph , ", milesian: ", $mil, PHP_EOL;
	}
?>