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
				<a class="navbar-brand" href="\"><?=$title?></a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
                    <?php if (!isset($active_page)){ $active_page = ""; }?>
					<li<?php if($active_page == "today") { echo ' class="active"';}?>><a href="\">今日記錄</a></li>
                    <li<?php if($active_page == "yestoday") { echo ' class="active"';}?>><a href="yestoday.php">昨日記錄</a></li>
                    <li<?php if($active_page == "add") { echo ' class="active"';}?>><a href="record.php">新增記錄</a></li>
                    <li<?php if($active_page == "search") { echo ' class="active"';}?>><a href="search.php">搜尋記錄</a></li>
					<li><a href="/Adminer">內部資料</a></li>
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</nav>
</header>