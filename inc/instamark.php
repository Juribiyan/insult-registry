<?php
/*
* This file is part of INSTANT fucking 0CHAN
*
*/

class Parse {

	function urlcallback($matches) {
		return '<a target="_blank" rel="nofollow noopener noreferrer" href="'.$matches[1].$matches[2].'">'.$matches[1].urldecode($matches[2]).'</a>';
	}

	function exturlcallback($matches) {
		$text = strtr(urldecode($matches[1]), array('/' => '&#47;'));
		return '<a target="_blank" rel="nofollow noopener noreferrer" href="'.$matches[2].$matches[3].'">'.$text.'</a>';
	}

	function MakeClickable($txt) {
		$txt = preg_replace_callback('#«([^«»]*)»:(http:\/\/|https:\/\/|ftp:\/\/|gopher:\/\/)([^\s<]+(([^\s\pP<]|\/|\]|\pP(?=(\R|$))))+)#u',array(&$this, 'exturlcallback'),$txt);
		$txt = preg_replace_callback('#(?<!(?:(?:ref|src)=(?:[\'"])))((?:http:|https:|ftp:|gopher:)\/\/)([^\s<]+(([^\s\pP<]|\/|\]|\pP(?=(\R|$))))+)#u',array(&$this, 'urlcallback'),$txt);
		return $txt;
	}

	function BBCode($string){
		$string = preg_replace_callback('#`(.+?)`#i', array(&$this, 'inline_code_callback'), $string);
		$string = preg_replace_callback('`((?:(?:(?:^[•\*] )(?:[^\r\n]+))[\r\n]*)+)`m', array(&$this, 'bullet_list'), $string);
		$string = preg_replace_callback('`((?:(?:(?:[+\#] )(?:[^\r\n]+))[\r\n]*)?(?:(?:(?:^[+\#] )(?:[^\r\n]+))[\r\n]*)+)`m', array(&$this, 'number_list'), $string);

		$patterns = array(
      '`\*\*(.+?)\*\*`is',
      '`\*(.+?)\*`is',
      '`%%(.+?)%%`is',
      '`\[b\](.+?)\[/b\]`is',
      '`\[i\](.+?)\[/i\]`is',
      '`\[u\](.+?)\[/u\]`is',
      '`\[s\](.+?)\[/s\]`is',
      '`~~(.+?)~~`is',
      '`\[aa\](.+?)\[/aa\]`is',
      '`\[spoiler\](.+?)\[/spoiler\]`is',
      '`\[caps\](.+?)\[/caps\]`is',
      '`&quot;(.+?)&quot;`is',
      '`\[q\][ \t]{0,}(.*)\[/q\]`',
      '`\[rq\][ \t]{0,}(.*)\[/rq\]`'
    );
    $replaces =  array(
      '<b>\\1</b>',
      '<i>\\1</i>',
      '<span class="spoiler">\\1</span>',
      '<b>\\1</b>',
      '<i>\\1</i>',
      '<span style="border-bottom: 1px solid">\\1</span>',
      '<strike>\\1</strike>',
      '<strike>\\1</strike>',
      '<span style="font-family: Mona,\'MS PGothic\' !important;">\\1</span>',
      '<span class="spoiler">\\1</span>',
      '<span style="text-transform: uppercase;">\\1</span>',
      '«\\1»',
      '<span class="unkfunc">&gt;\\1</span>',
      '<span class="rquote">&lt;\\1</span>'
    );
		$string = preg_replace($patterns, $replaces, $string);
		return $string;
	}

	function bullet_list($matches) {
		$output = '<ul>';
		$lines = explode(PHP_EOL,$matches[1]);
		foreach($lines as $line) {
			if(strlen($line))
			$output .= '<li>'.substr($line, 2).'</li>';
		}
		$output .= '</ul>';
		return $output;
	}

	function number_list($matches) {
		$output = '<ol>';
		$lines = explode(PHP_EOL,$matches[1]);
		foreach($lines as $line) {
			if(strlen($line))
			$output .= '<li>'.substr($line, 2).'</li>';
		}
		$output .= '</ol>';
		return $output;
	}

	function code_callback($matches) {
		$tr = array("\t"=>"&#9;", "["=>"&#91;", "]"=>"&#93;", "*"=>"&#42;", "%"=>"&#37;", "/"=>"&#47;", "&quot;"=>"&#34;", "-"=>"&#45;", ":"=>"&#58;", " "=>"&nbsp;", "#"=>"&#35;", "~"=>"&#126;",  "&#039;"=>"'", "&apos;"=>"'", "`"=>'&#96;', "&gt;"=>"&#62;", "&lt;"=>"&#60;" );
		$return = '<pre class="prettyprint">'.  strtr($matches[1],$tr) . '</pre>';
		return $return;
	}

	function inline_code_callback($matches) {
		$tr = array("\t"=>"&#9;", "["=>"&#91;", "]"=>"&#93;", "*"=>"&#42;", "%"=>"&#37;", "/"=>"&#47;", "&quot;"=>"&#34;", "-"=>"&#45;", ":"=>"&#58;", " "=>"&nbsp;", "#"=>"&#35;", "~"=>"&#126;",  "&#039;"=>"'", "&apos;"=>"'", "&gt;"=>"&#62;", "&lt;"=>"&#60;" );
		$return = '<pre class="inline-pp">' . strtr($matches[1],$tr) . '</pre>';
		return $return;
	}

	function ColoredQuote($buffer) {
		return preg_replace_callback('/^(&(g|l)t;)[ \t]{0,}(.*)$/m', array(&$this, 'ColoredQuoteCallback'), $buffer);
	}

	function ColoredQuoteCallback($matches) {
		$class = ($matches[2]=='g') ? 'unkfunc' : 'rquote';
		return '<span class="'.$class.'">'.$matches[1].' '.$matches[3].'</span>';
	}

	function CheckNotEmpty($buffer) {
		$buffer_temp = str_replace("\n", "", $buffer);
		$buffer_temp = str_replace("<br>", "", $buffer_temp);
		$buffer_temp = str_replace("<br/>", "", $buffer_temp);
		$buffer_temp = str_replace("<br />", "", $buffer_temp);

		$buffer_temp = str_replace(" ", "", $buffer_temp);

		if ($buffer_temp=="") {
			return "";
		} else {
			return $buffer;
		}
	}

	function InsertPics($message) {
		return preg_replace_callback('/\['.PIC_URL_PATTERN.'\]/', array(&$this, 'InsertPicsCallback'), $message);
	}
	function InsertPicsCallback($matches) {
		require_once 'inc/blocks/figure.php';
		return figure($matches[1], $matches[2]);
	}

	function ParseMessage($message) {
		$message = trim($message);
		$message = htmlspecialchars($message, ENT_QUOTES);
		$message = $this->BBCode($message);
		$message = $this->ColoredQuote($message);
		$message = str_replace("\n", '<br />', $message);
		$message = preg_replace('#(<br(?: \/)?>\s*){3,}#i', '<br /><br />', $message);
		$message = $this->InsertPics($message);
		$message = $this->MakeClickable($message);
		$message = preg_replace('# -{1,2} #is', '&nbsp;— ', $message);
		return $message;
	}
}
?>
