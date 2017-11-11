<?php
// Compute Easter days, i.e. the number of days after March 21st that the Easter Sunday is in the given year.
// Computation method is as simple as possible.
// Copyright Miletus 2017
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
function easter_days_miletus ($year, $method = CAL_EASTER_ROMAN) {
	# Initial control
	if ($year <= 0) throw new DomainException("Easter not computable before common era");
	# Decide effective method (gregorian or not ?) (do not use OR nor AND !)
	$easter_gregorian = $method == CAL_EASTER_ALWAYS_GREGORIAN
		|| ($method == CAL_EASTER_ROMAN && $year > 1582)
		|| ($method == CAL_EASTER_DEFAULT && $year > 1752) ;
	# decompose $year as $century * 100 + $bissextile * 4 + $remainder4
	# $quadrisaeculum ($century / 100) is also necessary for the gregorian computation.
	# for compatibility reason, intdiv is not used.
	$century = (int) floor ($year / 100);
	$bissextile = (int) floor (($year - $century*100)/4);
	$remainder4 =  $year - $century*100 - $bissextile*4; 
	$quadrisaeculum = (int) floor ($century / 4);
	# compute gold number minus one
	$gold = $year % 19;
	# easter_residue i.e. days from 21 March to next full moon
	$easter_residue = (15 + 19*$gold # this is enough for julian calendar
	# for the gregorian computus: add metemptose and proemptose
	# here proemptose is computed after Zeller's formula: intdiv (8*$century + 13, 25)
		+ (($easter_gregorian) ? $century - $quadrisaeculum - (int) floor((8*$century + 13)/25) : 0))
	# finally take modulo 30
	% 30; 
	# apply gregorian correction here, on easter residue rather than on easter_days.
	if ($easter_gregorian) $easter_residue -=(int) floor (($gold + 11*$easter_residue) / 319);
	# return final result: add to Easter residue, the number of days until the next Sunday. 
	if ($easter_gregorian) {
         return 1 + $easter_residue + (32 - $quadrisaeculum + 2*$century + 2*$bissextile - $remainder4 - $easter_residue) % 7 ;
      } else {
         return 1 + $easter_residue + (34 + $century + 2*$bissextile - $remainder4 - $easter_residue) % 7 ;
      }
}
?>