<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?=$page_title ?? 'Crawl Cosplay Sudden Death Tournament (CCSDT)'?></title>
    <link rel="icon" href="/img/RIP-YASD.png" type="image/png">
    <link rel="stylesheet" href="/css/ccsdt.css?v=<?=time()?>">
    <!-- <link rel="stylesheet" href="https://crawl.develz.org/tournament/0.23/tourney-score.css"> -->

<style>

html, body {
    margin: 0;
    background-color: #202;
    color: #ccc;
    font-family: sans-serif;
}

#content {
    margin-bottom: 40px;
    margin-left: auto;
    margin-right: auto;
    width: calc(100% - 20px);
}

a {
    color: #ccc;
}

pre {
	background: #606;
	border: 3px dashed #669;
	margin-left: auto;
	margin-right: auto;
	padding: 10px;
}

#rc {
	width: 6em;
	font-size: 12pt;
	text-align: center;
}

#cover {
	width: 45em;
	overflow-x: hidden;
	overflow-y: hidden;
}

#combo, #bonus, #gods, #times, #schedule  {
    font-size: 11pt;
}

.label {
	font-weight: bold;
}

.card {
    margin-left: 0px;
    margin-right: 0px;
}

table {
    margin-top: 30px;
    width: auto;
    margin-left: auto;
    margin-right: auto;
    border-collapse: collapse;
}
	
td.pt, td.total {
	text-align: center;
}

td.hs {
	text-align: right;
}

td, th {
	padding-left: 10px;
	padding-right: 10px;
	padding-bottom: 5px;
	padding-top: 5px;
}

th {
	vertical-align: top;
}

tr.head {
	font-size: 9pt;
	border-bottom: 3px solid #669;
	border-top: 3px solid #669;
}

tr {
	background-color: #606;
}
tr:nth-child(even) {
	background-color: #303;
}

tr.won {
  background-color: #363;
  font-weight: bold;
}
tr.won:nth-child(even) {
  background-color: #002A00;
}
tr.dead {
  background-color: #711;
}
tr.dead:nth-child(even) {
  background-color: #450000;
}
tr.alive {background-color: #383338}
tr.alive:nth-child(even) {background-color: #1F1A1F;}

tr.none {
	color: #477;
	background-color: #111;
}

tr.none:nth-child(even) {background-color: #000;}

.menu {
	padding-left: 10px;
}

img.flag {
    vertical-align: middle;
    padding-right: 8px;
}

.menuspacer {
	margin-left: 10px;
	border-left: 3px solid #ccc;
	height: calc(14pt);
}

#bottomtext {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    background-color: #606;
    padding: 5px;
}

#updated {
	float: right;
	margin-right: 15px;
}

</style>
    
<?php

    if (isset($meta) && is_array($meta) && isset($meta['filename'])) {
        $meta += [
            'width' => 256,
            'height' => 256,
            'alt' => "Crawl Cosplay Academy",
            'type' => "image/png",
        ];
        if (strpos($meta['filename'], 'https') === 0) {
            $meta['secure_filename'] = $meta['filename'];
            $meta['filename'] = str_replace("https", "http", $meta['filename']);
        } else {
            $meta['secure_filename'] = str_replace("http", "https", $meta['filename']);
        }
        echo <<<META
            <meta property="og:image" content="{$meta['filename']}" />
            <meta property="og:image:secure_url" content="{$meta['secure_filename']}" />
            <meta property="og:image:type" content="{$meta['type']}" />
            <meta property="og:image:width" content="{$meta['width']}" />
            <meta property="og:image:height" content="{$meta['height']}" />
            <meta property="og:image:alt" content="{$meta['alt']}" />
        META;
    }

?>
</head>
<body class="page_back">
<div class="page_floor">
