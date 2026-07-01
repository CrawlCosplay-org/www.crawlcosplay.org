<?php $this->layout = 'dcf'; ?>
<?php 
echo '<div class="login_placeholder">' . 
        '<div id="login" align="right">' .
         '<br>';
echo '<span id="login_message">' . _("Logged in as:") . " xxxxx </span>" . 
     '<a id="chem_link" href="javascript:" style="">' . _("Change Email") . "</a> " .
     '<a id="chpw_link" href="javascript:" style="">' . _("Change Password") . "</a> " .
     '<a id="logout_link" href="javascript:" style="">' . _("Logout") . "</a></span>";
echo     '</div>';

echo "<h2>" . _("Welcome to the lobby for the Dungeon Crawl Fajita (DCF) WebTiles server") . "</b>!</h2>";

echo "<p>" . _("By playing on this DCF WebTiles server you agree to our") . ' <a href="/dcf/code_of_conduct">' . _("Code of Conduct") . "</a>.<br></p>";
echo "<p>" . _("Please join our") . ' <a href="https://discord.gg/79uNk5HJeh" target="_blank">' . _("Dungeon Crawl community discord server") . "</a>.</p>";
echo "<p>" . _("DCF Admin (RoGGa) may be contacted via email at:") . "<b>rogga @ crawlcosplay . org</b></p>";
echo "<p>" . _("For quick account or server issues, please contact RoGGa in the DC discord server linked above.") . "</p>";

echo "<h2>" . _("Hello") . ", <b>{username}</b>!</h2>";
echo "<p>"  . _("Games on this server are recorded for in-progress viewing and playback. Chat messages might be logged, depending on the player's settings.") . "</p>";

echo "<h4>" . _("Play Now") . "</h4>";
echo "<p>Dungeon Crawl Chili 1.0-a0 (" . _("Updates every 15 minutes") . ") (edit rc)</p>";
?>
