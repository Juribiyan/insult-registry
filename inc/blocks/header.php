<?php 
	
?>
<header>
	<nav>
		<a href="/" class="index-link <?= @$nav_state=='index' ? '' : 'index-link-animated' ?>">
			<img class="logo" src="/static/logo.svg">
			<?= @$nav_state=='index' ? '' : '<span class="hero-nav-item">Добавить</span>' ?>
		</a>
		<a href="/all"<?= @$nav_state=='entries' ? 'class="nav-current"' : '' ?>>Записи</a>
		<div>
			<a href="/offenders"<?= @$nav_state=='offenders' ? 'class="nav-current"' : '' ?>>Вредители</a>
			<div class="semantic-icon">→</div>
			<a href="/victims"<?= @$nav_state=='victims' ? 'class="nav-current"' : '' ?>>Жертвы</a>
		</div>
		<div>
			<div class="semantic-icon">@</div>
			<a href="/sites"<?= @$nav_state=='sites' ? 'class="nav-current"' : '' ?>>Сайты</a>
		</div>
	</nav>
</header>
<section class="content">
<?= isset($heading) ? "<h2>$heading</h2>" : '' ?>