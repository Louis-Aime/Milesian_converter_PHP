<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  h1.dateconvert {text-align: center; font-size: 20pt; font-style: bold}
  p.dateconvert {text-align: center; font-size: 16pt}
  td.dateconvert {text-align: center; font-size: 16pt}
  form.dateconvert {text-align: center; font-size: 16pt}
  input.dateconvert {text-align: center; font-family: Serif; font-size: 16pt}
  button.dateconvert {font-family: Serif; font-size: 16pt}
  table.dateconvert {font-size: 16pt}
</style>
<title>Convertisseur de dates - calendriers milésien et PHP</title> 
</head>
<body>
<?php
/*
Display a date in Julian Day, and enables conversion
from and to Milesian, gregorian, julian and jewish calendars.
Copyright Miletus 2016-2024 - Louis A. de Fouquières
Tested under PHP 8.x.x
Versions
	2016 - original
	2019 
		re-order calendars presentation 
		enable 0 julian day
		enable 7-digit years and 9-digits days
	2024
		Encoding conversion to UTF-8
		No capital in tag names
		Update HTML head elements
*/
/*
Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:
1. The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.
2. Changes with respect to any former version shall be documented.
//
The software is provided "as is", without warranty of any kind,
express of implied, including but not limited to the warranties of
merchantability, fitness for a particular purpose and noninfringement.
In no event shall the authors of copyright holders be liable for any
claim, damages or other liability, whether in an action of contract,
tort or otherwise, arising from, out of or in connection with the software
or the use or other dealings in the software.
Inquiries: www.calendriermilesien.org
*/

//
require_once ("Milesian_calendar_conversion.php");
//
//Analyse posted data. Something has changed on the forms, we have to recompute all data. 
//
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$message = "Dates et jour julien"; // Computations are deemed to be performed... unless an exception occurs.
	// Initialise data with form contents
	$days = $_POST ["days"];
	$jd = (int) $_POST["julianday"];
	$md ["year"] = (int) $_POST ["year"];
	$md ["month"] = (int) $_POST ["month"];
	$md ["day"] = (int) $_POST ["quant"];
	$juliandate["relativeyear"] = (int) $_POST ["jyear"];
	$juliandate["year"] = ($juliandate ["relativeyear"] > 0) ? $juliandate ["relativeyear"] : $juliandate ["relativeyear"] - 1; // PHP uses non-zero years fir Julian and Gregorian calendars
	$juliandate ["month"] = (int) $_POST ["jmonth"];
	$juliandate ["day"] = (int) $_POST ["jquant"];
	$gregdate ["relativeyear"] = (int) $_POST ["gyear"];
	$gregdate ["year"] = ($gregdate ["relativeyear"] >0) ? $gregdate ["relativeyear"] : $gregdate ["relativeyear"] - 1; // PHP uses non-zero years fir Julian and Gregorian calendars
	$gregdate ["month"] = (int) $_POST ["gmonth"];
	$gregdate ["day"] = (int) $_POST ["gquant"];
	$jewdate ["year"] = (int) $_POST ["hyear"]; // No special handling of the negative Jewish years, the world did not exist at that time...
	$jewdate ["month"] = (int) $_POST ["hmonth"];
	$jewdate ["day"] = (int) $_POST ["hquant"];
