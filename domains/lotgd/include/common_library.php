<?php

if (!defined('PUN'))
	exit;

function generate_library_menu($page = ''){
?>
<div id="adminconsole" class="block2col">
	<div id="adminmenu" class="blockmenu">
		<h2><span>Библиотека</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li<?php if ($page == 'index') echo ' class="isactive"'; ?>><a href="library_index.php">Игровой процесс</a></li>
					<li<?php if ($page == 'exptable') echo ' class="isactive"'; ?>><a href="library_exptable.php">Таблица опыта</a></li>
				</ul>
			</div>
		</div>	
	</div>	

	
<?php
}

?>