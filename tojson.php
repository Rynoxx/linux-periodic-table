<?php

/*
Copyright © Lukas 'Rynoxx' Söder 2017

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(isset($_GET["get"])){
	$cacheFileName = dirname(__FILE__) . "/cache.json";
	$cacheAge = 60 * 60 * 24;

	if (time() - (@filemtime($cacheFileName)) < $cacheAge) {
		header("Content-Type: application/json;charset=utf-8");
		exit(file_get_contents($cacheFileName));
	}

	$content = @file_get_contents("http://distrowatch.com/dwres.php?resource=popularity");

	$doc = new \DOMDocument();
	$doc->preserveWhiteSpace = false;
	@$doc->loadHTML($content);

	$xpath = new \DOMXPath($doc);

	$class = "NewsText";

	$table = $xpath->query('//td[@class="' . $class . '"]/table');

	// The selectors got quite long winded...

	// 12 Months popularity
	$list12 = $table->item(0)->firstChild->firstChild->childNodes->item(1)->childNodes;

	// 6 Months
	$list6 = $table->item(0)->firstChild->childNodes->item(2)->childNodes->item(1)->childNodes;

	// 3 Months
	$list3 = $table->item(0)->firstChild->childNodes->item(4)->childNodes->item(1)->childNodes;

	// Last month
	$list1 = $table->item(0)->firstChild->childNodes->item(6)->childNodes->item(1)->childNodes;


	$distros = [
		"months12" => [],
		"months6" => [],
		"months3" => [],
		"months1" => [],
	];

	foreach($list1 as $i => $elm){
		if($i === 0){
			continue;
		}

		$distros["months1"][] = [
			"name" => $elm->childNodes->item(2)->textContent,
			"popularity" => $elm->childNodes->item(0)->textContent,
			"hpd" => $elm->childNodes->item(4)->textContent
		];
	}

	foreach($list3 as $i => $elm){
		if($i === 0){
			continue;
		}

		$distros["months3"][] = [
			"name" => $elm->childNodes->item(2)->textContent,
			"popularity" => $elm->childNodes->item(0)->textContent,
			"hpd" => $elm->childNodes->item(4)->textContent
		];
	}

	foreach($list6 as $i => $elm){
		if($i === 0){
			continue;
		}

		$distros["months6"][] = [
			"name" => $elm->childNodes->item(2)->textContent,
			"popularity" => $elm->childNodes->item(0)->textContent,
			"hpd" => $elm->childNodes->item(4)->textContent
		];
	}

	foreach($list12 as $i => $elm){
		if($i === 0){
			continue;
		}

		$distros["months12"][] = [
			"name" => $elm->childNodes->item(2)->textContent,
			"popularity" => $elm->childNodes->item(0)->textContent,
			"hpd" => $elm->childNodes->item(4)->textContent
		];
	}

	$encoded = json_encode($distros);

	file_put_contents($cacheFileName, $encoded);

	header("Content-Type: application/json;charset=utf-8");

	exit($encoded);
}
else{
	?>
	<html>
		<head>
			<title>DistroWatch Popularity</title>
		</head>
		<body>
			Please be gentle...
		</body>
	</html>
	<?php
}
