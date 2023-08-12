<?php
require_once 'config.php';
$initial = !@$_GET['show'];
$shorten_life = number_format(CAPTCHA_LIFE - 0.5, 1, '.', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instant 0chan® NoJS Captcha™ for iframe (patent pending)</title>
  <link rel="stylesheet" href="/static/nojscaptcha.css">
  <style>
    .rotting-indicator {
      -webkit-animation-duration: <?php echo $shorten_life?>s;
           -o-animation-duration: <?php echo $shorten_life?>s;
              animation-duration: <?php echo $shorten_life?>s;
    }
    img, .rotten-msg {
      -webkit-animation-delay: <?php echo $shorten_life?>s;
           -o-animation-delay: <?php echo $shorten_life?>s;
              animation-delay: <?php echo $shorten_life?>s;
    }
  </style>
</head>
<body>
  <form action="">
    <button type="submit" name="show" value="1">    
	    <?php
	    if ($initial) 
	      echo 
	      '<div class="captcha-msg">Показать капчу</div>';
	    else 
	      echo 
	      '<img alt="Captcha image" src="/captcha.php?'.(float)rand()/(float)getrandmax().'">
	      <div class="rotting-indicator"></div>
	      <div class="rotten-msg captcha-msg">Капча-протухла</div> ';
	    ?>
    </button>
  </form>
</body>
</html>