//	
switch ($_POST ["Compute"])  {
	case "Today" : 
		$today = getdate(); // la date courante -- attention le tableau reçu de getdate n'a pas la même structure que $md.
		$message = "Dates et jour julien aujourd'hui";
		$jd = gregoriantojd ($today["mon"], $today["mday"], $today["year"]); // Julian day of today
		$md = cal_from_jd_milesian ($jd); // Milesian date of today
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);		
		$wd = french_weekday_name($md["dow"]);	
	break;
	case "Shift" : try {	
		$jd1 = $jd + $days; 
		$tempd1 = cal_from_jd_milesian ($jd1); // Exception may occur here. If catched, next instructions will not be performed. 
		$jd = $jd1;
		$md = $tempd1;
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years for Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years for Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);		
		$wd = french_weekday_name($md["dow"]);
		}
	catch (Exception $e) {
		$message = "Jour julien irrégulier ou hors limites";
		}
	break;	
	case "JulianDay" : try {	
		$tempd1 = cal_from_jd_milesian ($jd); // Exception may occur here. If catched, next instructions will not be performed. 
		$md = $tempd1;
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);		$wd = french_weekday_name($md["dow"]);
		}
	catch (Exception $e) {
		$message = "Jour julien irrégulier ou hors limites";
		}
	break;
	case "Milesian" : try {
		$jd1 = milesiantojd ($md ["month"],$md ["day"],$md ["year"]); // Exception may occur here. If catched, next instructions will not be performed.
		$jd = $jd1; 
		$md = cal_from_jd_milesian ($jd); // In order to replenish the "monthname"	field.
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);
		$wd = french_weekday_name(jddayofweek($jd));
		}
	catch (Exception $e) {
		$message = "Date irrégulière ou hors limites";
		}
	break;
	case "Julian" : try {
		$jd1 = juliantojd ($juliandate ["month"],$juliandate ["day"],$juliandate ["year"]); // Exception may occur here. If catched, next instructions will not be performed.
		$jd = $jd1;
		$md = cal_from_jd_milesian ($jd); 
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);
		$wd = french_weekday_name(jddayofweek($jd));
		}
	catch (Exception $e) {
		$message = "Date irrégulière ou hors limites";
		}
	break;
	case "Gregorian" : try {
		$jd1 = gregoriantojd ($gregdate ["month"],$gregdate ["day"],$gregdate ["year"]); // Exception may occur here. If catched, next instructions will not be performed.
		// if ($jd1 <= 0) throw new DomainException("Invalid date (Gregorian)");
		$jd = $jd1;
		$md = cal_from_jd_milesian ($jd); 
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);
		$wd = french_weekday_name(jddayofweek($jd));
		}
	catch (Exception $e) {
		$message = "Date irrégulière ou hors limites";
		}
	break;
	case "Hebraic" : try {
		$jd1 = jewishtojd ($jewdate ["month"],$jewdate ["day"],$jewdate ["year"]); // Exception may occur here. If catched, next instructions will not be performed.
		$jd = $jd1;
		$md = cal_from_jd_milesian ($jd); 
		$juliandate = cal_from_jd ($jd, CAL_JULIAN);
		$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
		$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
		$jewdate = cal_from_jd ($jd, CAL_JEWISH);
		$wd = french_weekday_name(jddayofweek($jd));
		}
	catch (Exception $e) {
		$message = "Date irrégulière ou hors limites";
		}
	break;
}
}
else { // No data posted: prepare data of today, to be inserted in first form
	$today = getdate(); // la date courante -- attention le tableau reçu de getdate n'a pas la même structure que $md.
	$message = "Dates et jour julien aujourd'hui";
	$days = 1;
	$jd = gregoriantojd ($today["mon"], $today["mday"], $today["year"]); // Julian day of today
	$md = cal_from_jd_milesian ($jd); // Milesian date of today
	$wd = french_weekday_name($md["dow"]);
	$juliandate = cal_from_jd ($jd, CAL_JULIAN);
	$juliandate ["relativeyear"] = ($juliandate ["year"] < 0) ? $juliandate ["year"] + 1 : $juliandate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
	$gregdate = cal_from_jd ($jd, CAL_GREGORIAN);
	$gregdate ["relativeyear"] = ($gregdate ["year"] < 0) ? $gregdate ["year"] + 1 : $gregdate ["year"]; // PHP uses non-zero years fir Julian and Gregorian calendars
	$jewdate = cal_from_jd ($jd, CAL_JEWISH);		
}
?>
<h1 class="dateconvert">Convertisseur de dates milésiennes, juliennes, grégoriennes et hébraïques</h1>
<p class="dateconvert">Années avant J.-C. selon la convention des astronomes, avec un an zéro: l'an -752 est l'an 753 av. J.-C.<br/>
Le calendrier hébraïque ne comprend aucune date avant son origine.<br/>
Les noms de mois romains sont en anglais, selon le standard du langage PHP.</p> 
<form class="dateconvert" method="post">
<table class="dateconvert" align="center" >
	<tr><td class="dateconvert" colspan="3"><b><?php echo $message ?></b> </td></tr>
	<tr>
		<td><button class="dateconvert" type="submit" name="Compute" value="Today">Aujourd'hui</button></td>
		<td><button class="dateconvert" type="submit" name="Compute" value="Shift">+/-jours</button></td>
		<td><input class="dateconvert" type="int" size="4" name="days" value="<?php if (isset($days)) echo($days);?>"/></td>
	</tr>
	</table> <br/>

