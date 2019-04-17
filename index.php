<?php

$txt = 'links.txt';

$display = 50;

$sort_categ = ''; //asce, desc

ini_set('user_agent', getenv('HTTP_USER_AGENT'));
date_default_timezone_set('Asia/Tokyo');

$msg = '';
$duplicate = [];
$url = isset($_REQUEST['url']) ? filter_var($_REQUEST['url'], FILTER_SANITIZE_STRING) : '';
$categ = isset($_REQUEST['categ']) && $_REQUEST['categ'] ? filter_var($_REQUEST['categ'], FILTER_SANITIZE_STRING) : 'その他';
$delete = filter_has_var(INPUT_GET, 'delete') ? (int)filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT) : '';

function r($path)
{
	return str_replace(['%23', '%2F', '%3A', '%3F', '%3D'], ['#', '/', ':', '?', '='], rawurlencode($path));
}

function array_uniq($array, $result=[])
{
	if (!is_array($array)) return $result;

	foreach($array as $value)
	{
		if (!is_array($value)) $result[] = $value;

		$result = array_uniq($value, $result);
	}
	$result = array_values(array_unique($result));

	return $result;
}

if (!is_file($txt))
	$msg = '<div id=msg><strong>使用方法</strong><ol><li><strong>'. basename(__DIR__). '</strong> フォルダ内に <strong>'. $txt. '</strong> を作成</li><li><strong>'. $txt. '</strong> のプロパティを開き、『その他』のアクセス権を「<strong>読み書き」</strong>に変更</li></ol></div>';

elseif (!is_writable($txt))
	$msg = '<div id=msg><strong>使用方法</strong><p><strong>'. $txt. '</strong> のプロパティを開き、『その他』のアクセス権を「読み書き」に設定して下さい</p></div>';

else
	$lines = file($txt, FILE_SKIP_EMPTY_LINES);

