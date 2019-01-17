# Milesian_converter_PHP
Converter between date in Milesian and Julian day.
Tested under PHP 5.4.x.
## Milesian_calendar_conversion
### function long_milesian_year ($year) 
boolean, says whether $year (in milesian) is 366 days long or not.
### function milesiantojd ($month, $day, $year)
similar to gregoriantojd, but argument represent a milesian date; 
control of validity of milesian date, year between -4713 and 1465102.
### function cal_from_jd_milesian ($jd) 
an extension of the cal_from_jd routine defined since php 4.1.0., for the Milesian calendar.

All other routines in this file are utilities implementing the integer division with ceiling.

## Date_converter
A web application demonstrating the milesian conversions.

## Easter_lean
The "milesian method" for computing Easter sunday.
### function easter_days_miletus ($year, $method = CAL_EASTER_ROMAN)
Compute Easter day the same way as easter_day in PHP.

## Compare_easter_days_cmd
Use as a command line in order to compare easter_days_miletus with the standard function easter_days.
- Argument 1 denotes the easter_days method, 0:normal, 1:Roman, 2:Gregorian, 3:Julian,
- Argument 2 is first year of comparison,
- Argument 3 is last year.

If a difference occurs, the results are displayed.


