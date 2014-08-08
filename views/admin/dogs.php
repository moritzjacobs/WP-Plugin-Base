<?php

include_once($this->vendor_dir . "moritzjacobs/wp-easy-admin-table/easy-admin-table.class.php");

?>


<div class="wrap">
	<h2><?=$title?></h2>
	<p><?php new EasyAdminTable($dogs); ?></p>
</div>