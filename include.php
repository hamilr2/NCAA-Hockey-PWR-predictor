<?

function getTeamName($id) {
	$teamsMap = array(
		1=> "Merrimack",
    2=> "Union",
    3=> "Bowling Green",
    4=> "Niagara",
    5=> "Quinnipiac",
    6=> "Maine",
    7=> "Colgate",
    8=> "Northeastern",
    9=> "Michigan Tech",
    10=> "Lake Superior",
    11=> "RIT",
    12=> "Michigan",
    13=> "Vermont",
    14=> "Mass.-Lowell",
    15=> "Clarkson",
    16=> "Colorado College",
    17=> "St. Lawrence",
    18=> "Western Michigan",
    19=> "Wisconsin",
    20=> "Northern Michigan",
    21=> "Ferris State",
    22=> "Rensselaer",
    23=> "American Int'l",
    24=> "Penn State",
    25=> "Ohio State",
    26=> "Minnesota-Duluth",
    27=> "Connecticut",
    28=> "Massachusetts",
    29=> "Sacred Heart",
    30=> "Providence",
    31=> "St. Cloud State",
    32=> "New Hampshire",
    33=> "Minnesota State",
    34=> "Alabama-Huntsville",
    35=> "Michigan State",
    36=> "Minnesota",
    37=> "Miami",
    38=> "Notre Dame",
    39=> "Army",
    40=> "Nebraska-Omaha",
    41=> "Air Force",
    42=> "Alaska-Fairbanks",
    43=> "Canisius",
    44=> "Alaska-Anchorage",
    45=> "Mercyhurst",
    46=> "Boston University",
    47=> "Boston College",
    48=> "Robert Morris",
    49=> "Bentley",
    50=> "Holy Cross",
    51=> "Bemidji State",
    52=> "Denver",
    53=> "North Dakota",
    54=> "Cornell",
    55=> "Princeton",
    56=> "Brown",
    57=> "Yale",
    58=> "Dartmouth",
    59=> "Harvard");
	return $teamsMap[$id];
}

function addGame(&$teams, $winner, $loser, $tie = null) {
	if ($tie == -1) {
		$teams[$winner]['t'][] = $loser;
		$teams[$winner]['o'][$loser][] = 't';
		$teams[$loser]['t'][] = $winner;
		$teams[$loser]['o'][$winner][] = 't';
		//echo "($winner) " . $teams[$winner]['name'] . " ties ($loser) " . $teams[$loser]['name'] . "\n";
	} else {
		$teams[$winner]['w'][] = $loser;
		$teams[$winner]['o'][$loser][] = 'w';
		$teams[$loser]['l'][] = $winner;
		$teams[$loser]['o'][$winner][] = 'l';
		//echo "($winner) " . $teams[$winner]['name'] . " defeats ($loser) " . $teams[$loser]['name'] . "\n";
	}
}

