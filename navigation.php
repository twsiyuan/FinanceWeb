<?php require_once("config.php");?>
<header>
    <nav class="navbar navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?=$rootUrl?>"><?=$title?></a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
                    <?php if (!isset($active_page)){ $active_page = ""; }?>
					<li<?php if($active_page == "today") { echo ' class="active"';}?>><a href="<?=$rootUrl?>">今日記錄</a></li>
                    <li<?php if($active_page == "yestoday") { echo ' class="active"';}?>><a href="<?=$rootUrl?>yestoday.php">昨日記錄</a></li>
					<li<?php if($active_page == "7-days") { echo ' class="active"';}?>><a href="<?=$rootUrl?>7days.php">七日記錄</a></li>
                    <li<?php if($active_page == "add") { echo ' class="active"';}?>><a href="<?=$rootUrl?>record.php"><i class="fa fa-plus"></i> 新增記錄</a></li>
                    <li<?php if($active_page == "search") { echo ' class="active"';}?>><a href="<?=$rootUrl?>search.php"><i class="fa fa-search"></i> 搜尋記錄</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="\Adminer"><i class="fa fa-database"></i> 資料庫</a></li>
					<li><a href="\"><i class="fa fa-user"></i> 個人服務</a></li>     
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</nav>
</header>