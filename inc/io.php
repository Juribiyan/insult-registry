<?php
require_once 'inc/instamark.php';
$markup = new Parse();

function validate($arr, $schema) {
	global $markup; 
	
	$errors = array();
	$fields = array();

	foreach ($schema as $key => $sc) {
		$str = @$arr[$key];
		$err_found = false;
		if (is_null($str)) {
			if (@$sc['optional']) {
				$fields[$key] = (@$sc['default'] ? $sc['default'] : '');
			}
			else {
				$errors []= "Отсутствует поле $key";
				$err_found = true;
			}
			continue;
		}
		if (@$sc['numeric']) {
			if (!is_numeric($str)) {
				$errors []= "Поле $key должно быть числом";
				$err_found = true;
			}
			else {
				$str = (int)$str;
				// extra checks for numbers...
			}
		}
		else {
			$str = trim($str);
			$length = mb_strlen($str);
			if ($length == 0) {
				if (!@$sc['optional']) {
					$errors []= "Поле $key не должно быть пустым";
					$err_found = true;
				}
			}
			else {
				if (@$sc['minLength'] && mb_strlen($str) < $sc['minLength']) {
					$errors []= "Поле $key должно иметь минимальную длину " . $sc['minLength'] . " символов";
					$err_found = true;
				}
				if (@$sc['maxLength'] && mb_strlen($str) > $sc['maxLength']) {
					$errors []= "Длина поля $key не должна превышать " . $sc['maxLength'] . " символов";
					$err_found = true;
				}
				if (@$sc['markup']) {
					$str = $markup->parseMessage($str);
				}
				if (@$sc['url'] && !filter_var($str, FILTER_VALIDATE_URL)) {
					$errors []= "Поле $key не является валидной ссылкой";
					$err_found = true;
				}
				if (@$sc['pattern']) {
					$pattern_pass = preg_match($sc['pattern'], $str, $matches);
					if (!$pattern_pass) {
						$errors []= "Поле $key не соответствует паттерну";
						$err_found = true;
					}
					elseif(isset($sc['extract_match'])) {
						$str = $matches[$sc['extract_match']];
					}
				}
				if (@$sc['pattern'] && !preg_match($sc['pattern'], $str)) {
					$errors []= "Поле $key не соответствует паттерну";
					$err_found = true;
				}
			}
		}
		if (!$err_found/* && !$skip_field*/)
			$fields[$key] = $str;
	}
	if (count($errors)) {
		exitWithError(400, join('<br>', $errors));
	}
	return $fields;
}

function sanitizeOutput($arr, $keys, $filter_out = false) {
	$r = $filter_out ? array() : $arr;
	foreach($keys as $key) {
		if (array_key_exists($key, $arr)) {
			$r[$key] = htmlspecialchars($arr[$key]);
		}
	}
	return $r;
}

function exitWithError($code, $error_message) {
	http_response_code($code);
	if (@$_GET['ajax']) {
		header('Content-type: application/json; charset=UTF-8');
		die(json_encode([
			"error" => $error_message
		]));
	}
	if (is_array($error_message)) {
		$error_message = join('<br>', $error_message);
	}
	include "inc/blocks/error.php";
	die();
}

function redirectToPage($page) {
	if($page == 0)
		$page = '';
	header("Location: " . recreateRoute($page)[0]);
	die();
}

function exitWithJSON($data) {
	$data['error'] = false;
	header('Content-type: application/json; charset=UTF-8');
	die(json_encode($data));
}

function recreateRoute($page = '') {
	if($page == 0)
		$page = '';

	$param_map = 
		(isset($_GET['offender']) ? 'o' : '_') .
		(isset($_GET['victim'])   ? 'v' : '_') .
		(isset($_GET['site'])     ? 's' : '_');

	$ovs_query = getOVSquery();

	switch ($param_map) {
		case '___':
			$route = "/all/" . $page;
			break;

		case 'o__':
			$route = "/offender/" . $_GET['offender'] . "/" . $page;
			break;

		case 'ov_':
			$route = "/pair/" . $_GET['offender'] . "/" . $_GET['victim'] . "/" . $page;
			break;

		case '_v_':
			$route = "/victim/" . $_GET['victim'] . "/" . $page;
			break;

		case '__s':
			$route = "/site/" . $_GET['site'] . "/" . $page;
			break;
		
		default:
			$route = "/entries.php?$ovs_query&page=$page";
	}
	
	return [$route, $ovs_query];
}

function getOVSquery() {
	$url_components = [];
	foreach(['offender', 'victim', 'site'] as $key) {
		if (isset($_GET[$key]))
			$url_components []= $key . "=" . $_GET[$key];
	}
	return implode("&", $url_components);
}

function checkCaptcha() {
	session_start();
	$secret = @$_SESSION['security_code'];
	$birth = @$_SESSION['captchatime'];
	$answer = @$_POST['captcha'];
	$now = time();
	unset($_SESSION['security_code']);
	if ($now - $birth > CAPTCHA_LIFE) {
		exitWithError(400, "Капча протухла " . ($now - $birth - CAPTCHA_LIFE) . " c назад");
	}
	if (!$secret || !$answer || $secret!=$answer) {
		exitWithError(400, "Неправильно введена капча");
	}
}