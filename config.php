<?php

#1ページあたりの表示件数
$d = 50;

#表示言語：0=英語、1=日本語
$lang = 1;

$enc = 'ASCII, JIS, UTF-8, EUC-JP, SJIS';

date_default_timezone_set('Asia/Tokyo');
define('CATEG', ['Categories', 'カテゴリ', '']);
define('DUP', ['Duplicate items', '重複しています', '']);
define('MISC', ['Miscellaneous', 'その他', '']);
define('NEXT', ['&gt;', '▶', '']);
define('PREV', ['&lt;', '◀', '']);
define('SEARCH', ['Search', 'Search', '']);
define('TIME', ['F j, Y, g:i a', "Y年m月d日\nH時i分s秒", '']);
define('URL', ['URL', 'URL', '']);
define('GRABHERE', ['•', '・', '']);
