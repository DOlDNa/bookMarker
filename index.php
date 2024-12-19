<?php
ob_implicit_flush(1);
if (!is_file($txt = 'links.txt')) file_put_contents($txt, PHP_EOL);
if ($lines = explode(PHP_EOL, $get_txt = file_get_contents($txt)))
{
	include 'config.php';
	if ($sort_by_time) rsort($lines);
	$cls = count($lines);
	$category = filter_input(INPUT_GET, 'category');
	$tmp = ini_get('upload_tmp_dir') ?? sys_get_temp_dir();
	if ($delete = filter_input(INPUT_GET, 'delete'))
	{
		if (str_contains($get_txt, $delete))
			file_put_contents($txt, str_replace($delete. PHP_EOL, '', $get_txt), LOCK_EX);
		exit (header('Location: ./'. (!$category ? '' : '?category='. $category)));
	}
	if ($test = filter_input(INPUT_POST, 'test'))
	{
		if (file_put_contents($txt2 = $tmp. '/test.txt', urldecode($test). $get_txt))
			file_put_contents($txt, implode(PHP_EOL, array_uniq(file($txt2))). PHP_EOL);
	}
	foreach ($lines as $key => $val)
	{
		if (!trim($val)) unset($lines[$key]);
		$e = str_getcsv($val, ',', "\"", "\\");
		$a[] = $e[3] ?? '';
		$f[] = $e[1] ?? '';
		if ($search = filter_input(INPUT_GET, 'search'))
		{
			if (str_contains($val, $search)) $b[] = $val;
			$lines = $b ?? null;
		}
		elseif ($dup = filter_input(INPUT_GET, 'duplicate'))
		{
			if (isset($e[1]) && 0 === strcmp($e[1], $dup)) $b[] = $val;
			$lines = $b ?? null;
		}
		elseif ($category)
		{
			if (isset($e[3]) && 0 === strcmp(trim($e[3]), $category)) $b[] = $val;
			$lines = $b ?? null;
		}
	}
	if (isset($f)) foreach (array_count_values($f) as $k => $v) if ($k && 2 <= $v) $duplicate[] = $k;
	$max = ceil(($c = !$lines ? 0 : $cls) / $d);
	if (($p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_NUMBER_INT) ?? 1) > $max) $p = $max;
	$line = !$lines ? [] : array_slice($lines, ($p - 1) * $d, $d);
	foreach ($line as $n => $l) if ($l && 3 === substr_count($l, ',')) [$date[], $uri[], $name[], $cat[]] = str_getcsv($l, ',', "\"", "\\");
	$cl = count($line);
	if ($url = filter_input(INPUT_POST, 'url') ?? filter_input(INPUT_GET, 'url'))
	{
		ini_set('user_agent', getenv('HTTP_USER_AGENT'));
		$parse_url = parse_url($url);
		$host = idn_to_ascii($parse_url['host']);
		$scheme = $parse_url['scheme']. '://';
		$path = !isset($parse_url['path']) ? '' : r(rawurldecode($parse_url['path']));
		$query = !isset($parse_url['query']) ? '': r(rawurldecode($parse_url['query']));
		$context = stream_context_create(['http' => ['ignore_errors' => true]]);
		$contents = mb_convert_encoding(@file_get_contents($scheme. $host. $path. '?'. $query, false, $context, 0, 10240), mb_internal_encoding(), $enc);
		preg_match('/<title[^>]*>(.*?)<\/title>/is', $contents, $match);
		$title = !isset($match[1]) ? $url : trim(str_replace(["\r\n", "\r", "\n", ','], '', $match[1]));
		$categ = filter_input(INPUT_POST, 'categ') ?: filter_input(INPUT_GET, 'categ') ?: MISC[$lang];
		$data[] = time(). ','. $scheme. $host. $path. (!$query ? '' : '?'. $query). ','. $title. ','. $categ. PHP_EOL. $get_txt;
		copy($txt, $backup = $tmp. '/'. $txt);
		if ($data && file_put_contents($txt, $data, LOCK_EX) && filesize($txt) >= filesize($backup))
		{
			unlink($backup);
			exit(header('Location: ./'. (!$category ? '' : '?category='. $category)));
		}
	}
	$uniq = array_uniq($a);
	include 'html.php';
}

function r($path)
{
	return str_replace(['%23', '%2F', '%3A', '%3F', '%3D'], ['#', '/', ':', '?', '='], rawurlencode($path));
}

function h($str)
{
	return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
}

function array_uniq($array, $result=[])
{
	if (!is_array($array)) return $result;
	foreach ($array as $value)
	{
		if (!is_array($value)) $result[] = trim($value);
		$result = array_uniq($value, $result);
	}
	return array_values(array_filter(array_unique($result)));
}
