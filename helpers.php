<?php

/**
 * Vairāku mērķu optimizācijas uzdevuma palīgfunkcijas
 *
 * PHP version 5
 *
 * @author     Dāvis Krēgers <davis@image.lv>
 * @copyright  2015 Dāvis Krēgers
 * @license    https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0 Universal (CC0 1.0) 
 * @version    SVN: $Id$
 * @link       http://faili.deiveris.lv/genetiskais-algoritms1/
 */

// Noapaļo skaitli līdz 0.1 precizitātei
function skaitlis($val) {
	return number_format((float) $val, 1, '.', ''); 
}

// Noapaļo skaitli līdz .001 precizitātei
function prbsk($val) {
	return number_format((float) $val, 3, '.', ''); 
}

// Pārveido skaitli uz binārās vērtības skalu
function real2bin($val) {
	global $intv, $dalsk;
	return ($val - $intv[0])*((pow(2, $dalsk)-1)/($intv[1]-$intv[0]));
}

// Pārveido bināro vērtību uz reālu skaitli
function bin2real($val) {
	global $intv, $dalsk;
	return $intv[0] + $val * (($intv[1]-$intv[0])/(pow(2,$dalsk)-1));
}
