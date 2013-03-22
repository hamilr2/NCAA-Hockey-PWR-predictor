<pre><?php
include('include.php');

set_time_limit(0);

mysql_connect("localhost", "root", "");
mysql_select_db("pwr2013");

$file = fopen('c:\xampp\htdocs\hockey_predictor\schedule.txt', 'r');
$contents = stream_get_contents($file);
/* preg_match('/small">(.*?)<\/pre>/s', $contents, $match);
  $match = trim($match[1]); */
$match = $contents;

$lines = explode("\n", $match);

// Initialize variables
$teamsMap = array();
$teamsMapIndex = 1;
$teams = array();
for ($i = 0; $i < 60; $i++) {
	$teams[$i] = array();
	$teams[$i]['w'] = array();
	$teams[$i]['l'] = array();
	$teams[$i]['t'] = array();
	$teams[$i]['o'] = array();
	$teams[$i]['c'] = array();
	$teams[$i]['ctot'] = 0;
}

// Read schedule from file
foreach ($lines as $line) {
	if (substr($line, 63, 2) == 'ex' || substr($line, 30, 2) == '  ')
		continue;
	$t1 = trim(substr($line, 11, 19));
	$t2 = trim(substr($line, 36, 19));

	if ($t1 == 'TBD' || $t2 == 'TBD')
		continue;

	if (!$teamsMap[$t1]) {
		$teamsMap[$t1] = $teamsMapIndex;
		$t1 = $teamsMapIndex++;
	} else {
		$t1 = $teamsMap[$t1];
	}

	if (!$teamsMap[$t2]) {
		$teamsMap[$t2] = $teamsMapIndex;
		$t2 = $teamsMapIndex++;
	} else {
		$t2 = $teamsMap[$t2];
	}

	$s1 = intval(substr($line, 30, 2));
	$s2 = intval(substr($line, 55, 2));
	if ($s1 > $s2) {
		$teams[$t1]['w'][] = $t2;
		$teams[$t2]['l'][] = $t1;
	} else if ($s2 > $s1) {
		$teams[$t1]['l'][] = $t2;
		$teams[$t2]['w'][] = $t1;
	} else {
		$teams[$t1]['t'][] = $t2;
		$teams[$t2]['t'][] = $t1;
	}
}

// decorate teams
foreach ($teamsMap as $name => $id) {
	$teamsMap[$id] = $name;
	$teams[$id]['name'] = $name;
	$teams[$id]['id'] = $id;
}

// generate opponents list/array
foreach ($teams as $id => $team) {
	if ($id == 0)
		continue;
	foreach ($teams[$id]['w'] as $win) {
		if ($teams[$id]['o'][$win] == null)
			$teams[$id]['o'][$win] = array();
		$teams[$id]['o'][$win][] = 'w';
	}
	foreach ($teams[$id]['l'] as $loss) {
		if (!$teams[$id]['o'][$loss])
			$teams[$id]['o'][$loss] = array();
		$teams[$id]['o'][$loss][] = 'l';
	}
	foreach ($teams[$id]['t'] as $tie) {
		if (!$teams[$id]['o'][$tie])
			$teams[$id]['o'][$tie] = array();
		$teams[$id]['o'][$tie][] = 't';
	}
}

unset($teams[0]); // it got added at some point?

$testInput = explode(',', '45,43,45,12,25,12,46,30,46,2,56,2,57,16,16,33,33,16');

//doPWR($teams,$input,true);
//exit(0);

print_R($teamsMap);

for ($i = 393216;; $i--) {
	$id = $i;
	break;

	// fetch an outcome
	$query = "SELECT * FROM outcomes LEFT JOIN results ON outcomes.id = results.id WHERE outcomes.id=$id";
	$ref = mysql_query($query);
	$result = mysql_fetch_assoc($ref);

	if ($result[t1] != null) {
		echo 'CACHE HIT ';
		continue;
	}
	$input = explode(',', $result['str']);

	$seeding = doPWR($teams, $input, true);

	$query = "INSERT INTO results (id";
	$columns = "";
	$data = "";

	foreach ($seeding as $col => $seed) {
		$columns .= ', ' . $col;
		$data .= ', ' . $seed;
	}

	$query .= $columns . ") VALUES (" . $id . $data . ');';
	//$ref = mysql_query($query)  or die("Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

	echo $i . ' ';

	if($i %100 == 0) {
		flush();
	}
	break;
}
?>
