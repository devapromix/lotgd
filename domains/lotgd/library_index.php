<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_library.php';


$page_title = 'Игровой процесс / Библиотека / Край Серого Дракона';
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_library_menu('index');



?>
	<div class="block">
		<h2><span>Игровой процесс</span></h2>
		<div id="adintro" class="box">
			<legend>Приход героя</legend>
			<div class="inbox">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
			<legend>Стартовая локация</legend>
			<div class="inbox">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
			<legend>Первый бой</legend>
			<div class="inbox">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
			<legend>Отдых</legend>
			<div class="inbox">
				<p>О некоторых локациях можно отдыхать и восстанавливать здоровье, если есть запас провизии. Во время отдыха есть вероятность, что на героя нападет вор.</p>
			</div>
			<legend>Лечение</legend>
			<div class="inbox">
				<p>В любом городе или Домике Целителя Герой за определенную плату может частично или полностью восстановить здоровье.</p>
			</div>

		</div>
	</div>
</div>

<?php
require PUN_ROOT.'footer.php';
?>