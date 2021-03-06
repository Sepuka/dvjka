<?php require 'server.php'; ?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Главная</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="stylesheet" type="text/css" href="css/style.css">
    <script type="text/javascript" charset="utf-8" src="http://yandex.st/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="js.js"></script>
	<!--[if lte IE 8]>
		<link rel="stylesheet" type="text/css" href="css/ie7-8.css">
	<![endif]-->
    <style>
        div.zoomimg { width:50px; height:50px; position:relative; float:left; }
        div.zoomimg img { position:relative; cursor:pointer; left:0; top:0; width:50px; height:50px; }
        div.zoomimg:hover { overflow:visible; visibility:hidden; }
        div.zoomimg:hover img { visibility:visible; position:absolute; z-index:150; }
    </style>
</head>
<body>
<div id="page">
	<div id="wrapper">
		<div id="header">
			<a href="/" id="logo" class="blocks"><img src="img/logo.png" width="158" height="184" alt=""></a><!--
		/--><div class="contacts blocks">
				<div class="phone">+7 926 687 40 51</div>
				<img src="img/site.png" width="246" height="30" alt="">
			</div>
		</div>
		<ul class="nav">
			<li><a href="service.html">шиномонтаж</a></li>
			<li><a href="used.html">б/у шины</a></li>
			<li><a href="howtobuy.html">как купить</a></li>
			<li><a href="contacts.html">контакты</a></li>
			<li class="end"><a href="about.html">о нас</a></li>
		</ul>
		<div id="top">
			<div class="container blocks">
				<div class="content">
					<div class="patch"></div>
					<div class="title2">Поиск по шинам</div>
					<form class="form">
						<div class="box-l">
							<label for="season" class="blocks">Сезон</label><br>
							<select name="season"><?php echo getSeason();?></select><br>
							<label for="firm" class="blocks">Фирма</label><br>
                            <select name="firm"><?php echo getBrand();?></select><br>
							<label for="width" class="blocks">Ширина</label><br>
                            <select name="width"><?php echo getWidth();?></select><br>
							<label for="profile" class="blocks">Профиль</label><br>
                            <select name="profile"><?php echo getProfile();?></select>
						</div>
						<div class="box-r">
							<label for="stiffness" class="blocks">Жесткость</label><br>
                            <select name="stiffness"><?php echo getStiffness();?></select><br>
							<label for="dia" class="blocks">Диаметр</label><br>
                            <select name="dia"><?php echo getDia();?></select><br>
							<div class="blocks">
								<label for="max1" class="blocks">Макс.цена</label><br>
								<input type="text" name="max" id="max1" class="int">
							</div><!--
						/--><div class=" box2 blocks">
								<label for="min1" class="blocks">Мин.цена</label><br>
								<input type="text" name="min" id="min1" class="int">
							</div><br>
							<label for="presence1">Наличие</label><br>
							<input type="checkbox" name="presence" class="blocks">
							<label for="presence1" class="show blocks">Показывать только полные<br>комплекты</label><br>
							<button type="submit" name="tbl" value="tire" class="btn">Искать</button>
						</div>
						<div class="clearfix"></div>
					</form>
				</div>
			</div><!--
		/--><div class="container margin blocks">
				<div class="content">
					<div class="patch"></div>
					<div class="title2">Подбор шин по авто</div>
                    <form class="form" action="index.php?tbl=auto">
						<div class="box-l">
							<label for="firm2" class="blocks">Марка</label><br>
							<select name="firm" id="firm2">
								<option value="0">--------------</option>
								<option value="1">Фирма 1</option>
								<option value="2">Фирма 2</option>
							</select><br>
							<label for="model" class="blocks">Модель</label><br>
							<select name="model" id="model">
							</select><br>
							<label for="modification" class="blocks">Модификация</label><br>
							<select name="modification" id="modification">
							</select>
                            <label for="season2" class="blocks">Сезон</label><br>
							<select name="season"><?php echo getSeason();?></select><br>
						</div>
						<div class="box-r">
							<label for="stiffness2" class="blocks">Жесткость</label><br>
							<select name="stiffness"><?php echo getStiffness();?></select><br></select><br>
							<label for="dia2" class="blocks">Диаметр</label><br>
							<select name="dia"><?php echo getDia();?></select><br>
							<div class="blocks">
								<label for="max2" class="blocks">Макс.цена</label><br>
								<input type="text" name="max" id="max2" class="int">
							</div><!--
						/--><div class=" box2 blocks">
								<label for="min2" class="blocks">Мин.цена</label><br>
								<input type="text" name="min" id="min2" class="int">
							</div><br>
							<label for="presence2">Наличие</label><br>
							<input type="checkbox" name="presence" id="presence2" class="blocks">
							<label for="presence2" class="show blocks">Показывать только полные<br>комплекты</label><br>
                            <button type="submit" name="tbl" value="auto" class="btn">Искать</button>
						</div>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
		</div>
		<div class="container">
            <div id="searchResult"><?php echo (array_key_exists('tbl', $_GET) && $_GET['tbl'] == 'auto') ? searchAuto() : searchTire();?></div>
			<div class="content">
				<div class="banner">
					<img src="img/banner1.jpg" width="274" height="190" alt=""><!--
				/--><img src="img/banner2.jpg" width="274" height="190" alt="" class="center"><!--
				/--><img src="img/banner3.jpg" width="274" height="190" alt="">
				</div>
				<div class="block">
					<div class="title">Как купить</div>
					<div class="line"></div>
					<p>Многолетний опыт специалистов "КАРЕН" позволяет нам выполнять работы высокой сложности на профессиональном уровне с гарантией качества. На сегодняшний день мы готовы предоставить полный спектр услуг: обслуживание, ремонт автомобилей и обеспечение запасными частями. Залогом высокого качества работ, выполняемых у нас, являются четыре основных фактора. Мы специализируемся на ремонте и техническом обслуживании преимущественно японских автомобилей: Toyota, Nissan, Honda, Mitsubishi, Mazda, Subaru, их люксовых американских брендов.</p>
					<div class="contact">
						<div class="title contact-l">Как проехать</div>
						<div class="title contact-r">Наши адреса</div>
						<div class="clearfix"></div>
						<div class="line"></div>
						<div class="contact-l">
							<script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=GEaFeOgXc4QOn-3sAXlpBhs39tYpr8xg&width=447&height=356"></script>
						</div>
						<div class="contact-r">
							<div class="address">Адрес // <span>г.Москва, ул.Преобреженская 23</span></div>
							<div class="phone">Телефон. // <span>(650) 695-143236</span></div>
							<div class="email">E-Mail // <a href="mailto:karensuport@gmail.com">karensuport@gmail.com</a></div>
							<div class="metro"><span>ст. Московская</span></div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="title">О нас</div>
					<div class="line"></div>
					<p>Многолетний опыт специалистов "КАРЕН" позволяет нам выполнять работы высокой сложности на профессиональном уровне с гарантией качества. На сегодняшний день мы готовы предоставить полный спектр услуг: обслуживание, ремонт автомобилей и обеспечение запасными частями. Залогом высокого качества работ, выполняемых у нас, являются четыре основных фактора. Мы специализируемся на ремонте и техническом обслуживании преимущественно японских автомобилей: Toyota, Nissan, Honda, Mitsubishi, Mazda, Subaru, их люксовых американских брендов.</p>
				</div>
			</div>
		</div>
		<div id="footer">
			<ul class="nav">
				<li><a href="service.html">шиномонтаж</a></li>
                <li><a href="used.html">б/у шины</a></li>
                <li><a href="howtobuy.html">как купить</a></li>
                <li><a href="contacts.html">контакты</a></li>
                <li class="end"><a href="about.html">о нас</a></li>
			</ul>
			<div class="bottom">
				<div class="left">
					<div class="title">Сейчас в наличии</div>
					<ul class="box1 blocks">
						<li><a href="index.php?brandtire=NOKIAN">Nokian</a></li>
						<li><a href="index.php?brandtire=BFGOODRICH">BFGoodrich</a></li>
						<li><a href="index.php?brandtire=TIGARCONTINENTAL">TigarContinental</a></li>
					</ul><!--
				/--><ul class="box2 blocks">
						<li><a href="index.php?brandtire=CORDIANT">Cordiant</a></li>
						<li><a href="index.php?brandtire=SAVABARUMGT">SavaBarumGT</a></li>
						<li><a href="index.php?brandtire=RADIAL">Radial</a></li>
					</ul><!--
				/--><ul class="blocks">
						<li><a href="index.php?brandtire=CONTINENTAL">Continental</a></li>
						<li><a href="index.php?brandtire=GISLAVED">Gislaved</a></li>
						<li><a href="index.php?brandtire=HANKOOK">Hankook</a></li>
					</ul>
				</div>
				<div class="right">тел.8-916-945-12-30<br>ул. Ивана Франко, 2а</div>
				<div class="clearfix"></div>
			</div>
			<div class="copyright">© 2013, <a href="#">«Карен»</a> Все права защищены.</div>
		</div>
	</div>
	<div id="footer-bg"></div>
</div>
<!--[if lte IE 8]>
	<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="js/PIE.js"></script>
	<script type="text/javascript" src="js/settingIE.js"></script>
<![endif]-->
</body>
</html>