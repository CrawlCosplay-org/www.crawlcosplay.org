<?php $this->layout = 'admin'; ?>

<h1>Admin Dashboard</h1>
<br>
<br>
<h2>Moderation</h2>

<ul style="line-height: 2;">
    <li>
        <a href="/admin/submissions/moderate">
            Submissions Needing Moderation
        </a>
    </li>
    <li>
        <a href="/admin/crawl_succession/list">
            Crawl Succession Games Pending Approval
        </a>
    </li>
</ul>

<br>

<h2>Content Management</h2>

<ul style="line-height: 2;">
    <li>
        <a href="/admin/challenges/list">
            Challenge List
        </a>
    </li>
    <li>
        <a href="/admin/players/list">
            Players List
        </a>
    </li>
    <li>
        <a href="/admin/ccc_seasons/list">
            CCC Seasons - Task: Make new challenge characters!
        </a>
    </li>
</ul>

<br>

<h2>Account</h2>

<ul style="line-height: 2;">
    <li>
        <a href="/admin/logout">
            Logout
        </a>
    </li>
</ul>
