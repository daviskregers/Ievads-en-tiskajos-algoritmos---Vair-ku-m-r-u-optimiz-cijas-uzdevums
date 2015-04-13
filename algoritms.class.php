<?php

/**
 * Vairāku mērķu optimizācijas uzdevuma algoritms
 *
 * PHP version 5
 *
 * @author     Dāvis Krēgers <davis@image.lv>
 * @copyright  2015 Dāvis Krēgers
 * @license    https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0 Universal (CC0 1.0) 
 * @version    SVN: $Id$
 * @link       http://faili.deiveris.lv/genetiskais-algoritms1/
 */

require_once 'rand.class.php';

$PI = 3.14159265359;
$apl_nr_3 = 768;

function piemerotiba($x, $y) {
	global $apl_nr_3, $PI;
	return 2*$apl_nr_3 + $x*$x + $y*$y - $apl_nr_3 * cos(2 * $PI * $x) - $apl_nr_3 * cos(2 * $PI * $y);
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

Class Algoritms {
	private $sakuma_populacija,
			$populacija, $berni,
			$mutacijas_varbutiba, 
			$intervals, $precizitate, $max_vertiba, $max_vertiba_binary, $dalijuma_skaitlis, $populacijas_info,
			$selekcija, $generacijas, $individiem_intervali, $rand, $krustosanas_intervali, $krustosanas_pari,
			$mutacijas_elementi, $mutacijas_genu_intervali, $selekcijas_elementi, $selekcijas_intervali, $selekcijas_elementi_rezultats;

	public function __construct($options) {
		foreach($options as $key => $option) {
			$this->$key = $option;
		}
		$this->populacijas_info = array('sum' => 0,'max' => array('key' => 0, 'val' => 0),'avg' => 0);
		$this->krustosanas_intervali = array();

		$this->rand = new RND_Skaitlis();
		$this->loop();
	}

	protected function loop() {

		for($i = 0; $i <= $this->generacijas; $i++) {
			
			if($i != 0) echo "<h1 id=\"generacija-".$i."\">".($i+1).". Ģenerācija</h1>";
			else {
				?>
				<h1>Sākums</h1>
				<p>Intervāls: [<?php echo $this->intervals[0]; ?>; <?php echo $this->intervals[1]; ?>]</p>
				<p>Precizitāte: <?php echo $this->precizitate; ?></p>
				<p>MAX vērtība: <?php echo $this->max_vertiba; ?> => <?php echo $this->max_vertiba_binary; ?></p>
				<p>Dalījumu skaits: <?php echo $this->dalijuma_skaitlis; ?></p>
				<p>Sākuma vērtības populācijai: <?php echo count($this->sakuma_populacija); ?></p>
				<h1 id="generacija-0">Sākuma populācija</h1>
				<?php
			}

			if($i == 0) $this->populacija = $this->sakuma_populacija;
			$this->populacijas_piemerotiba();
			$this->populacijas_izvade();

			$this->individiem_aprekinatie_intervali();
			$this->individiem_aprekinatie_intervali_izvade();

			if($this->selekcija == 'turnirs') $this->turnirs_paru_veidosana();
			else $this->rulete_paru_veidosana();

			$this->krustosanas_intervali();
			$this->individu_krustosana();

			$this->individu_parbaude_mutacijai();
			$this->intervali_mutejosiem_geniem();

			$this->mutacija();

			$this->jaunas_paaudzes_selekcija();
			$this->jaunas_paaudzes_selekcija_intervali();

			if($this->selekcija == 'turnirs') $this->turnirs_jaunas_paaudzes_selekcija();
			else $this->rulete_jaunas_paaudzes_selekcija();

			$this->jauna_paaudze();

		}

		$this->populacijas_piemerotiba();

	}

	protected function jauna_paaudze() {
		$paaudze = array();
		foreach($this->selekcijas_elementi_rezultats as $key => $val) {
			$paaudze[] = $this->selekcijas_elementi[$val[1]];
		}
		$this->berni = array();
		$this->populacija = $paaudze;
	}

	protected function turnirs_jaunas_paaudzes_selekcija() {
		?>

			<h5>Turnīra selekcija jaunajai ģenerācijai</h5>
			<table border="1">
				<tr>
					<th>Gadījuma skaitlis no tabulas</th>
					<th>Izvēlētie indivīdi un piemērotība</th>
					<th>Uzvarētājs</th>
				</tr>
				
				<?php 
				$c = 0; $ParuVeidosana = array();
				for($i = 1; $i <= count($this->selekcijas_elementi); $i++) {
					$randVal = floatval($this->rand->generate()); $randKeys = array_keys($this->selekcijas_intervali); $randEl = 0;
					for($j = 0; $j < count($this->selekcijas_intervali); $j++) {
						if($randVal < floatval($randKeys[$j])) {
							$randKeys[$j];
							if($j > 0) $randEl = $this->selekcijas_intervali[$randKeys[$j-1]];
							else $randEl = $this->selekcijas_intervali[$randKeys[0]];
							break;
						}
					}
					$ParuVeidosana[] = array($randVal, $randEl);
				}

				$KrustosanasPari = $ParuVeidosana;
				for($i = 0; $i < count($ParuVeidosana); $i++):  ?>
				<tr>
					<td><?php echo $ParuVeidosana[$i][0]; ?></td>
					<td><?php echo ($ParuVeidosana[$i][1]+1). " (".skaitlis($this->selekcijas_elementi[$ParuVeidosana[$i][1]]['piemerotiba']).")"; ?></td>

					<?php if($i % 2 == 0): ?>
						<?php if($this->selekcijas_elementi[$ParuVeidosana[$i][1]]['piemerotiba'] > $this->selekcijas_elementi[$ParuVeidosana[$i+1][1]]['piemerotiba']): ?>
							<td rowspan=2><?php echo $ParuVeidosana[$i][1]+1; ?></td>
							<?php unset($KrustosanasPari[$i+1]); ?>
						<?php else: ?>
							<td rowspan=2><?php echo $ParuVeidosana[$i+1][1]+1; ?></td>
							<?php unset($KrustosanasPari[$i]); ?>
						<?php endif; ?>
					<?php endif; ?>

				</tr>
				<?php endfor; ?>


			</table>
		<?php
		$krustosana = array();
		foreach($KrustosanasPari as $val) { // atslegas neiet viena pec otras, reset
			$krustosana[] = $val;
		}
		$this->selekcijas_elementi_rezultats = $krustosana;
	}

	protected function rulete_jaunas_paaudzes_selekcija() {
		?>

			<h5>Ruletes selekcija jaunajai ģenerācijai</h5>
			<table border="1">
				<tr>
					<th>Nr.</th>
					<th>Indivīds</th>
					<th>X</th>
					<th>Y</th>
					<th>f(x, y)</th>
					<th>Izdzīvošanas varbūtība</th>
					<th>Kumulatīvā varbūtība</th>
					<th>Intervāls</th>
				</tr>
				
				<?php 
				$c = 0; $ParuVeidosana = array(); $kumul = 0; $piem_summa = 0; $atlasitie = array();

				for($i = 1; $i <= count($this->selekcijas_elementi); $i++) {
					$piem_summa += $this->selekcijas_elementi[$i-1]['piemerotiba'];
				}



				$max_piem = 0; $max_el = 0;
				for($k = 0; $k < count($this->selekcijas_elementi); $k++) {
					if($this->selekcijas_elementi[$k]['piemerotiba'] > $max_piem) {
						$max_piem = $this->selekcijas_elementi[$k]['piemerotiba'];
						$max_el = $k;
					}
				}

				for($i = 1; $i <= count($this->selekcijas_elementi); $i++):
					$probability = $this->selekcijas_elementi[$i-1]['piemerotiba'] / $piem_summa;
					$prev = $kumul;
					$kumul += $probability;
					if($this->selekcijas_elementi[$i-1]['piemerotiba'] == $max_piem) {
						$atlasitie[] = array(0, $i-1);
					}
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $this->selekcijas_elementi[$i-1]['BIN']; ?></td>
						<td><?php echo skaitlis($this->selekcijas_elementi[$i-1]['x']); ?></td>
						<td><?php echo skaitlis($this->selekcijas_elementi[$i-1]['y']); ?></td>
						<?php if($this->selekcijas_elementi[$i-1]['piemerotiba'] == $max_piem) : ?>
							<td style="background-color: yellow"><?php echo skaitlis($this->selekcijas_elementi[$i-1]['piemerotiba']); ?></td>
						<?php else: ?>
							<td><?php echo skaitlis($this->selekcijas_elementi[$i-1]['piemerotiba']); ?></td>
						<?php endif; ?>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
				<?php
					$atlase[''.$kumul] = $i-1;
				endfor;
				?>
		
			</table>

		<h4>Atlasītie elementi</h4>
		<table border="1">
			<tr>
				<th>Gadījuma skaitlis no tabulas</th>
				<th>Izvēlētie indivīdi un piemērotība</th>
			</tr>
			<?php 
			for($i = 0; $i < count($this->selekcijas_elementi) / 2; $i++):  ?>
				<?php if($i == 0): ?>
				<tr>
					<td>-</td>
					<td><?php echo ($atlasitie[0][1]+1). " (".skaitlis($this->selekcijas_elementi[$atlasitie[0][1]]['piemerotiba']).")"; ?></td>
				</tr>
				<?php else: ?>
					<?php
						$random = $this->rand->generate(); 
						$atlase_keys = array_keys($atlase);
						for($j = 0; $j < count($atlase_keys); $j++) {
							if($random < floatval($atlase_keys[$j])) {
								$elements = $j;
								break;
							}
						}
						$atlasitie[] = array($random, $elements);
					?>
					<tr>
						<td><?php echo prbsk($random); ?></td>
						<td><?php echo ($elements+1). " (".skaitlis($this->selekcijas_elementi[$elements]['piemerotiba']).")"; ?></td>
					</tr>
					

				<?php endif; ?>
			<?php endfor; ?>
		</table>
		<?php


		$this->selekcijas_elementi_rezultats = $atlasitie;
	}

	protected function jaunas_paaudzes_selekcija_intervali() {
		if($this->selekcija == 'turnirs'):
		?>

		<h5>Indivīdiem aprēķinātie intervāli</h5>

		<table border=1>
			<tr>
				<th>Indivīds</th>
				<th>Varbūtība tikt izvēlētam</th>
				<th>Kumulatīvā varbūtība</th>
				<th>Intervāls</th>
			</tr>
			<?php 
			$kumul = 0;
			for($i = 1; $i <= count($this->selekcijas_elementi); $i++):
				

				
					$probability = 1 / count($this->selekcijas_elementi);
					$prev = $kumul;
					$kumul += $probability;
					$slekc_int[''.$kumul] = $i;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php

			endfor; ?>
		</table>
		<?php

		else:
			
			$piem_summa = 0;
			for ($i=0; $i < count($this->selekcijas_elementi); $i++) { 
				$piem_summa += $this->selekcijas_elementi[$i]['piemerotiba'];
			}

			$probability = $this->selekcijas_elementi[$i-1]['piemerotiba'] / $piem_summa;
			$kumul = 0;

			for($i = 1; $i <= count($this->selekcijas_elementi); $i++):
			
				$probability = 1 / count($this->selekcijas_elementi);
				$prev = $kumul;
				$kumul += $probability;
				$slekc_int[''.$kumul] = $i;

			endfor; 
		endif;

		$this->selekcijas_intervali = $slekc_int;
	}

	protected function jaunas_paaudzes_selekcija() {
		?>
		<h4>Jaunās paaudzes selekcija</h4>
		<table border=1>
			<tr>
				<th>Nr</th>
				<th>Indivīds</th>
				<th>X</th>
				<th>Y</th>
				<th>X'</th>
				<th>Y'</th>
				<th>Piemērotība</th>
			</tr>
			<?php $i = 0; $selekcija = array();
			foreach($this->populacija as $key => $val): $i++; $selekcija[] = $val; ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $val['BIN']; ?></td>
				<td><?php echo skaitlis($val['x']); ?></td>
				<td><?php echo skaitlis($val['y']); ?></td>
				<td><?php echo skaitlis($val['xd']); ?></td>
				<td><?php echo skaitlis($val['yd']); ?></td>
				<td><?php echo skaitlis($val['piemerotiba']); ?></td>
			</tr>
			<?php endforeach;
			foreach($this->berni as $key => $val): $i++; ?>
			<tr>
				<?php
					$vert = explode(' ', $val);
					$xd = bindec($vert[0]);
					$yd = bindec($vert[1]);
					$x = bin2real($xd);
					$y = bin2real($yd);
					$piemerotiba = piemerotiba($x, $y);
				?>
				<td><?php echo $i; ?></td>
				<td><?php echo $val; ?></td>
				<td><?php echo skaitlis($x); ?></td>
				<td><?php echo skaitlis($y); ?></td>
				<td><?php echo skaitlis($xd); ?></td>
				<td><?php echo skaitlis($yd); ?></td>
				<td><?php echo skaitlis($piemerotiba); ?></td>
			</tr>
			<?php 
			$selekcija[] = array('x' => $x, 'y' => $y, 'xd' => $xd, 'yd' => $yd, 'BIN' => $val, 'piemerotiba' => $piemerotiba);
			endforeach; ?>
		</table>
		<?php
		$this->selekcijas_elementi = $selekcija;
	}

	protected function mutacija() {
		?>
		<h4>Mutācija</h4>
		<table border=1>
			<tr>
				<th>Pirms</th>
				<th>RND</th>
				<th>Gēns</th>
				<th>Pēc</th>
			</tr>
			<?php foreach($this->mutacijas_elementi as $key => $el): ?>
			<tr>
				<?php if($el[1] == true): ?>
					<td><?php echo $this->berni[$el[0]]; ?></td>
						<?php
							$rnd = $this->rand->generate();

							$randKeys = array_keys($this->mutacijas_genu_intervali);
							for($j = 0; $j < count($this->mutacijas_genu_intervali); $j++) {
								if($rnd < floatval($randKeys[$j])) {
									$randKeys[$j];
									if($j > 0) $gens = $this->mutacijas_genu_intervali[$randKeys[$j-1]];
									else $gens = $this->mutacijas_genu_intervali[$randKeys[0]];
									break;
								}
							}

							$tmp = str_split($this->berni[$el[0]]);
							$tmp[$gens-1] = (intval($tmp[$gens-1]) == 0) ? '1' : '0';
							$this->berni[$el[0]] = implode('', $tmp);

						?>
					<td><?php echo $rnd; ?></td>
					<td><?php echo $gens+1; ?></td>
					<td><?php echo $this->berni[$el[1]]; ?></td>
				<?php else: ?>
					<td><?php echo $this->populacija[$el[0]]['BIN']; ?></td>
					<?php
							$rnd = $this->rand->generate();

							$randKeys = array_keys($this->mutacijas_genu_intervali);
							for($j = 0; $j < count($this->mutacijas_genu_intervali); $j++) {
								if($rnd < floatval($randKeys[$j])) {
									$randKeys[$j];
									if($j > 0) $gens = $this->mutacijas_genu_intervali[$randKeys[$j-1]];
									else $gens = $this->mutacijas_genu_intervali[$randKeys[0]];
									break;
								}
							}

							$tmp = str_split($this->populacija[$el[0]]['BIN']);
							$tmp[$gens-1] = (intval($tmp[$gens-1]) == 0) ? '1' : '0';
							$this->populacija[$el[0]]['BIN'] = implode('', $tmp);

						?>
					<td><?php echo $rnd; ?></td>
					<td><?php echo $gens; ?></td>
					<td><?php echo $this->populacija[$el[0]]['BIN']; ?></td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}

	protected function intervali_mutejosiem_geniem() {
		?>
		<h4>Intervāli mutējošiem gēniem</h4>
		<?php $mutacijas_int = array();?>

		<table border=1>
			<tr>
				<th>Gēns</th>
				<th>Varbūtība tikt izvēlētam</th>
				<th>Kumulatīva varbūtība</th>
				<th>Intervāls</th>
			</tr>
			<?php $kumul = 0;
			for($i = 1; $i <= 2*$this->dalijuma_skaitlis; $i++): $prev = $kumul; $kumul += 1/(2*$this->dalijuma_skaitlis); ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo prbsk(1/(2*$this->dalijuma_skaitlis)); ?></td>
					<td><?php echo prbsk($kumul); ?></td>
					<?php if($i==0): ?><td><?php echo "[".prbsk($prev).";".$kumul."]"; ?></td>
					<?php else: ?><td><?php echo "(".prbsk($prev).";".prbsk($kumul)."]"; ?></td>
					<?php endif;?>
				</tr>
			<?php 
			$mutacijas_int[''.$kumul] = $i; 
			endfor; ?>
		</table>
		<?php
		$this->mutacijas_genu_intervali = $mutacijas_int;
	}

	protected function individu_parbaude_mutacijai() {
		$mutacijas_el = array(); ?>
		<h4>Indivīdu pārbaude mutācijai</h4>

		<table border=1>
			<tr>
				<th>Indivīds</th>
				<th>Gadījuma skaitlis</th>
				<th>Mutēs?</th>
			</tr>

			<?php foreach($this->populacija as $key => $val): $randZ = $this->rand->generate(); ?>
				<tr>
					<td><?php echo $val['BIN']; ?></td>
					<?php 
						$mutes = false;
						if(floatval($randZ) <= $this->mutacijas_varbutiba) {
							$mutacijas_el[] = array($key, false); // false - ir populacija, ne berns
							$mutes = true;
						}
					?>
					<td>
						<?php echo $randZ; ?> 
						<?php echo ($mutes == true) ? '&le;' : '>'; ?> 
						<?php echo $this->mutacijas_varbutiba; ?>
					</td>

					<td>
						<?php echo ($mutes == true) ? 'Jā' : 'Nē'; ?> 
					</td>

				</tr>
			<?php endforeach; ?>

			<?php foreach($this->berni as $key => $val): $randZ = $this->rand->generate(); ?>
				<tr>
					<td><?php echo $val; ?></td>
					<?php 
						$mutes = false;
						if(floatval($randZ) <= $this->mutacijas_varbutiba) {
							$mutacijas_el[] = array($key, true); // false - ir populacija, ne berns
							$mutes = true;
						}
					?>
					<td>
						<?php echo $randZ; ?> 
						<?php echo ($mutes == true) ? '&le;' : '>'; ?> 
						<?php echo $this->mutacijas_varbutiba; ?>
					</td>

					<td>
						<?php echo ($mutes == true) ? 'Jā' : 'Nē'; ?> 
					</td>

				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		$this->mutacijas_elementi = $mutacijas_el;
	}

	protected function individu_krustosana() {
		// Dabujam gadijuma skaitlus un nosakam punktus

		$krustosana = $this->krustosanas_pari; $paris = array();


		$pari_krusosanai = (count($krustosana) % 2 > 0) ? (count($krustosana) - 1) / 2 : (count($krustosana)) / 2;
		for($i = 0; $i < $pari_krusosanai; $i++) {
			
			if($i > 0 && $krustosana[2*$i][1] == $krustosana[2*$i+1][1] || $i == 0 && $krustosana[0][1] == $krustosana[1][1]) {
				$paris[$i] = array('-', '-', '-', '-');
			}
			else {
				$paris[$i] = array($this->rand->generate(), $this->rand->generate());

				$randVal = floatval($paris[$i][0]); $randKeys = array_keys($this->krustosanas_intervali[0]);

					for($j = 0; $j < count($this->krustosanas_intervali[0]); $j++) {
						if($randVal < floatval($randKeys[$j])) {
							if($j > 0) $paris[$i][] = $this->krustosanas_intervali[0][$randKeys[$j]];
							else $paris[$i][] = $this->krustosanas_intervali[0][$randKeys[0]];
							break;
						}
					}

				$randVal = floatval($paris[$i][1]); $randKeys = array_keys($this->krustosanas_intervali[1]);
					for($j = 0; $j < count($this->krustosanas_intervali[1]); $j++) {
						if($randVal < floatval($randKeys[$j])) {
							if($j > 0) $paris[$i][] = $this->dalijuma_skaitlis + $this->krustosanas_intervali[1][$randKeys[$j]];
							else $paris[$i][] = $this->dalijuma_skaitlis + $this->krustosanas_intervali[1][$randKeys[0]];
							break;
						}
					}
			}

			if($i > 0) $paris[$i][] = array($krustosana[2*$i], $krustosana[2*$i+1]);
			else $paris[$i][] = array($krustosana[0], $krustosana[1]);
		}

		// Krustojam
		$berni = array();
		foreach($paris as $key => $p) {
			
			$start = $p[2]; $end = $p[3];
			if($start == '-' || $end == '-') {
				$this->berni[] = $this->populacija[$p[4][0][1]]['BIN'];
				$this->berni[] = $this->populacija[$p[4][1][1]]['BIN'];
			}
			else {
				$pirmais = str_split($this->populacija[$p[4][0][1]]['BIN']);
				$otrais = str_split($this->populacija[$p[4][1][1]]['BIN']);

				for($i = $start; $i <= $end; $i++) {
					$tmp = $pirmais[$i];
					$pirmais[$i] = $otrais[$i];
					$otrais[$i] = $tmp;
				}
				$this->berni[] = implode('', $pirmais);
				$this->berni[] = implode('', $otrais);
			}


		}
		?>
		<h4>Indivīdu krustošana</h4>
		<table border=1>
			<tr>
				<th rowspan=2>Pāris</th>
				<th rowspan=2>BIN</th>
				<th colspan=4>Krustošanās punkti</th>
				<th rowspan=2>Bērni BIN</th>
			</tr>
			<tr>
				<th>G.sk.</th>
				<th>1. punkts</th>
				<th>G.sk.</th>
				<th>2. punkts</th>
			</tr>
			<?php $c = 0;
			for($i = 0; $i < $pari_krusosanai * 2; $i++): ?>
				<tr>
					<?php if($i % 2 == 0): $c++; ?>
						<td rowspan=2><?php echo $c; ?></td>
					<?php endif; ?>

					<td>
						<?php echo $this->populacija[$krustosana[$i][1]]['BIN']; ?>
					</td>

					<?php if($i % 2 == 0): ?>

						<td rowspan=2><?php echo $paris[$c-1][0]; ?></td>
						<?php if($paris[$c-1][2] != '-'): ?><td rowspan=2>Aiz <?php echo $paris[$c-1][2]; ?>. gēna</td>
						<?php else: ?><td rowspan=2>-</td><?php endif; ?>

						<td rowspan=2><?php echo $paris[$c-1][1]; ?></td>
						<?php if($paris[$c-1][3] != '-'): ?><td rowspan=2>Aiz <?php echo $paris[$c-1][3]; ?>. gēna</td>
						<?php else: ?><td rowspan=2>-</td><?php endif; ?>

					<?php endif; ?>

					<td>
						<?php echo $this->berni[$i]; ?>
					</td>

				</tr>
			<?php endfor; ?>
		</table>
		<?php
	}

	protected function krustosanas_intervali() {
		?>
		<h4>Krustošanas pozīciju intervāli</h4>
		<table border=1>
			<tr>
				<th colspan=2>Pirmais krustošanās punkts</th>
			</tr>
			<tr>
				<th>Pozīcija</th>
				<th>Intervāls</th>
				<?php 

					$kumul = 0; 
					$varb = 1 / $this->dalijuma_skaitlis;
					for($i = 1; $i <= $this->dalijuma_skaitlis; $i++ ): 
						$prev = $kumul; $kumul += $varb; 
						$this->krustosanas_intervali[0][''.$kumul] = $i;
					?>

						<tr>
							<td>Aiz <?php echo $i; ?>. gēna</td>
							<td>
								<?php echo prbsk($prev); ?> 
								<?php if($i == 1): ?>&le;<?php else: ?>&lt;<?php endif; ?>
								gad.sk. &le; 
								<?php echo prbsk($kumul); ?>
							</td>
						</tr>
				<?php endfor; ?>
			</tr>
		</table>

		<br />

		<table border=1>
			<tr>
				<th colspan=2>Otrais krustošanās punkts</th>
			</tr>
			<tr>
				<th>Pozīcija</th>
				<th>Intervāls</th>
				<?php 
					$kumul = 0; $varb = 1 / ($this->dalijuma_skaitlis - 1);
					for($i = 1; $i <= $this->dalijuma_skaitlis -1; $i++ ): 
						$prev = $kumul; $kumul += $varb; 
						$this->krustosanas_intervali[1][''.$kumul] = $i;
					?>
						<tr>
							<td>Aiz <?php echo $this->dalijuma_skaitlis+$i; ?>. gēna</td>
							<td>
								<?php echo prbsk($prev); ?> 
								<?php if($i == 1): ?>&le;<?php else: ?>&lt;<?php endif; ?>
								gad.sk. &le; 
								<?php echo prbsk($kumul); ?>
							</td>
						</tr>
				<?php endfor; ?>
			</tr>
		</table>
		<?php
	}

	protected function turnirs_paru_veidosana() {
		?>
		<h4>Pāru Veidošana</h4>
		<table border="1">
			<tr>
				<th>Gadījuma skaitlis no tabulas</th>
				<th>Izvēlētie indivīdi un piemērotība</th>
				<th>Uzvarētājs</th>
				<th>Pāris</th>
			</tr>
			
			<?php 
			$c = 0; $ParuVeidosana = array();
			for($i = 1; $i <= 2*count($this->populacija); $i++) {
				$randVal = floatval($this->rand->generate()); $randKeys = array_keys($this->individiem_intervali); $randEl = 0;
				for($j = 0; $j < count($this->individiem_intervali); $j++) {
					if($randVal < floatval($randKeys[$j])) {
						$randKeys[$j];
						if($j > 0) $randEl = $this->individiem_intervali[$randKeys[$j-1]];
						else $randEl = $this->individiem_intervali[$randKeys[0]];
						break;
					}
				}
				$ParuVeidosana[] = array($randVal, $randEl);
			}

			$KrustosanasPari = $ParuVeidosana;
			for($i = 0; $i < count($ParuVeidosana); $i++):  ?>
			<tr>
				<td><?php echo $ParuVeidosana[$i][0]; ?></td>
				<td><?php echo ($ParuVeidosana[$i][1]+1). " (".skaitlis($this->populacija[$ParuVeidosana[$i][1]]['piemerotiba']).")"; ?></td>

				<?php if($i % 2 == 0): ?>
					<?php if($this->populacija[$ParuVeidosana[$i][1]]['piemerotiba'] > $this->populacija[$ParuVeidosana[$i+1][1]]['piemerotiba']): ?>
						<td rowspan=2><?php echo $ParuVeidosana[$i][1]+1; ?></td>
						<?php unset($KrustosanasPari[$i+1]); ?>
					<?php else: ?>
						<td rowspan=2><?php echo $ParuVeidosana[$i+1][1]+1; ?></td>
						<?php unset($KrustosanasPari[$i]); ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php if($i % 4 == 0 && $i != count($ParuVeidosana)): $c++;?>
					<td rowspan=4><?php echo $c; ?></td>
				<?php endif; ?>
			</tr>
			<?php endfor; ?>
		</table>
		<?php
		$krustosana = array();
		foreach($KrustosanasPari as $val) { // atslegas neiet viena pec otras, reset
			$krustosana[] = $val;
		}
		$this->krustosanas_pari = $krustosana;
	}

	protected function rulete_paru_veidosana() {
		?>
		<h4>Pāru Veidošana</h4>
		<table border="1">
			<tr>
				<th>Gadījuma skaitlis no tabulas</th>
				<th>Izvēlētie indivīdi un piemērotība</th>
				<th>Pāris</th>
			</tr>
			
			<?php 
			$c = 0; $ParuVeidosana = array();
			for($i = 1; $i <= count($this->populacija); $i++) {
				$randVal = floatval($this->rand->generate()); $randKeys = array_keys($this->individiem_intervali); $randEl = 0;
				for($j = 0; $j < count($this->individiem_intervali); $j++) {
					if($randVal < floatval($randKeys[$j])) {
						$randKeys[$j];
						if($j > 0) $randEl = $this->individiem_intervali[$randKeys[$j-1]];
						else $randEl = $this->individiem_intervali[$randKeys[0]];
						break;
					}
				}
				$ParuVeidosana[] = array($randVal, $randEl);
			}

			for($i = 0; $i < count($ParuVeidosana); $i++):  ?>
			<tr>
				<td><?php echo $ParuVeidosana[$i][0]; ?></td>
				<td><?php echo $ParuVeidosana[$i][1]. " (".skaitlis($this->populacija[$ParuVeidosana[$i][1]]['piemerotiba']).")"; ?></td>

				<?php if($i % 2 == 0 && $i != count($ParuVeidosana)): $c++;?>
					<td rowspan=2><?php echo $c; ?></td>
				<?php endif; ?>
			</tr>
			<?php endfor; ?>
		</table>
		<?php
		$krustosana = array();
		foreach($ParuVeidosana as $val) { // atslegas neiet viena pec otras, reset
			$krustosana[] = $val;
		}
		$this->krustosanas_pari = $krustosana;
	}

	protected function individiem_aprekinatie_intervali() {
		$kumul = 0;
		for($i = 1; $i <= count($this->populacija); $i++) {
			if($this->selekcija == 'turnirs'):
				$probability = 1 / count($this->populacija);
				$prev = $kumul;
				$kumul += $probability;
				$this->individiem_intervali[''.$kumul] = $i;
			else:
				$probability = $this->populacija[$i-1]['piemerotiba'] / $this->populacijas_info['sum'];
				$prev = $kumul;
				$kumul += $probability;
				$this->individiem_intervali[''.$kumul] = $i;
			endif;
		}
	}

	protected function individiem_aprekinatie_intervali_izvade() {
		?>
		<h4>Indivīdiem aprēķinātie intervāli</h4>

		<table border=1>
			<tr>
				<th>Indivīds</th>
				<th>Varbūtība tikt izvēlētam</th>
				<th>Kumulatīvā varbūtība</th>
				<th>Intervāls</th>
			</tr>
			<?php 
			$kumul = 0;
			for($i = 1; $i <= count($this->populacija); $i++):
				
				if($this->selekcija == 'turnirs'):
					$probability = 1 / count($this->populacija);
					$prev = $kumul;
					$kumul += $probability;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php
				else:

					$probability = $this->populacija[$i-1]['piemerotiba'] / $this->populacijas_info['sum'];
					$prev = $kumul;
					$kumul += $probability;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php



				endif;

			endfor; ?>
		</table>
		<?php
	}

	protected function populacijas_piemerotiba() {
		$this->populacijas_info = array('sum' => 0,'max' => array('key' => 0, 'val' => 0),'avg' => 0);
		foreach($this->populacija as $key => $individs) {
			$individs['xd'] = real2bin($individs['x']);
			$individs['yd'] = real2bin($individs['y']);

			$individs['binx'] = decbin($individs['xd']);
			$individs['biny'] = decbin($individs['yd']);

			if(strlen($individs['binx']) < $this->dalijuma_skaitlis) {
				$dx = $this->dalijuma_skaitlis - strlen($individs['binx']);
				for($i = 0; $i < $dx; $i++) $individs['binx'] = '0'.$individs['binx'];
			}

			if(strlen($individs['biny']) < $this->dalijuma_skaitlis) {
				$dy = $this->dalijuma_skaitlis - strlen($individs['biny']);
				for($i = 0; $i < $dy; $i++) $individs['biny'] = '0'.$individs['biny'];
			}

			$individs['BIN'] = $individs['binx']." ".$individs['biny'];
			$individs['piemerotiba'] = piemerotiba($individs['x'], $individs['y']);

			/* populacijas info */
			$this->populacijas_info['sum'] += $individs['piemerotiba']; // Piemērotības summa
			if($individs['piemerotiba'] > $this->populacijas_info['max']['val']) { // meklējam maksimālo vērtību
				$this->populacijas_info['max']['val'] = $individs['piemerotiba'];
				$this->populacijas_info['max']['key'] = $key;
			}

			$this->populacija[$key] = $individs;
		}
		$this->populacijas_info['avg'] = $this->populacijas_info['sum'] / count($this->populacija);
		$this->populacijas_info = $this->populacijas_info;
	}

	protected function populacijas_izvade() {
		?>
		<table border=1>
			<tr>
				<th>Nr</th>
				<th>X</th>
				<th>Y</th>
				<th>X'</th>
				<th>Y'</th>
				<th width=300>BIN</th>
				<th>Piemērotība</th>
			</tr>	
			
			<?php 
			foreach($this->populacija as $key => $individs): ?>
				<tr>
					<td><?php echo $key + 1; ?></td>
					<td><?php echo skaitlis($individs['x']); ?></td>
					<td><?php echo skaitlis($individs['y']); ?></td>			
					<td><?php echo skaitlis($individs['xd']); ?></td>
					<td><?php echo skaitlis($individs['yd']); ?></td>
					<td align="center"><?php echo $individs['BIN']; ?></td>
					<td><?php echo skaitlis($individs['piemerotiba']); ?></td>
				</tr>

			<?php endforeach; ?>
		</table>
		<p>Kopējā piemērotība: <?php echo skaitlis($this->populacijas_info['sum']); ?></p>
		<p>MAX piemērotība: <?php echo skaitlis($this->populacijas_info['max']['val']); ?></p>
		<p>Vidējā piemērotība: <?php echo skaitlis($this->populacijas_info['avg']); ?></p>
		<?php
	}

}