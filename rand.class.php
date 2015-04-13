<?php

/**
 * Vairāku mērķu optimizācijas uzdevuma gadījuma skaitļu ģenerators
 *
 * PHP version 5
 *
 * @author     Dāvis Krēgers <davis@image.lv>
 * @copyright  2015 Dāvis Krēgers
 * @license    https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0 Universal (CC0 1.0) 
 * @version    SVN: $Id$
 * @link       http://faili.deiveris.lv/genetiskais-algoritms1/
 */

require_once 'helpers.php';


Class RND_Skaitlis {
	private $table, $direction, $last; 
	public function __construct() {

			/* 
				Pasniedzēja iedotie gadījuma skaitļi
			*/

			$this->table = array(
						array(
						0.297002882,	0.783666026,	0.055296093,	0.148800012,	0.670394469,	0.945184111,	0.338105715,	0.241068401,	0.010515502,	0.609878461),
						array(
						0.907679017,	0.850708937,	0.644214392,	0.333755624,	0.74246813,	0.817217022,	0.547258147,	0.144879406,	0.891671721,	0.333159746),
						array(
						0.076643225,	0.15949123,	0.754212475,	0.569218803,	0.303961115,	0.821838244,	0.821391117,	0.185015312,	0.359141678,	0.05678016),
						array(
						0.692772518,	0.409137055,	0.777453437,	0.655124397,	0.38318724,	0.679649553,	0.220994453,	0.911211396,	0.117719464,	0.163232982),
						array(
						0.270153374,	0.376078743,	0.846058347,	0.168955429,	0.07924448,	0.163027506,	0.064500361,	0.759663678,	0.377303734,	0.171036614),
						array(
						0.165406279,	0.117895491,	0.930374381,	0.411475527,	0.485769442,	0.206536891,	0.940767801,	0.610389642,	0.728387238,	0.671050221),
						array(
						0.474636573,	0.177344931,	0.205570403,	0.659984516,	0.330252326,	0.058636695,	0.905943367,	0.329731729,	0.41102444,	0.849171819),
						array(
						0.95463509,	0.152355173,	0.041688645,	0.196032691,	0.864803196,	0.466307436,	0.285478086,	0.217525159,	0.086915892,	0.880006088),
						array(
						0.192698668,	0.076529222,	0.841289786,	0.864020386,	0.902463394,	0.219793997,	0.48656471,	0.01038434,	0.518922889,	0.380118062),
						array(
						0.884387373,	0.057866221,	0.026434019,	0.96534338,	0.038785912,	0.801561244,	0.270679231,	0.734445908,	0.081695145,	0.015225851));

			$this->direction = 0;
			$this->last = array(-1,-1);
		}

	/*  Funkcija, kas "ģenerē" gadījuma skaitli - seko līdzi pēdējam skaitlim un nosaka, 
		kad jāatriežas uz sākumu, jāmaina virziens matricā */

	public function generate() {
			// var_dump($this->last);
			if($this->direction == 0) { // pa rindām
				if(isset($this->table[$this->last[0]][$this->last[1]+1])) {
					$this->last[1]++;
					return prbsk($this->table[$this->last[0]][$this->last[1]]);
				}
				else if(isset($this->table[$this->last[0]+1])) {
					// var_dump('next row');
					$this->last[0]++;
					$this->last[1] = 0;
					return prbsk($this->table[$this->last[0]][0]);
				}
				else { 
					// var_dump('changing direction -> bottom');
					$this->last[0] = 0;
					$this->last[1] = 0;
					$this->direction++;
					return prbsk($this->table[0][0]);
				}
			}
			else { // Pa kolonnām
				if(isset($this->table[$this->last[0]+1][$this->last[1]])) {
					$this->last[0]++;
					return prbsk($this->table[$this->last[0]][$this->last[1]]);
				}
				else if(isset($this->table[$this->last[0]][$this->last[1]+1])) {
					// var_dump('next col');
					$this->last[1]++;
					$this->last[0] = 0;
					return prbsk($this->table[$this->last[0]][$this->last[1]]);
				}
				else {
					// var_dump('changing direction -> LTR');
					$this->last[0] = 0;
					$this->last[1] = 0;
					$this->direction = 0;
					return prbsk($this->table[0][0]);
				}
			}
		}

}