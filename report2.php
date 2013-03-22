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
	td:last-of-type,
	th:last-of-type {
		display:none;
	}


</style><?php

set_time_limit(0);
include('include.php');

mysql_connect("localhost", "root", "");
mysql_select_db("pwr2013");

$query = "SELECT * FROM outcomes LEFT JOIN results ON outcomes.id = results.id WHERE results.t22 < '17' AND outcomes.g15 = '19' AND outcomes.g13 = '16'";
$resource = mysql_query($query);

$games = array();

for ($i = 0; $i <= 17; $i++) {
	$games[$i] = array();
}
$count = 0;
$totalWeight = 0;

while ($result = mysql_fetch_assoc($resource)) {
	$wchaf1t = 36;
	$wchaf2t = 31;
	$gdb[0] = array(45 => 27, 27 => 45);
	$gdb[1] = array(43 => 4, 4 => 43);
	$gdb[2] = array($result['g0'] => $result['g1'], $result['g1'] => $result['g0']);
	$gdb[3] = array(12 => 37, 37 => 12);
	$gdb[4] = array(25 => 38, 38 => 25);
	$gdb[5] = array($result['g3'] => $result['g4'], $result['g4'] => $result['g3']);
	$gdb[6] = array(2 => 57, 57 => 2);
	$gdb[7] = array(56 => 5, 5 => 56);
	$gdb[8] = array($result['g6'] => $result['g7'], $result['g7'] => $result['g6']);
	$gdb[9] = array($gdb['6'][$result['g6']] => $gdb['7'][$result['g7']], $gdb['7'][$result['g7']] => $gdb['6'][$result['g6']]);
	$gdb[10] = array(46 => 47, 47 => 46);
	$gdb[11] = array(30 => 14, 14 => 30);
	$gdb[12] = array($result['g10'] => $result['g11'], $result['g11'] => $result['g10']);
	$gdb[13] = array(16 => 53, 53 => 16);
	$gdb[14] = array($result['g13'] => $wchaf1t, $wchaf1t => $result['g13']);
	$gdb[15] = array(33 => 19, 19 => 33);
	$gdb[16] = array($result['g15'] => $wchaf2t, $wchaf2t => $result['g15']);
	$gdb[17] = array($result['g14'] => $result['g16'], $result['g16'] => $result['g14']);

	for ($i = 0; $i <= 17; $i++) {
		$winner = $result['g' . $i];
		if($winner != -1) {
			if(count($games[$i][$winner])==0){
				$games[$i][$winner] == array();
			}
			if(count($games[$i][$winner][$gdb[$i][$winner]]) == 0) {
				$games[$i][$winner][$gdb[$i][$winner]] = array('outcomes'=>0,'weighted'=>0);
			}
			$games[$i][$winner][$gdb[$i][$winner]]['outcomes']++;
			$games[$i][$winner][$gdb[$i][$winner]]['weighted'] += $result['krach'];
		} else {
			if(count($games[$i]['-1']) == 0) {
				$games[$i]['-1'] = array();
			}
			if(count($games[$i]['-1'][$gdb[6][$result['g6']]]) == 0) {
				$games[$i]['-1'][$gdb[6][$result['g6']]] = array();
			}
			if(count($games[$i]['-1'][$gdb[6][$result['g6']]][$gdb[7][$result['g7']]]) == 0) {
				$games[$i]['-1'][$gdb[6][$result['g6']]][$gdb[7][$result['g7']]] = array('outcomes'=>0,'weighted'=>0);
			}
			$games[$i]['-1'][$gdb[6][$result['g6']]][$gdb[7][$result['g7']]]['outcomes']++;
			$games[$i]['-1'][$gdb[6][$result['g6']]][$gdb[7][$result['g7']]]['weighted']+=$result['krach'];
		}

	}
	$totalWeight += $result['krach'];
	$count++;
}

//print_r($games);

$gName = array();
$gName[0] = 'AHA Semi #1';
$gName[1] = 'AHA Semi #2';
$gName[2] = 'AHA Final';
$gName[3] = 'CCHA Semi #1';
$gName[4] = 'CCHA Semi #2';
$gName[5] = 'CCHA Final';
$gName[6] = 'ECAC Semi #1';
$gName[7] = 'ECAC Semi #2';
$gName[8] = 'ECAC Final';
$gName[9] = 'ECAC Consolation';
$gName[10] = 'HEA Semi #1';
$gName[11] = 'HEA Semi #2';
$gName[12] = 'HEA Final';
$gName[13] = 'WCHA Quarter #1';
$gName[14] = 'WCHA Semi #1';
$gName[15] = 'WCHA Quarter #2';
$gName[16] = 'WCHA Semi #2';
$gName[17] = 'WCHA Final';

echo '<h2>Game-by-game breakdown of outcomes when RPI is in the tournament</h2>';
echo '<p>This page lists the number of outcomes for each result of each game when RPI is in the tournament. For each game, the number represents the number of in-the-tournament outcomes that RPI would have remaining if that result occurred. The result with the greatest number of outcomes is the preferred; the disparity between the numbers of outcomes illustrates how important that game is to RPI. No KRACH weighting has been performed. Games that have gone final will have only one 100% result. Any game that is missing a result represents a case where RPI cannot make the tournament if that (missing) result occurs. There are currently no missing results -- no single result can spell doom for Rensselaer.';
echo '<p>Total outcomes where RPI is in the tournament: ' . $count . ' out of 98304.</p>';
echo '<p>Results following both WCHA Quarterfinals.</p>';

foreach($games as $key=>&$game) {

	echo '<h2>' . $gName[$key] . '</h2><table>';
	echo '<tr><th>Result</th><th>Outcomes</th><th>Percent</th><th>KRACH weighted</th><tr>';
	$gtot = 0;
	foreach($game as $winner => $wins) {
		if($winner != -1) {
			foreach($wins as $loser => $num) {
				echo '<tr><td>' . getTeamName($winner) . ' defeats ' . getTeamName($loser) . '</td><td>' . $num['outcomes'] . '</td><td>' . sprintf("%.2f",$num['outcomes']/$count*100) . '%</td><td>' . sprintf("%.2f",$num['weighted']/$totalWeight*100) . '%</td></tr>';
			}
		} else {
			foreach($wins as $tie => $tie1) {
				foreach($tie1 as $tie2 => $num) {
					echo '<tr><td>' . getTeamName($tie) . ' ties ' . getTeamName($tie2) . '</td><td>' . $num['outcomes'] . '</td><td>' . sprintf("%.2f",$num['outcomes']/$count*100) . '%</td><td>' . sprintf("%.2f",$num['weighted']/$totalWeight*100) . '%</td></tr>';
				}
			}
		}
	}
	echo '</table>';
	//echo '<li>' . getTeamName($winner) . ' - ' . $wins . '</li>';

}

?>
