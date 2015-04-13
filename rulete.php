<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dāvis Krēgers &mdash; 121RDB768</title>
	<style>
		body {
			padding-top: 50px;
		}
		nav {
			width: 100%;
			background: #cdcdcd;
			position: fixed;
			height: 50px;
			margin-top: -50px;
		}
		nav ul li {
			display: inline;
			margin-left: 20px;
		}
		nav ul li a {
			color: #000;
			font-weight: bold;
			text-decoration: none;
		}
	</style>
</head>
<body>

<nav>
	<ul>
		<?php for($i = 0; $i <= 2; $i++): ?>
			<li><a href="#generacija-<?php echo $i; ?>"><?php echo $i+1; ?>. ģenerācija</a></li>
		<?php endfor; ?>
		<li><a href="index.php">Turnīra selekcija</a></li>
	</ul>
</nav>

<h1>Ruletes selekcija</h1>

<?php 

require 'helpers.php';
require 'rand.class.php';
require 'algoritms.class.php';

$rand = new RND_Skaitlis();

/* Sākuma mainīgie */

$intv = array(-12.8, 12.7);
$precizitate = 0.1;
$max_val = 1 + (($intv[1] - $intv[0]) / $precizitate);
$bin_max_val = decbin($max_val);
$dalsk = strlen($bin_max_val);

$sakuma = array(
	array('x' => -12.8,  'y' => -12.8),
	array('x' => -4,  'y' => 12.7),
	array('x' => 12.7,  'y' => -3.2),
	array('x' => 0,  'y' => 5),
	array('x' => 5,  'y' => 3),
	array('x' => 0,  'y' => 0),
	array('x' => -1,  'y' => 1),
	array('x' => 1,  'y' => -1),
	array('x' => 0,  'y' => -12.8),
	array('x' => 12.7,  'y' => 12.7)
);

$parametri = array(
	'intervals' => $intv,
	'precizitate' => 0.1,
	'max_vertiba' => $max_val,
	'max_vertiba_binary' => $bin_max_val,
	'dalijuma_skaitlis' => $dalsk,
	'mutacijas_varbutiba' => 0.1,
	'selekcija' => 'rulete',
	'sakuma_populacija' => $sakuma,
	'generacijas' => 2
);

$algoritms = new Algoritms($parametri);



?>
</body>
</html>
