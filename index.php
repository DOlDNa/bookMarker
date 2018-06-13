<?php

##### 設定 #####

$txt = 'links.txt';

$display = 20;

$sort_categ = ''; // asce か desc または 空欄

// テーブルの背景色は sortable_ja.js の alternate_row_colors または .even を編集

##### 設定終了 #####


ini_set('user_agent', getenv('HTTP_USER_AGENT'));
date_default_timezone_set('Asia/Tokyo');

$msg = '';
$duplicate = [];
$url = isset($_REQUEST['url']) ? filter_var($_REQUEST['url'], FILTER_SANITIZE_STRING) : '';
$categ = isset($_REQUEST['categ']) && $_REQUEST['categ'] ? filter_var($_REQUEST['categ'], FILTER_SANITIZE_STRING) : 'その他';
$delete = filter_has_var(INPUT_GET, 'delete') ? (int)filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT) : '';

function r($path)
{
    return str_replace(array('%26', '%2F', '%5C', '%3A'), array('&amp;', '/', '/', ':'), rawurlencode($path));
}

function array_uniq($array, $result=[])
{
    if (!is_array($array))
        return $result;

    foreach($array as $value)
    {
        if (!is_array($value))
            $result[] = $value;

        $result = array_uniq($value, $result);
    }
    $result = array_values(array_unique($result));

    return $result;
}

if (!is_file($txt))
    $msg = '<p id=msg><strong>'. basename(__DIR__). '</strong> フォルダ内に <strong>'. $txt. '</strong> を作成してからプロパティを開き、<br>『その他』のアクセス権を「読み書き」に設定して下さい</p>';

elseif (!is_writable($txt))
    $msg = '<p id=msg><strong>'. $txt. '</strong> のプロパティを開き、『その他』のアクセス権を「読み書き」に設定して下さい</p>';

if (is_file($txt) && is_writable($txt))
    $lines = file($txt, FILE_SKIP_EMPTY_LINES);

