<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_library.php';


$page_title = 'Игровые локации / Библиотека / Край Серого Дракона';
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_library_menu('locations');



?>
	<div class="block">
		<h2><span>Игровые локации</span></h2>
		<div id="adintro" class="box">
			<legend>Таверна</legend>
			<div class="inbox">
				<p>Здание Таверны всегда находится у городских ворот. В Таверне можно отдохнуть, восстановить силы, узнать последние новости и пополнить запасы провизии.</p>
			</div>
			<legend>Кладбище</legend>
			<div class="inbox">
				<p>Особенная локация, в которой Герой воскрешается после смерти. У воскресшего Героя не полный запас здоровья.</p>
			</div>
			<legend>Опасные локации</legend>
			<div class="inbox">
				<p>В опасных локациях Герой сражается, добывая опыт и полезные предметы экипировки. В этой же локации Герой может отдохнуть и восстановить здоровье и дух, если имеет с собой запас провизии.</p>
			</div>
			<legend>Целитель</legend>
			<div class="inbox">
				<p>В домике целителя можно полностью или частично исцелиться за золото.</p>
			</div>
			<legend>Конюшня</legend>
			<div class="inbox">
				<p>С помощью конюшни Герой может за определенную плату отправиться в любой другой регион.</p>
			</div>
			<legend>Гавань/Пристань</legend>
			<div class="inbox">
				<p>Сев на корабль и оплатив путешествие, Герой может отплыть в другой город в другом регионе. Только для прибрежных городов.</p>
			</div>

		</div>
	</div>
</div>

<?php
require PUN_ROOT.'footer.php';
?>