if ($url)
{
	$parse_url = parse_url($url);
	$host = idn_to_ascii($parse_url['host']);
	$scheme = $parse_url['scheme']. '://';
	$path = isset($parse_url['path']) ? r(rawurldecode($parse_url['path'])) : '';
	$query = isset($parse_url['query']) ? r(rawurldecode($parse_url['query'])) : '';
	$context = stream_context_create(['http' => ['ignore_errors' => true]]);
	$contents = mb_convert_encoding(@file_get_contents($scheme. $host. $path. '?'. $query, false, $context, 0, 10240), mb_internal_encoding(), 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
	preg_match('/<title>(.*?)<\/title>/is', $contents, $match);
	$title = isset($match[1]) ? trim(str_replace(["\r\n", "\r", "\n"], '', $match[1])) : $host;
	$data[] = time(). ','. $scheme. $host. $path. '?'. $query. ','. str_replace(',', '，', $title). ','. $categ. PHP_EOL. implode('', $lines);
	$bk = sys_get_temp_dir(). '/'. $txt. '.bk';
	copy($txt, $bk);

	if ($data && file_put_contents($txt, $data, LOCK_EX) && filesize($txt) >= filesize($bk))
	{
		unlink($bk);
		header('Location: ./');
		exit;
	}
	if (is_file($bk))
		$msg = '<div id=msg><strong>エラー発生</strong><p><strong>'. $bk. '</strong> を作成しました</p></div>';
}

if ($delete !== '')
{
	unset($lines[$delete]);
	file_put_contents($txt, $lines, LOCK_EX);
	header('Location: ./');
	exit;
}
?>
<!doctype html>
<html lang=ja>
<head>
<meta charset=utf-8>
<title>bookMarker</title>
<style>
#add{display:none}
#bookmark{margin:1em}
#copyright a,#reset{font-size:large;opacity:.4;transition:1s}
#copyright a:hover,#search:not(:placeholder-shown)+#reset{opacity:1;transition:1s}
#copyright{align-items:center;justify-content:space-between}
#footer{background-color:rgba(0,0,0,.7);position:sticky;bottom:0;padding:1em}
#header{background-color:rgba(0,0,0,.7);position:sticky;top:0;box-shadow:0 1px 5px 1px rgba(0,0,0,.4);padding:1em;z-index:1020}
#incsearch,#result,.sortable{width:100%}
#incsearch{display:inline-table;margin-bottom:1em}
#logo img{position:relative;display:inline-block;vertical-align:middle;margin-right:.4em;animation:bounce 2s}
#logo,#incsearch,#msg,#pager,footer{text-align:center}
#logo{margin:0;padding-top:.5em}#logo a{color:ghostwhite;font:normal 400 22pt FreeMono;letter-spacing:4pt;vertical-align:baseline}
#msg{border:thin solid dimgray;border-left:none;border-right:none;margin:0 auto;padding:1em;width:50%}
#msg address{margin:1em}
#msg ol{line-height:2;text-align:left}
#nav a{display:block;padding:2em}
#nav{border-right:solid thin dimgray;min-height:800px;min-width:20%;overflow-x:auto}
#noscript{max-width:inherit;height:500px;align-items:center;justify-content:center}
#pager{padding-bottom:1em}
#reset{color:#ffffff;cursor:pointer;background-color:rgba(0,0,0,.9);border:1px solid transparent;padding:.4em .8em;margin:-2em}
#result strong{font-weight:inherit}
#result th a,#result td a{cursor:pointer;display:block;padding:1.5em}
#result th, #result td{text-align:left;border-bottom:solid thin dimgray}
#result th:nth-child(2),#result th:nth-child(3){white-space:nowrap}
#search,#url,#categ{color:#ffffff;background-color:rgba(0,0,0,.9);border:1px solid transparent;font-size:large;padding:.4em .8em;width:30%}
#search:placeholder-shown+#reset{}
#wrapper,#noscript,#copyright{display:flex}
*{box-sizing:border-box}
.current{background-color:rgba(0,0,0,.3);font-weight:bold;border-bottom:solid thin dimgray;border-top:solid thin dimgray}
.even{background-color:rgba(0,0,0,.2)}
.link .url{top:0em;left:0;padding:.2em .5em;opacity:0;max-width:800px;background-color:rgba(0,0,0,.6);color:white;font-size:x-small;white-space:pre;border-radius:3px;position:absolute;transition:opacity 1s}
.link{position:relative}
.sortable{border-top:solid thin dimgray;border-collapse:collapse;margin:3em 0}
@keyframes bounce{0%,10%{transform:translateY(-100px)}20%,40%,60%{transform:translateY(0px)}30%{transform:translateY(-20px)}45%{transform:translateY(-4px)}}
@media(max-width:900px){th:first-child{width:auto}#result th a,#result td a,#nav a{padding:1em}}
a:hover,button:hover{opacity:.8}
a{color:#cdc8c0;text-decoration:none}
body{background-color:#2e2e2e;color:#cdc8c0;margin:0;padding:0}
strong.high1{background:linear-gradient(transparent 70%,deepskyblue 90%)}
strong.high2{background:linear-gradient(transparent 70%,palegreen 90%)}
strong.high3{background:linear-gradient(transparent 70%,fuchsia 90%)}
strong.high4{background:linear-gradient(transparent 70%,dimgray 90%)}
td:first-child:hover .url{opacity:1}
th:first-child{min-width:50%}
tr:first-child a{color:white}
tr:first-child{background-color:rgba(0,0,0,.6)}
tr:not(:first-child):hover{background-color:rgba(0,0,0,.1)}
</style>
<script src=incsearch.js></script>
<script src=sortable_ja.js></script>
<script>var d=document, w=window, lines=[
<?php
if (isset($lines))
{
	foreach($lines as $line)
	{
		if (empty(trim($line))) continue;

		$list[] = explode(',', $line);
	}
	if (isset($list))
	{
		for ($i=0, $c=count($list);$i < $c;++$i)
		{
			$uri[] = $uris = trim($list[$i][1]);

			echo '{url:"'. $uris. '",title:"'. trim($list[$i][2]). '",tags:[';

			$tag[] = $tags = explode(' ', trim($list[$i][3]));

			for ($j=0, $d=count($tags);$j < $d;++$j) echo '"'. $tags[$j]. '",';

			echo '],others:["'. date('Y-m-d H:i:s', trim($list[$i][0])). '","'. $i. '"]},'. PHP_EOL;
		}
		$a = array_count_values($uri);

		foreach($a as $k => $v)
		{
			if ($v >= 2) $duplicate[] = $k;
		}
		if ($duplicate)
		{
			$msg .= '<div id=msg><strong>以下の URL が重複しています</strong>';
			foreach($duplicate as $duplink)
				$msg .= '<address><a href="#TOP" onclick="d.getElementById(\'search\').value=\''. $duplink. '\'">'. $duplink. '</a></address>';
			$msg .= '</div>';
		}
	}
}?>];
function startIncSearch()
{
	new IncSearch.ViewBookmark('search', 'result', lines, {startElementText: '<table class=sortable><tr><th>タイトル<\/th><th>カテゴリ<\/th><th>時間<\/th><th class=unsortable><\/th>', pageLink:'pager', pagePrevName:'前のページ', pageNextName:'次のページ', changePageAfter:sortByTime, searchAfter:sortByTime, interval:10, dispMax:<?=$display?>})
}
function sortByTime()
{
	sortables_init();
	ts_resortTable(d.querySelectorAll("table th:nth-child(3) a")[0], 2)
}
function current(a,m)
{
	d.getElementById('search').value=m.textContent;
	nav=d.getElementById('nav').getElementsByTagName('a');
	for(var i=0,l=nav.length;i<l;i++)if(nav[i].hasAttribute('class'))nav[i].removeAttribute('class');
	a.setAttribute('class','current')
}
w.addEventListener('load', startIncSearch, false);
w.onload = sortByTime;
</script>
<link rel=icon href=favicon.ico>
</head>
<body>
<header id=header>
<h1 id=logo><a href=./><img src=icon.png width=27 height=60 alt=bookMarker>bookMarker</a></h1>
<div id=incsearch>
<input id=search type=text name=pattern placeholder="Search" tabindex=1>
<button id=reset onclick="d.getElementById('search').value='';w.location.href='#TOP'" tabindex=2 accesskey=r title="消去">❌</button></div>
<div id=pager></div>
<noscript><p id=noscript>Javascript を有効にして下さい</p></noscript>
<?php if ($msg) echo $msg?>
</header>
<div id=wrapper>
<nav id=nav>
<?php
if (isset($tag))
{
	$uniq = array_uniq($tag);

	if ($sort_categ === 'asce') sort($uniq);

	elseif ($sort_categ === 'desc') rsort($uniq);

	foreach($uniq as $menu) echo '<a href="#top" onclick="current(this,this.childNodes[0])">'. $menu. '</a>';
}
?>
</nav>
<main id=result></main>
</div>
<footer id=footer>
<?php if (isset($lines)) {?>
<form method=post id=bookmark><input id=url type=url name=url placeholder="URL" tabindex=10 required><input id=categ type=text name=categ placeholder="カテゴリ" title="半角スペースで区切って複数のカテゴリを入力することができます" tabindex=20
<?php
if (isset($uniq))
{
	echo ' autocomplete=on list=datalist><datalist id=datalist>';
	foreach($uniq as $autocomp) echo '<option value="'. $autocomp. '"></option>';
	echo'</datalist';
}
?>><input id=add type=submit accesskey=s></form>
<?php }?>
<div id=copyright>
<small>&copy; <?=date('Y')?> bookMarker.</small><a href=#top>⏫</a>
</div>
</footer>
</body>
</html>