if ($url)
{
    $parse_url = parse_url($url);
    $host = idn_to_ascii($parse_url['host']);
    $scheme = $parse_url['scheme']. '://';
    $path = isset($parse_url['path']) ? r(rawurldecode($parse_url['path'])) : '';
    $context = stream_context_create(array('http' => array('ignore_errors' => true)));
    $contents = mb_convert_encoding(@file_get_contents($scheme. $host. $path, false, $context, 0, 10240), mb_internal_encoding(), 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    preg_match('/<title>(.*?)<\/title>/is', $contents, $match);
    $title = isset($match[1]) ? trim(str_replace(array("\r\n", "\r", "\n"), '', $match[1])) : $host;
    $data[] = time(). ','. $scheme. $host. $path. ','. str_replace(',', '，', $title). ','. $categ. PHP_EOL. implode('', $lines);
    $bk = sys_get_temp_dir(). '/'. $txt. '.bk';
    copy($txt, $bk);

    if ($data && file_put_contents($txt, $data, LOCK_EX) && filesize($txt) >= filesize($bk))
    {
        unlink($bk);
        header('Location: ./');
        exit;
    }
    elseif (is_file($bk))
            $msg = '<p id=msg>エラーが発生したため、<strong>'. $bk. '</strong> を作成しました</p>';
}

if ($delete !== '')
{
    unset($lines[$delete]);
    file_put_contents($txt, $lines);
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
            #dropbtn,#dropdown-menu a,button,.sortheader{cursor:pointer}
            #dropbtn{background-color:mediumseagreen}
            #dropdown,h1,h1 img{position:relative;display:inline-block;vertical-align:middle}
            h1 img{margin-right:.2em;animation:bounce 2s}
            @keyframes bounce{
                0%,10%{transform:translateY(-100px)}
                20%,40%,60%{transform:translateY(0px)}
                30%{transform:translateY(-20px)}
                45%{transform:translateY(-4px)}
            }
            #dropdown-menu a:hover{color:black;background-color:whitesmoke}
            #dropdown-menu{opacity:0;max-height:35em;overflow:auto;visibility:hidden;transition:visibility .5s,opacity .2s linear;position:absolute;background-color:#fefefe;min-width:15em;box-shadow:0px 8px 8px 0px grey;z-index:1021}
            #dropdown:hover #dropbtn{background-color:seagreen}
            #dropdown:focus #dropdown-menu{opacity:1;visibility:visible}
            #dropdown-menu a,address{display:block}
            #msg{line-height:1.5em;margin:2em auto;width:90%;border:thin solid dimgray;border-left:none;border-right:none;padding:1em}
            #noscript{max-width:inherit;height:500px;display:flex;align-items:center;justify-content:center}
            #pager{padding-bottom:1em}
            #reset{margin-right:1em}
            #result strong{font-weight:inherit}
            #result table{min-width:90%;margin:2em auto;border-top:solid thin dimgray;border-collapse:collapse}
            #result th:nth-child(2),#result td:nth-child(3),#result td:nth-child(4){white-space:nowrap}
            #result th, #result td{text-align:left;padding:1.5em;border-bottom:solid thin}
            #search,#reset{float:right;margin-top:1em}
            #search{width:20%}
            *{box-sizing:border-box;font-family:"Droid Sans","Yu Gothic",YuGothic,"Hiragino Sans",sans-serif}
            .even{background-color:azure}
            .link{position:relative}
            .link .url{top:-2.5em;left:0;padding:.5em;opacity:0;max-width:800px;background-color:black;color:white;white-space:pre;border-radius:3px;position:absolute;transition:opacity 1s}
            td:first-child:hover .url{opacity:1}
            a:hover,button:hover{opacity:.8}
            a{color:dimgray;text-decoration:none}
            footer{margin:1em}
            h1,#dropbtn,#dropdown-menu a{margin:0;padding:.5em}
            header form,#msg,#pager,footer{text-align:center}
            header{background-color:whitesmoke;position:sticky;top:0;box-shadow:0 1px 5px 1px grey;z-index:1020}
            html,body{color:#555555;margin:0;padding:0}
            input,#reset,button[type=submit]{margin-bottom:1em;background-color:whitesmoke;font-size:1em;height:51px;line-height:3em;border:thin solid dimgray;border-left:none;border-top:none;border-right:none}
            input[name=url],input[name=categ]{width:40%}
            strong.high1{background:linear-gradient(transparent 70%,deepskyblue 90%)}
            strong.high2{background:linear-gradient(transparent 70%,palegreen 90%)}
            strong.high3{background:linear-gradient(transparent 70%,fuchsia 90%)}
            strong.high4{background:linear-gradient(transparent 70%,dimgray 90%)}
            th:first-child{min-width:50%}
            tr:first-child a,#dropbtn{color:white}
            tr:first-child{background-color:dodgerblue}
            tr:not(:first-child):hover{background-color:ghostwhite}
        </style>
        <script src=incsearch.js></script>
        <script src=sortable_ja.js></script>
        <script>
            var d=document, w=window, lines=[
            <?php

            if (isset($lines))
            {
                foreach($lines as $line)
                {
                    if (empty(trim($line)))
                        continue;

                    $list[] = explode(',', $line);
                }

                if (isset($list))
                {
                    for ($i=0, $c=count($list);$i < $c;++$i)
                    {
                        $uri[] = $uris = trim($list[$i][1]);

                        echo '{url:"'. $uris. '",title:"'. trim($list[$i][2]). '",tags:[';

                        $tag[] = $tags = explode(' ', trim($list[$i][3]));

                        for ($j=0, $d=count($tags);$j < $d;++$j)
                            echo '"'. $tags[$j]. '",';

                        echo '],others:["'. date('Y-m-d H:i:s', trim($list[$i][0])). '","'. $i. '"]},'. "\n\t\t\t";
                    }
                    $a = array_count_values($uri);

                    foreach($a as $k => $v)
                    {
                        if ($v >= 2)
                            $duplicate[] = $k;
                    }

                    if ($duplicate)
                    {
                        $msg .= '<div id=msg><strong>以下の URL が重複しています</strong>';

                        foreach($duplicate as $duplink)
							$msg .=  '<address><a href="#TOP" onclick="d.getElementById(\'search\').value=\''. $duplink. '\'">'. $duplink. '</a></address>';

                        $msg .= '</div>';
                    }
                }
            }
            ?>];

            function startIncSearch()
            {
                new IncSearch.ViewBookmark('search', 'result', lines, {startElementText: '<table class=sortable><tr><th>タイトル<\/th><th>カテゴリ<\/th><th>時間<\/th><th class=unsortable><\/th>', pageLink:'pager', pagePrevName:'前のページ', pageNextName:'次のページ', changePageAfter:sortByTime, searchAfter:sortByTime, interval:10, dispMax:<?=$display?>})
            }

            function sortByTime()
            {
                sortables_init();
                ts_resortTable(d.querySelectorAll("table th:nth-child(3) a")[0], 2)
            }

            w.addEventListener ? w.addEventListener('load', startIncSearch, false) : w.attachEvent('onload', startIncSearch);
            w.onload = sortByTime;
        </script>
        <link href=favicon.png rel=icon type="image/png" sizes="64x64">
    </head>
    <body>
        <header>
            <button id=reset onclick="d.getElementById('search').value='';w.location.href='#TOP'" tabindex=2 accesskey=r>消去</button>
            <input id=search type=text name=pattern placeholder="Search" tabindex=1>
            <h1><a href=./><img src=icon.png width=30 height=66 alt=bookMarker>bookMarker</a></h1>
            <?php

            if (isset($tag))
            {
                echo
                '<div tabindex=0 id=dropdown>
                <div id=dropbtn>カテゴリ一覧</div>
                <div id=dropdown-menu>';

                $uniq = array_uniq($tag);

                if ($sort_categ === 'asce')
                    sort($uniq);

                elseif ($sort_categ === 'desc')
                    rsort($uniq);

                foreach($uniq as $menu)
                    echo '
                    <a href="#TOP" onclick="d.getElementById(\'search\').value=\''. $menu. '\'">'. $menu. '</a>';

                echo '
                </div>
            </div>';
            }

            if (!$msg)
            {
                echo '
            <form method=post>
                <input type=url name=url placeholder="URL" tabindex=10 required><!--
             --><input type=text name=categ placeholder="カテゴリ" title="半角スペースで区切って複数のカテゴリを入力することができます" tabindex=20';

				if (isset($uniq))
				{
					echo ' autocomplete=on list=datalist><datalist id=datalist>';
					foreach($uniq as $autocomp)
						echo '<option value="'. $autocomp. '"></option>';
					echo'</datalist>';
				}
				else
					echo '>';
				echo '<!--
             --><button type=submit tabindex=30 accesskey=s>追加</button>
            </form>';
            }
            ?>

            <div id=pager></div>
        </header>
        <?=$msg?>

        <div id=result><noscript><p id=noscript>Javascript を有効にして下さい</p></noscript></div>
        <footer><small>&copy; <?=date('Y')?> bookMarker.</small></footer>
    </body>
</html>