function augmentData(&$teams,$o) {

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

	addGame($teams, $o[0], $ahas1[$o[0]]);
	addGame($teams, $o[1], $ahas2[$o[1]]);
	$ahaf = array($o[0] => $o[1], $o[1] => $o[0]);
	addGame($teams, $o[2], $ahaf[$o[2]]);

	addGame($teams, $o[3], $cchas1[$o[3]]);
	addGame($teams, $o[4], $cchas2[$o[4]]);
	$cchaf = array($o[3] => $o[4], $o[4] => $o[3]);
	addGame($teams, $o[5], $cchaf[$o[5]]);

	addGame($teams, $o[6], $ecacs1[$o[6]]);
	addGame($teams, $o[7], $ecacs2[$o[7]]);
	$ecacf = array($o[6] => $o[7], $o[7] => $o[6]);
	addGame($teams, $o[8], $ecacf[$o[8]]);

	if ($o[9] == -1) {
		addGame($teams, $ecacs1[$o[6]], $ecacs2[$o[7]], -1);
	} else {
		$ecacc = array($ecacs1[$o[6]] => $ecacs2[$o[7]], $ecacs2[$o[7]] => $ecacs1[$o[6]]);
		addGame($teams, $o[9], $ecacc[$o[9]]);
	}

	addGame($teams, $o[10], $heas1[$o[10]]);
	addGame($teams, $o[11], $heas2[$o[11]]);
	$heaf = array($o[10] => $o[11], $o[11] => $o[10]);
	addGame($teams, $o[12], $heaf[$o[12]]);

	addGame($teams, $o[13], $wchas1[$o[13]]);
	$wchaf1 = array($o[13] => $wchaf1t, $wchaf1t => $o[13]);
	addGame($teams, $o[14], $wchaf1[$o[14]]);
	addGame($teams, $o[15], $wchas2[$o[15]]);
	$wchaf2 = array($o[15] => $wchaf2t, $wchaf2t => $o[15]);
	addGame($teams, $o[16], $wchaf2[$o[16]]);
	$wchach = array($o[14] => $o[16], $o[16] => $o[14]);
	addGame($teams, $o[17], $wchach[$o[17]]);

	$teams[$o[2]]['autobid'] = 1;
	$teams[$o[5]]['autobid'] = 1;
	$teams[$o[8]]['autobid'] = 1;
	$teams[$o[12]]['autobid'] = 1;
	$teams[$o[17]]['autobid'] = 1;
}

function winPct(&$team, $mod = null, $mult = 1) {
	$w = count($team['w']) - $mod['l'] * $mult;
	$l = count($team['l']) - $mod['w'] * $mult;
	$t = count($team['t']) - $mod['t'] * $mult;
	return ($w * 2 + $t) / (($w + $l + $t) * 2);
}

function altWinPct(&$team, $mod = null, $mult = 1) {
	$w = $team['w'] - $mod['l'];
	$l = $team['l'] - $mod['w'];
	if ($mult == -1) {
		$w = $team['w'] - $mod['w'];
		$l = $team['l'] - $mod['l'];
	}
	$t = $team['t'] - $mod['t'];
	return ($w * 2 + $t) / (($w + $l + $t) * 2);
}

function owp(&$team, &$teams) {
	$owp = 0;
	$count = 0;
	foreach ($team['o'] as $opp => $record) {
		$mod = array('w' => 0, 'l' => 0, 't' => 0);
		foreach ($record as $rec) {
			$mod[$rec]++;
		}
		$owp += winPct($teams[$opp], $mod) * array_sum($mod);
		$count += array_sum($mod);
	}
	return $owp / $count;
}

function oowp(&$team, &$teams) {
	$oowp = 0;
	$count = 0;
	foreach ($team['o'] as $opp => $record) {
		$oowp += $teams[$opp]['owp'] * count($record);
		$count += count($record);
	}
	return $oowp / $count;
}

function rpi(&$team) {
	return $team['winpct'] * .25 + $team['owp'] * .21 + $team['oowp'] * .54;
}

function removeBadWins(&$team, &$teams) {
	$tempTeam = $team;
	$gamesToRemove = array();
	foreach ($team['w'] as $win) {
		//echo (.25 + $teams[$win]['winpct'] * .21 + $teams[$win]['owp'] * .54) . ' ' . $team['rpi'] . "\n";
		if ((.25 + $teams[$win]['winpct'] * .21 + $teams[$win]['owp'] * .54) < $team['rpi']) {
			$gamesToRemove[] = $win;
		}
	}
	if (count($gamesToRemove) == 0) {
		return;
	}
	$uniqueGames = array_unique($gamesToRemove);
	foreach ($uniqueGames as $game) {
		foreach ($tempTeam['o'][$game] as $okey => $res) {
			if ($res == 'w') {
				unset($tempTeam['o'][$game][$okey]);
			}
		}
		foreach ($tempTeam['w'] as $wkey => $opp) {
			if ($opp == $game) {
				unset($tempTeam['w'][$wkey]);
			}
		}
	}
	$tempTeam['winpct'] = winPct($tempTeam);
	$tempTeam['owp'] = owp($tempTeam, $teams);
	$tempTeam['oowp'] = oowp($tempTeam, $teams);
	//$oldRPI = $teams[$id]['rpi'];
	$team['rpi'] = rpi($tempTeam);

	//print_r($team);
	//echo $tempTeam['name'] . ' - Old: ' . $oldRPI . ' - New: ' . $teams[$id]['rpi'] . "\n";
}

