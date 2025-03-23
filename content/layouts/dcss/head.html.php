<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?=$page_title ?? 'Dungeon Crawl:Stone Soup (DCSS)'?></title>
    <link rel="icon" href="/img/orb_of_zot.gif" type="image/png">
    <!-- <link rel="stylesheet" 	href="/css/reset.css"> -->
    <link rel="stylesheet" href="/css/dcss.css?v=<?=time()?>">
    <!-- <link rel="stylesheet" href="https://crawl.develz.org/tournament/0.23/tourney-score.css"> -->

<style>

div.score-sidebar-cca {
    float: right;
    clear: both;
    text-align: right;
    width: 575px;
    padding: 0 2em 5em;
}
	
.centerTable { 
    margin: 0px auto; 
    border: none;
}
	
h1	{
	font-size: 48px;
	text-align: center;
}

.dropbtn {
  background-color: #7D623C;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background-color: #7D623C;
  min-width: 400px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {background-color: #000000;}
.dropdown:hover .dropdown-content {display: block;}
.dropdown:hover .dropbtn {background-color: #7D623C;}

</style>

<?php

    if (isset($meta) && is_array($meta) && isset($meta['filename'])) {
        $meta += [
            'width' => 256,
            'height' => 256,
            'alt' => "Dungeon Crawl Stone Soup",
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
