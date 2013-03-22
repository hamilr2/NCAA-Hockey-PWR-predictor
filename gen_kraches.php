<?php
set_time_limit(0);
$queries = 0;
mysql_connect("localhost", "root", "");
mysql_select_db("pwr2013");


function addMatchup(&$matchups, $winner, $loser, $tie = null) {
	if($tie == -1) {
		$matchups .= $winner . 't' . $loser . ';';
	} else {
		$matchups .= $winner . ',' . $loser . ';';
	}
}

function createMatchups($o) {

	$matchupString = "";

	$ahas1 = array(45 => 27, 27 => 45);
	$ahas2 = array(43 => 4, 4 => 43);
	$cchas1 = array(12 => 37, 37 => 12);
	$cchas2 = array(25 => 38, 38 => 25);
	$heas1 = array(46 => 47, 47 => 46);
	$heas2 = array(30 => 14, 14 => 30);
	$ecacs1 = array(2 => 57, 57 => 2);
	$ecacs2 = array(56 => 5, 5 => 56);
	$wchas1 = array(16 => 53, 53 => 16);
	$wchas2 = array(33 => 19, 19 => 33);
	$wchaf1t = 36;
	$wchaf2t = 31;

	addMatchup($matchupString, $o[0], $ahas1[$o[0]]);
	addMatchup($matchupString, $o[1], $ahas2[$o[1]]);
	$ahaf = array($o[0] => $o[1], $o[1] => $o[0]);
	addMatchup($matchupString, $o[2], $ahaf[$o[2]]);

	addMatchup($matchupString, $o[3], $cchas1[$o[3]]);
	addMatchup($matchupString, $o[4], $cchas2[$o[4]]);
	$cchaf = array($o[3] => $o[4], $o[4] => $o[3]);
	addMatchup($matchupString, $o[5], $cchaf[$o[5]]);

	addMatchup($matchupString, $o[6], $ecacs1[$o[6]]);
	addMatchup($matchupString, $o[7], $ecacs2[$o[7]]);
	$ecacf = array($o[6] => $o[7], $o[7] => $o[6]);
	addMatchup($matchupString, $o[8], $ecacf[$o[8]]);

	if ($o[9] == -1) {
		addMatchup($matchupString, $ecacs1[$o[6]], $ecacs2[$o[7]], -1);
	} else {
		$ecacc = array($ecacs1[$o[6]] => $ecacs2[$o[7]], $ecacs2[$o[7]] => $ecacs1[$o[6]]);
		addMatchup($matchupString, $o[9], $ecacc[$o[9]]);
	}

	addMatchup($matchupString, $o[10], $heas1[$o[10]]);
	addMatchup($matchupString, $o[11], $heas2[$o[11]]);
	$heaf = array($o[10] => $o[11], $o[11] => $o[10]);
	addMatchup($matchupString, $o[12], $heaf[$o[12]]);

	addMatchup($matchupString, $o[13], $wchas1[$o[13]]);
	$wchaf1 = array($o[13] => $wchaf1t, $wchaf1t => $o[13]);
	addMatchup($matchupString, $o[14], $wchaf1[$o[14]]);
	addMatchup($matchupString, $o[15], $wchas2[$o[15]]);
	$wchaf2 = array($o[15] => $wchaf2t, $wchaf2t => $o[15]);
	addMatchup($matchupString, $o[16], $wchaf2[$o[16]]);
	$wchach = array($o[14] => $o[16], $o[16] => $o[14]);
	addMatchup($matchupString, $o[17], $wchach[$o[17]]);

	return $matchupString;
}

$kraches = array(
		41 => 72.8,
34 => 6.265,
44 => 38.17,
42 => 116.6,
23 => 36.26,
39 => 19.3,
51 => 50.51,
49 => 29.9,
47 => 235.4,
46 => 159.6,
3 => 90.63,
56 => 122.8,
43 => 57.82,
15 => 61.63,
7 => 85.41,
16 => 141.4,
27 => 79.33,
54 => 122.9,
58 => 121.5,
52 => 213.1,
21 => 116.2,
59 => 66.66,
50 => 77.14,
10 => 86.13,
6 => 80.39,
14 => 250.4,
28 => 81.38,
45 => 65.65,
1 => 102.2,
37 => 243,
12 => 109.1,
35 => 77.4,
9 => 84.7,
36 => 369.8,
33 => 227.6,
26 => 86.9,
40 => 123.4,
32 => 201.3,
4 => 137.1,
53 => 214,
8 => 50.21,
20 => 103.9,
38 => 221,
25 => 124.6,
24 => 55.52,
55 => 72.29,
30 => 168.7,
5 => 362.5,
22 => 150.7,
11 => 51.57,
48 => 83.92,
29 => 6.804,
31 => 206,
17 => 111.5,
2 => 155.8,
13 => 84.32,
18 => 168.4,
19 => 189.4,
57 => 200.5);

$queries = 0;

$query = 'select * from outcomes';
$ref = mysql_query($query);
while($row = mysql_fetch_assoc($ref)) {
	if($queries%1000 == 0)
  {
    echo("$id - $queries queries<br>\n");
    flush();
  }
	$input = explode(',', $row['str']);
	$str =  createMatchups($input);

	$games = explode(";",$str);
	$count = 1;
	$oprob = 1;

	unset($games[18]);

	$count = 0;
	foreach($games as $game)
	{
		$tie = 0;
		if($count == 9)
			$tie = 1;
		$teams = explode(",",$game);
		if(count($teams)==1)
		{
			$teams = explode("t",$game);
			$tie = 2;
		}
		$krach1 = $kraches[$teams[0]];
		$krach2 = $kraches[$teams[1]];
		$prob = $krach1/($krach1+$krach2);
		if($tie == 1)
			$prob -= (($prob*(1-$prob)));
		if($tie == 2)
			$prob = ($prob*(1-$prob))*2;
		$oprob *= $prob;
		$count++;
	}
	$oprob *= 100;
	$id = $row['id'];
	$query = "update outcomes set krach='$oprob' where id='$id'";
	mysql_query($query) or die("error");
	$queries++;
}

?>