<table class="dateconvert" align="center">
<tr><th>Calendrier</th><th>Quantième</th><th>Mois</th><th>Nom de mois</th><th>Année</th><th>Commande</th>
<tr><td>Date milésienne:</td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2" name="quant" value="<?php echo htmlEntities($md["day"]);?>" /> </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2"  name="month" value="<?php echo htmlEntities($md["month"]);?>" /> </td>
	<td><input class="dateconvert" type="text" size="10" disabled name="mmname" value="<?php if (isset($md["monthname"])) echo($md["monthname"]);?>" /></td>
	<td><input class="dateconvert" type="int" size="7" maxlength="7"  name="year" value="<?php echo htmlEntities($md["year"]);?>" /> </td>
	<td><button class="dateconvert" type="submit" name="Compute" value="Milesian">Calculer sur cette date</button></td>	
<tr><td>Date grégorienne: </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2" name="gquant" value="<?php echo htmlEntities($gregdate["day"]);?>" /> </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2"  name="gmonth" value="<?php echo htmlEntities($gregdate["month"]);?>" /> </td>
	<td><input class="dateconvert" type="text" size="10" disabled name="gmname" value="<?php if (isset($gregdate["monthname"])) echo($gregdate["monthname"]);?>" /></td>
	<td><input class="dateconvert" type="int" size="7" maxlength="7"  name="gyear" value="<?php echo htmlEntities($gregdate["relativeyear"]);?>" /> </td>
	<td><button class="dateconvert" type="submit" name="Compute" value="Gregorian">Calculer sur cette date</button></td>
<tr><td>Date julienne: </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2" name="jquant" value="<?php echo htmlEntities($juliandate["day"]);?>" /> </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2"  name="jmonth" value="<?php echo htmlEntities($juliandate["month"]);?>" /> </td>
	<td><input class="dateconvert" type="text" size="10" disabled name="jmname" value="<?php if (isset($juliandate["monthname"])) echo($juliandate["monthname"]);?>" /></td>
	<td><input class="dateconvert" type="int" size="7" maxlength="7"  name="jyear" value="<?php echo htmlEntities($juliandate["relativeyear"]);?>" /> </td>
	<td><button class="dateconvert" type="submit" name="Compute" value="Julian">Calculer sur cette date</button> </td>
<tr><td>Date hébraïque: </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2" name="hquant" value="<?php if (($jewdate["day"]>0)) echo ($jewdate["day"]);?>" /> </td>
	<td><input class="dateconvert" type="int" size="2" maxlength="2"  name="hmonth" value="<?php if (($jewdate["month"]>0)) echo ($jewdate["month"]);?>" /> </td>
	<td><input class="dateconvert" type="text" size="10" disabled name="hmname" value="<?php if (isset($jewdate["monthname"])) echo($jewdate["monthname"]);?>" /></td>
	<td><input class="dateconvert" type="int" size="7" maxlength="7"  name="hyear" value="<?php if (($jewdate["year"]>0)) echo ($jewdate["year"]);?>" /> </td>
	<td><button class="dateconvert" type="submit" name="Compute" value="Hebraic">Calculer sur cette date</button></td>
</table>
<table class="dateconvert" align="center" > 
<tr>
	<td>Jour de semaine:</td><td> <input class="dateconvert" type="text" size="8" disabled name="wday" value="<?php if (isset($wd)) echo($wd);?>" /></td>
	<td>Jour julien:</td> 
	<td><input class="dateconvert" type="int" size="9" maxlength="9" name="julianday" value="<?php echo htmlEntities($jd);?>"/> </td>
	<td><button class="dateconvert" type="submit" name="Compute" value="JulianDay">Calculer sur ce jour julien</button> </td>
</table><br/>
</form>	
<p class="dateconvert">Référence : <a href="http://www.calendriermilesien.org">www.calendriermilesien.org</a></p>
</body>
</html>