function tuc(&$team, &$teams, $mod, $mult) {
	$tuc = array('w' => 0, 'l' => 0, 't' => 0);
	foreach ($team['o'] as $id => $records) {
		if ($teams[$id]['rpi'] < .5/* && $teams[$id]['autobid'] != 1 */)
			continue;
		foreach ($records as $rec) {
			$tuc[$rec]++;
		}
	}
	if ((array_sum($tuc) - array_sum($mod)) < 10) {
		return -1;
	}
	return altWinPct($tuc, $mod, $mult);
}

function winPctOpp(&$team, $opp) {
	$record = array('w' => 0, 'l' => 0, 't' => 0);
	foreach ($team['o'][$opp] as $rec) {
		$record[$rec]++;
	}
	return altWinPct($record);
}

function compareTeams(&$a, &$b) {
	if ($a['ctot'] < $b['ctot'])
		return 1;
	if ($a['ctot'] == $b['ctot'] && $a['rpi'] < $b['rpi'])
		return 1;
	return -1;
}

function determineNumberOfReseeds($reseeds, &$teams) {
	$newReseeds = 0;
	$lastPos = 16 - $reseeds;
	for ($i = 1; $i < 60; $i++) {
		if ($teams[$i]['autobid'] == 1 && $teams[$i]['seed'] > $lastPos) {
			$newReseeds++;
		}
	}
	if ($newReseeds == $reseeds) {
		return $newReseeds;
	} else {
		return determineNumberOfReseeds($newReseeds, $teams);
	}
}

function seedTeams(&$teams) {
	$i = 1;
	foreach ($teams as &$team) {
		$team['seed'] = $i;
		$i++;
	}
}

function outputTable(&$teams) {
	echo '<table><tr><th>Seed</th><th>Name</th><th>Record</th><th>Win%</th><th>OWP</th><th>OOWP</th><th>RPI</th><th>Auto</th><th>Comp</th></tr>';
		foreach ($teams as $id => $team) {
			echo '<tr>';

			echo '<td>' . $teams[$id]['seed']. '</td>';
			echo '<td>' . $teams[$id]['name'] . ' (' . $id . ')</td>';
			echo '<td>' . $teams[$id]['record'] . '</td>';
			echo '<td>' . sprintf("%.4f", $teams[$id]['winpct']) . "</td>";
			echo '<td>' . sprintf("%.4f", $teams[$id]['owp']) . "</td>";
			echo '<td>' . sprintf("%.4f", $teams[$id]['oowp']) . "</td>";
			echo '<td>' . sprintf("%.4f", $teams[$id]['rpi']) . "</td>";
			echo '<td>' . $teams[$id]['autobid'] . "</td>";
			echo '<td>' . $teams[$id]['ctot'] . "</td>";
			echo '</tr>';
		}
		echo '</table>';
}

