<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<h1>Welcome, <?= $session->userdata('username'); ?>!</h1>
<p>Role: <?= $session->userdata('role'); ?></p>
<a href="<?= site_url('auth/logout'); ?>">Logout</a>


