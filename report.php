<style type="text/css">
	body
	{
		font-family:Arial;
	}
	.inh
	{
		background-color: #BBFFBB;
	}
	.outh
	{
		background-color:#FFBBBB;
	}
	.ind
	{
		background-color:#DDFFDD;
	}
	.outd
	{
		background-color:#FFDDDD;
	}
	td{
		margin:0px;
		padding:5px;
	}


</style><?php

set_time_limit(0);
include('include.php');

mysql_connect("localhost", "root", "");
mysql_select_db("pwr2013");

$query = "SELECT * FROM outcomes LEFT JOIN results ON outcomes.id = results.id WHERE outcomes.g15='19' AND outcomes.g13='16'";
$resource = mysql_query($query);

$teams = array();

for ($i = 1; $i < 60; $i++) {
	$teams[$i] = array();
	$teams[$i]['seeds'] = array();
	$teams[$i]['name'] = getTeamName($i);
}
$count = 0;
while ($result = mysql_fetch_assoc($resource)) {
	for ($i = 1; $i < 60; $i++) {
		$seed = $result['t' . $i];
		if (count($teams[$i]['seeds'][$seed]) == 0) {
			$teams[$i]['seeds'][$seed] = array('weighted' => 0, 'unweighted' => 0);
		}
		$teams[$i]['seeds'][$seed]['unweighted']++;
		$teams[$i]['seeds'][$seed]['weighted'] += $result['krach'];
	}
	$count++;
	/*if ($count == 10000) {
		break;
	}*/
}

foreach ($teams as &$team) {
	$likelySeed = 16;
	$likelySeedCount = 0;
	$inSum = 0;
	foreach ($team['seeds'] as $key => $seed) {
		if($key > 16) continue;
		if ($seed['weighted'] > $likelySeedCount) {
			$likelySeed = $key;
			$likelySeedCount = $seed['weighted'];
		}
		if ($key <= 16) {
			$inSum += $seed['weighted'];
		}
	}
	if($likelySeedCount == 0) {
		$likelySeed = '-';
	}
	$team['likelySeed'] = $likelySeed;
	$team['percentIn'] = $inSum;
}

function compare($x, $y) {
	if(round($x['percentIn'], 5) > round($y['percentIn'],5)) {
		return -1;
	} else if (round($x['percentIn'],5) < round($y['percentIn'],5)) {
		return 1;
	} else {
		if($x['likelySeed'] - $y['likelySeed'] != 0) {
			return $x['likelySeed'] - $y['likelySeed'];
		} else {
			$xBestSeed = 59;
			foreach ($x['seeds'] as $key => $seed) {
				if ($key < $xBestSeed) {
					$xBestSeed = $key;
				}
			}
			$yBestSeed = 59;
			foreach ($y['seeds'] as $key => $seed) {
				if ($key < $yBestSeed) {
					$yBestSeed = $key;
				}
			}
			if ($xBestSeed < $yBestSeed) {
				return -1;
			} else if ($yBestSeed < $xBestSeed) {
				return 1;
			} else {
				if ($x['seeds'][$xBestSeed]['weighted'] > $y['seeds'][$yBestSeed]['weighted']) {
					return -1;
				} else {
					return 1;
				}
			}
		}
	}
}

uasort($teams, 'compare');

//print_r($teams);

$highPercent = $teams[5]['percentIn']*.01;

echo("<h1>Overall Predictions</h1>");
echo("<p>All weighted probabilities are generated using KRACH comparisons. The KRACH is static, it does not regenerate after each game. The current method of predicting a tie game using KRACH isn't the best, it overstates the likelihood of a tie against two closely matched teams, feel free to email reillyhamilton@gmail.com with suggestions.</p>\n");
echo '<p>KRACH has been regenerated following the WCHA Quarterfinals.</p>';
echo("<table><tr><td>Team</td><td>Likelihood</td><td>Most Likely Seed if in Tournament</td></tr>");
foreach ($teams as &$team) {
	echo("<tr>");
	echo('<td><a href="#' . $team['name'] . '">' . $team['name'] . '</a></td>');
	echo("<td>" . sprintf("%.4f", $team['percentIn'] / $highPercent) . "%</td>");
	echo("<td>" . $team['likelySeed'] . "</td>");
	echo("</tr>\n");
}
echo("</table>");

foreach ($teams as &$team) {
	echo('<h1><a name="' . $team['name'] . '">' . $team['name'] . '</a> - ' . sprintf("%.4f", $team['percentIn'] / $highPercent) . "% chance of being in the tournament</h1>\n");
	echo("<table>");
	$instring = '';
	$outstring = '';
	for ($i = 1; $i <= 59; $i++) {
		if (count($team['seeds'][$i]) > 0) {
			$string = "<td>$i</td><td>" . sprintf("%.4f", $team['seeds'][$i]['weighted'] / $highPercent) . "%</td><td>" . sprintf("%.4f", $team['seeds'][$i]['unweighted'] / $count * 100) . "%</td><td>" . $team['seeds'][$i]['unweighted'] . "</td></tr>\n";
			if ($i <= 16) {
				$instring .= '<tr class="ind">' . $string;
			} else {
				$outstring .= '<tr class="outd">' . $string;
			}
		}
	}
	if (strlen($instring) > 0) {
		echo("<tr class=\"inh\"><td>Seed</td><td>Weighted</td><td>Unweighted</td><td>Permutations</td></tr>\n");
		echo $instring;
	}
	if (strlen($outstring) > 0) {
		echo("<tr class=\"outh\"><td>Seed</td><td>Weighted</td><td>Unweighted</td><td>Permutations</td></tr>\n");
		echo $outstring;
	}
	echo("</table>");
}
?>