function doPWR($teams,$outcome,$verbose) {
	// augment teams object with upcoming games
	if(count($outcome) == 18) {
		augmentData($teams,$outcome);
	}

	// calc record, win%, and opponent's win%
	foreach ($teams as &$team) {
		$team['record'] = count($team['w']) . '-' . count($team['l']) . '-' . count($team['t']);
		$team['winpct'] = winPct($team);
		$team['owp'] = owp($team,$teams);
	}

	// calc opp's opp's win%
	foreach ($teams as &$team) {
		$team['oowp'] = oowp($team,$teams);
	}

	// calculate RPI
	foreach ($teams as &$team) {
		$team['rpi'] = rpi($team);
	}

	// Remove wins that negtively impacted RPI
	foreach ($teams as &$team) {
		removeBadWins($team,$teams);
	}

	// PERFORM COMPARISONS

	foreach ($teams as $t1 => $team1) {
		// weed out non-TUCs
		if ($team1['rpi'] < .5 || $t1 == 0)
			continue;
		foreach ($teams as $t2 => $team2) {
			// weed out non-TUCs, teams that have already been compared, or if it's the same team
			if ($t2 == 0 || $team2['rpi'] < .5 || $team2['c'][$t1] != null || $t1 == $t2)
				continue;

			// calculate h2h record
			$h2h = array('w' => 0, 'l' => 0, 't' => 0);
			if ($team1['o'][$t2]) {
				foreach ($team1['o'][$t2] as $rec) {
					$h2h[$rec]++;
				}
			}

			// calculate TUC records
			$tuc1 = tuc($team1, $teams, $h2h, -1);
			$tuc2 = tuc($team2, $teams, $h2h, 1);

			// calculate COOP records
			$coopp1 = 0;
			$coopp2 = 0;
			foreach ($team1['o'] as $opp => $records) {
				if ($team2['o'][$opp] != null) {
					$coopp1 += winPctOpp($team1, $opp);
					$coopp2 += winPctOpp($team2, $opp);
				}
			}

			// Assign points starting with H2H
			$tot1 = $h2h['w'];
			$tot2 = $h2h['l'];

			// RPI is worth a point
			if ($team1['rpi'] > $team2['rpi']) {
				$tot1++;
			} else {
				$tot2++;
			}

			// TUC is worth a point
			if ($tuc1 == -1 || $tuc2 == -1) {
				// do nothing if either team is below 10 TUCs
			} else if ($tuc1 > $tuc2) {
				$tot1++;
			} else if ($tuc1 < $tuc2) {
				$tot2++;
			}

			// COOP is worth a point
			if ($coopp1 > $coopp2) {
				$tot1++;
			} else if ($coopp1 < $coopp2) {
				$tot2++;
			}
			// individual comparison viewer
			/*if ($t1 == 48 && $t2 == 57) {
				echo $team1['rpi'] . ' ' . $team2['rpi'] . "\n";
				echo $tuc1 . ' ' . $tuc2 . "\n";
				echo $coopp1 . ' ' . $coopp2 . "\n";
				echo $h2h['w'] . ' ' . $h2h['l'] . "\n";
				echo $tot1 . ' ' . $tot2 . "\n";
			}*/

			// Award comparison victories
			if ($tot1 > $tot2 || ($tot1 == $tot2 && $team1['rpi'] > $team2['rpi'])) {
				$teams[$t1]['c'][$t2] = 'w';
				$teams[$t2]['c'][$t1] = 'l';
				$teams[$t1]['ctot']++;
			} else {
				$teams[$t1]['c'][$t2] = 'l';
				$teams[$t2]['c'][$t1] = 'w';
				$teams[$t2]['ctot']++;
			}
		}
	}


	// sort teams by comparisons, then RPI
	uasort($teams, compareTeams);

	// Seed teams
	seedTeams($teams);

	// Calc number of teams that must be reseeded
	$numReseeds = determineNumberOfReseeds(0,$teams);

	// Find teams that must be reseeded
	$reseeds = array();
	$keys = array_keys($teams);
	$revkeys = array_reverse($keys, true);

	foreach($revkeys as $key) {
		if($teams[$key]['autobid'] == 1)	{
			$teams[$key]['autobid'] = 2;
			$reseeds[] = $teams[$key];
			unset($teams[$key]);
			$numReseeds--;
		}
		if($numReseeds == 0){
			break;
		}
	}

	// Move teams in to correct locations
	array_splice($teams,16-count($reseeds),0,array_reverse($reseeds));

	// reseed the teams
	seedTeams($teams);

	$results = array();

	//print_r($teams);
	foreach($teams as &$team) {
		if($results['t'.$team['id']] != null) {
			print_r($team);
		}
		$results['t'.$team['id']] = $team['seed'];
	}

	//outputTable($teams);

	return $results;
}