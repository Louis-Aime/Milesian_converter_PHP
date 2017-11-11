<?php 
//////////////////////////////////////////////////////////////////////////////
// Conversion from Julian Day to Milesian date and the reverse. 
// Copyright Miletus 2016 - Louis A. de Fouquières
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 1. The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
// 2. Changes with respect to any former version shall be documented.
//
// The software is provided "as is", without warranty of any kind,
// express of implied, including but not limited to the warranties of
// merchantability, fitness for a particular purpose and noninfringement.
// In no event shall the authors of copyright holders be liable for any
// claim, damages or other liability, whether in an action of contract,
// tort or otherwise, arising from, out of or in connection with the software
// or the use or other dealings in the software.
// Inquiries: www.calendriermilesien.org
///////////////////////////////////////////////////////////////////////////////
// Written after a version under Ada
// Tested under PHP 5.4.x.
//
// The purpose of this package is to provide conversion routines 
// between a Julian Day the equivalent date in the Milesian calendar.
// Main functions:
//		milesiantojd ($month, $day, $year) - similar to gregoriantojd, but arguments represent a date of the Milesian calendar.
//		cal_from_jd_milesian ($jd) - similar to cal_from_jd, specifically for the milesian calendar 
// which is not referenced under the PHP user community.
// 		french_weekday_name ($dow, $title) - yields the complete name of the weekdays in French, beginning or not with a capital.
// Other functions are usefull for the abovementionned, and are available for broader use: 
//		intdiv_a is a special implementation of intdiv (not available under PHP 5.4.x), so that the remainder is non-negative.
//		intdiv_r does the same, except that the first argument is by reference, it holds the remainder on return.
//		intidiv_r_ceiling is the integer division with positive remainder and ceiling: the remainder may be set to
// the divisor, when the ordinary quotient reaches the ceiling. 
// This routine is used e.g. to find the rank of year and of day in the year, in a quadriannum whose last year is the longest.
//
// Caution: all arguments are deemed integers (int). Control cannot be performed before PHP 7.
//////////////////////////////////////////////////////////////////////////////////
//
function intdiv_a ($argument, $divisor) {  //here only values of arguments are passed. 
//Almost the PHP 7.xx intdiv(), except that remainder is always >=0
$cycle = 0;
if ($divisor == 0) throw new DomainException("Divisor cannot be 0");
while ($argument < 0 and $divisor != 0) {
	$argument += $divisor;
	--$cycle;
};
while ($argument >= $divisor and $divisor != 0) {
	$argument -= $divisor;
	++$cycle;
	};
return $cycle;
}
function intdiv_r (&$argument, $divisor) { //same as above, except that first $argument (dividend) is changed to positive remainder
$cycle = 0;
if ($divisor == 0)throw new DomainException("Divisor cannot be 0");
while ($argument < 0) {
	$argument += $divisor;
	--$cycle;
	};
while ($argument >= $divisor) {
	$argument -= $divisor;
	++$cycle;
	};
return $cycle;
}
function intdiv_r_ceil (&$argument, $divisor, $ceiling) { //variant for intdiv_r; $argument is authorised to be = $divisor if it is was originally equal to $ceiling * $divisor
$cycle = 0;
if ($divisor <= 0 or $argument < 0 or $argument > $ceiling * $divisor) throw new DomainException("Invalid arguments for a division with ceiling");
--$ceiling; // in practical, test is performed against $ceiling - 1
while ($argument >= $divisor and $cycle < $ceiling) {
	$argument -= $divisor;
	++$cycle;
	};
return $cycle;
}
function long_milesian_year ($year) { // boolean, says whether $year (milesian) is 366 days long or not.
// $year must be int, this cannot be controlled before PHP 7.x
If ($year < -4713 or $year > 9999) throw new DomainException("Year is out of range or ill specified");
$year += 7201; // set to a positive number, because modulus computation does not handle properly negative numbers.
return $year % 4 == 0 and ($year % 100 != 0 or ($year % 400 == 0 and $year % 3200 != 0)) ; 
}
function milesiantojd ($month, $day, $year) { // similar to gregoriantojd; control of validity of milesian date, year between -4713 and +9999.
	if  (($year < -4713) or ($year > 9999)) throw new DomainException("Year is out of range or ill-specified");
	if	((($month <=0) or ($month > 12)) 
	or (($day <=0) or ($day >31)) 
	or (($day == 31) and ($month % 2 == 1)) 
	or ($month == 12 and $day == 31 and ! long_milesian_year($year)))
	throw new DomainException("This date does not exist");
	$year +=4000; //set to milesian year origin; this is OK using intdiv_a and not the standard intdiv integer division.
	return (260080 + $year*365 + intdiv_a($year,4) - intdiv_a($year,100) + intdiv_a($year,400) - intdiv_a($year,3200)
		+ --$month*30 +intdiv_a($month,2)) +$day;
}
function cal_from_jd_milesian ($jd){ //an extension of the cal_from_jd routine defined since php 4.1.0., for the Milesian calendar. 
//Names suitable to the Milesian calendar
//The month names are in Latin (although there is a proposed English list for the Milesian months
//The abbreviated names are the universal notation 1m, 2m, 3m etc. through 12m.
//Const 
$MILESIAN_MONTH_LATIN = array ("unemis", "secondemis", "tertemis", 
	"quartemis", "quintemis", "sextemis", 
	"septemis", "octemis", "novemis", 
	"decemis", "undecemis", "duodecemis");
//Const
$MILESIAN_MONTH_NOTATION = array ("1m", "2m", "3m", "4m", "5m", "6m", "7m", "8m", "9m", "10m", "11m", "12m");
//
if ($jd < 0 or $jd > 5373471) throw new DomainException("Julian Day is out of range or ill-specified");
$day = $jd-260081; // milesian day, starting 1 1m -4000; it may be negative.
// In the next computations, $day is set to the positive remainder of the integer division, whereas the quotient is directly used to compute the year then the month.
$year = 3200*intdiv_r ($day,1168775) - 4000; // Year Anno Domini initiated at the beginning of the suitable Milesian epoch, i.e. -4000, -800, 2400 etc.
$year += intdiv_r ($day, 146097) * 400; 
$year += intdiv_r_ceil ($day, 36524, 4) * 100; 
$year += intdiv_r ($day, 1461) * 4;
$year += intdiv_r_ceil ($day, 365, 4);
$month = intdiv_r ($day, 61) * 2; // initiate rank of month
$month += intdiv_r_ceil ($day, 30, 2); // This is the month rank (from 0). Enough for this purpose.
$day++; //Add one because days are counted starting from 1.
return array (
"date" => $day." ".$MILESIAN_MONTH_NOTATION[$month]." ".$year,
"month" => $month+1,
"day" => $day,
"year" => $year,
"dow" => jddayofweek ($jd),
"abbrevdayname" => jddayofweek ($jd, 2),
"dayname" => jddayofweek ($jd, 1),
"abbrevmonth" => $MILESIAN_MONTH_NOTATION [$month],
"monthname" => $MILESIAN_MONTH_LATIN [$month],
);
}
function french_weekday_name ($dow, $title = 0) {
// French names for weekdays
//Const 
$FRENCH_WEEKDAY_NOTITLE = array ("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
$FRENCH_WEEKDAY_TITLE = array ("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
//
	if ($title) {return $FRENCH_WEEKDAY_TITLE[$dow];}
	else {return $FRENCH_WEEKDAY_NOTITLE[$dow];};
}
?>