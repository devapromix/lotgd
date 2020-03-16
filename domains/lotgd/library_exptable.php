<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_library.php';


$page_title = 'Таблица опыта / Библиотека / Край Серого Дракона';
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_library_menu('exptable');



?>
	<div class="block">
		<h2><span>Таблица опыта</span></h2>
		<div id="adintro" class="box">
			<legend>Опыт зарабатывается в боях</legend>
			<div class="inbox">
				<p>Данная таблица показывает, сколько необходимо опыта Вашему персонажу для достижения определенного уровня.</p>
				<table border="3" width="400">
					<tr><td>Уровень</td><td>Опыт</td><td>Уровень</td><td>Опыт</td><td>Уровень</td><td>Опыт</td><td>Уровень</td><td>Опыт</td></tr>
					<?php
						for($i=1;$i<=25;$i++) {
							echo '<tr><td>'.$i.'</td><td>'.charactermaxexp($i).'</td><td>'.($i+25).'</td><td>'.charactermaxexp($i+25).'</td><td>'.($i+50).'</td><td>'.charactermaxexp($i+50).'</td><td>'.($i+75).'</td><td>'.charactermaxexp($i+75).'</td></tr>';
						}
					?>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
require PUN_ROOT.'footer.php';
?>