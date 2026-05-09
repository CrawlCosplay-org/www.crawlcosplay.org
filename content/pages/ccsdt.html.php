<?php $this->layout = 'ccsdt'; ?>
<h2 style="color:rgb(69, 136, 5);">Welcome to the <b>Crawl Cosplay Sudden Death Tournament</b> (CCSDT)</h2>

<h2>Next Tournament <b>CCSDT#3 for DCSS v0.34</b></h2>
<ul>
    <li>Starts: Friday May 15th, 2026 at 00:00 UTC. (Estimated local date & time: <a id="local-time"></a>)</li>
    <li><a href="/ccsdt/0.34/index.html">Tournament Overview</a>.</li>
    <li><a href="/ccsdt/0.34/rules.html">Tournament Details</a>.</li>
    <li>NOTE: Once you make your way to either of the pages linked above, use the nav bar at the bottom of the page to navigate to all of the available pages.</li>
</ul>

<script>
  // Your UTC time string (must end in 'Z' for UTC)
  const utcDate = '2026-05-15T00:00:00Z';
  
  // Create a Date object; it automatically converts to local time
  const localDate = new Date(utcDate);
  
  // Display it in a readable local format
  document.getElementById('local-time').innerHTML = localDate.toLocaleString();
</script>

<h2>Testing Phase of Python Scripts</h2>
<ul><li><a href="/ccsdt/0.34test/standings.html">Tryout NOW</a> the major changes to the scoring and bonuses.</li>
</ul>

<h2>Past Tournaments Results</h2>
<ul><li><a href="/ccsdt/0.32/standings.html">CCSDT#1</a> for DCSS v0.32</li>
    <li><a href="/ccsdt/0.33/standings.html">CCSDT#2</a> for DCSS v0.33</li>
</ul>

<h2>Python Volunteers Needed!</h2>
<ul><li>Are you Python savvy? Contact us in our
        <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">Crawl Cosplay 2.0 community discord server
        <img src="/img/discord_transparent_border.png" width="18" height="18" ></a>.</li>
</ul>
