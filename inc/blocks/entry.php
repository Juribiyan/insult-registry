<?php 
	require_once "inc/formatting.php";
	require_once "inc/io.php";
	require_once "inc/blocks/icon.php";
	require_once "inc/blocks/figure.php";

	$entry = sanitizeOutput($entry, ['offender', 'victim', 'link', 'site']);

	$offender = @$entry['offender']
		? '<a class="uncolored-link" href="/offender/'.$entry['offender'].'" title="Отобразить все оскорбления от '.$entry['offender'].'">'.$entry['offender'].'</a>'
		: "<i>".DEFAULT_NAME."</i>";

	$link = $entry['link'] 
		? '<a href="'.$entry['link'].'" target="_blank" title="Перейти к оскорблению">'.$entry['site']
			. make_icon('arrow', 'icon icon-16 external-link-indicator') . '</a>'
		: '';
	$pic = $entry['pic'] ? figure($entry['pic']) : '';

	$entry['date'] = formatDateShort(strtotime($entry['timestamp']));
	if (!isset($entry['id_padded']))
		$entry['id_padded'] = entryNoFull($entry['id']);

	$comment_count = @$entry['comment_count']
		? ' <span class="comment-count">' . make_icon('comment', 'icon icon-16') . "&nbsp;" . $entry['comment_count'] . "</span>"
		: '';

	$entry_id = @$entry_single
		? '<span class="entry-id post-id">№ ' . $entry['id_padded'] . '</span>'
		: '<a href="/entry/'.$entry['id'].'" class="entry-id post-id uncolored-link">'.
				'№ '.$entry['id_padded'] . $comment_count .
			'</a>';

	$site = @$entry['site']
		? '<a class="semantic-icon uncolored-link" href="/site/' . $entry['site'] 
			. '" title="Отобразить все оскорбления на этом сайте">@</a>'
		: '';
?>
<div class="entry" id="entry_<?= $entry['id'] ?>">
	<div class="entry-head post-head">
		<?= $entry_id ?>
		<date><?= $entry['date'] ?></date>
	</div>
	<h3 class="entry-title">
		<?= $offender ?>
		<a class="semantic-icon uncolored-link" href="/pair/<?= @$entry['offender'] ? $entry['offender'] : '_' ?>/<?= $entry['victim'] ?>" title="Отобразить все оскорбления для этой пары">→</a>
		<a class="uncolored-link" href="/victim/<?= $entry['victim'] ?>" title="Отобразить все оскорбления в адрес <?= $entry['victim'] ?>"><?= $entry['victim'] ?></a>
		<?= $site ?>
		<?= $link ?>
	</h3>
	<div class="entry-comment">
		<?= $pic ?>
		<?= $entry['comment'] ?>
	</div>
</div>