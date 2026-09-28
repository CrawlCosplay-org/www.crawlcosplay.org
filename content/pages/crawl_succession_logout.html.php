<?php

session_start();

unset($_SESSION['user_id']);

session_destroy();

return $this->request->redirect('/crawl_succession_login');
