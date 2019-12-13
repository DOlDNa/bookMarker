<?='<!doctype html>
<html lang=ja>
<head>
<meta charset=utf-8>
<title>bookMarker</title>
<style>
#search,#url,#categ{color:#cdcdcd;background-color:rgba(0,0,0,.9);border:1px solid transparent;font-size:large;padding:.4em .8em;width:30%}
#search{margin-bottom:1em}
*{box-sizing:border-box;overflow-wrap:anywhere}
.disabled{opacity:.2}
.nav{font-size:xx-large;vertical-align:middle}
a,body{color:#cdcdcd;text-decoration:none}
a:hover{opacity:.8}
address{margin:1em}
aside,footer,h1,header form,main ul:nth-child(3),main ul:last-child{text-align:center}
aside{border:thin solid dimgray;border-left:none;border-right:none;margin:0 auto 1em;padding:1em;width:40%}
body,div,main,main ul{display:flex}
body,header,menu,main ul{margin:0;padding:0}
body{background-color:#2e2e2e;flex-flow:column;height:100vh}
div{flex:1}
footer{bottom:0;padding:1em}
h1 a{color:ghostwhite;font:normal 400 1em FreeMono;letter-spacing:4pt}
h1 img{vertical-align:middle;margin-right:.4em;animation:bounce 2s}
h1{margin:0;padding-top:.5em}
header,footer{background-color:rgba(0,0,0,.5);position:sticky}
header{box-shadow:0 1px 5px 1px rgba(0,0,0,.4);top:0;z-index:10}
input[type=submit]{display:none}
main ul:before{content:"'. GRABHERE[$lang]. '";cursor:move;margin:0;padding:1em}
main ul li:first-child{text-align:left;width:100%}
main ul li:last-child{text-align:right;width:10%}
main ul li:nth-child(2),main ul li:nth-child(3){text-align:right;width:20%;white-space:pre}
main ul:nth-child(even),.current{background-color:rgba(0,0,0,.2)}
main ul{align-items:center}
main{flex-direction:column;margin-bottom:5em;width:80%}
menu a,main ul li a{display:block;padding:1.5em}
menu a,main ul{border-bottom:solid thin dimgray}
menu,main ul{list-style:none}
menu{border-right:solid thin dimgray;width:20%}
main ul li a{padding-left:0}
@keyframes bounce{0%,10%{transform:translateY(-100px)}20%,40%,60%{transform:translateY(0px)}30%{transform:translateY(-20px)}45%{transform:translateY(-4px)}}
@media(max-width:900px){footer{padding:.5em}main ul li a,menu a,main ul li:nth-child(3){padding:1em;white-space:normal}main ul{flex-direction:column;align-items:flex-start}main ul:before{content:"";padding:0}main ul li{width:auto!important}}
</style>
<link rel=icon href=favicon.ico>
</head>
<body>
<header>
<h1><a href=./><img src=icon.png width=27 height=60 alt=bookMarker>bookMarker<sup>&copy;</sup></a></h1>
<form method=get>
<a class="nav', ($p > 1 ? '" href="./?p='. ($p - 1). (!$category ? '' : '&amp;category='. $category) : ' disabled'), '">'. PREV[$lang]. '</a>
<input id=search name=search type=text placeholder="'. SEARCH[$lang]. '" tabindex=1', (!isset($search) ? '' : ' value="'. h($search). '"'), '>
<a class="nav', ($p < $max ? '" href="./?p='. ($p + 1). (!$category ? '' : '&amp;category='. $category) : ' disabled'), '">'. NEXT[$lang]. '</a>
</form>';
if (isset($duplicate))
{
	echo '<aside><strong>'. DUP[$lang]. '</strong>';
	foreach($duplicate as $duplink) echo '<address><a href="./?duplicate='. r($duplink). '">'. h($duplink). '</a></address>';
	echo '</aside>';
}
echo '
</header>
<div>
<menu>';
if (isset($uniq))
{
	foreach($uniq as $menu)
	{
		echo '<li><a href="./?category=', r($menu), '"', ($menu === $category ? ' class=current' : ''), ' draggable=true>', h($menu), '</a></li>';
		$options[] = '<option value="'. h($menu). '"'. ($menu === $category ? ' selected' : ''). '></option>';
	}
}
echo '
</menu>
<main>';
if (isset($cl, $date[0], $uri[0], $name[0], $cat[0]))
{
	$i = 0;
	while ($i < $d)
	{
		echo '<ul draggable=true><li><a href="', $uri[$i], '" target="_blank">', h($name[$i]), '</a></li><li><a href="./?category=', r($cat[$i]), '">', h($cat[$i]), '</a></li><li>', date(TIME[$lang], $date[$i]), '</li><li><a href="./', (!$category ? '?' : '?category='. $category. '&amp;'), 'delete=', r($date[$i]. ','. $uri[$i]. ','. $name[$i]. ','. $cat[$i]), '"><img src="Font_Awesome_5_regular_trash-alt.svg" width=16 height=20 alt=del></a></li></ul>';
		++$i;
		if ($cl <= $i) break;
	}
}
echo '
</main>
</div>
<footer>
<form method=post>
<input id=url type=url name=url placeholder="'. URL[$lang]. '" required>
<input id=categ type=text name=categ placeholder="'. CATEG[$lang]. '"',
(!isset($category) ? '' : ' value="'. $category. '"'),
(!isset($options) ? '' : ' autocomplete=on list=categlist>
<datalist id=categlist>'. implode('', $options). '</datalist'), '>
<input id=add type=submit>
</form>
</footer>
<script>function c(e){e.addEventListener("dragstart",function(e){a=this;e.dataTransfer.effectAllowed="move";e.dataTransfer.setData("text/html",this.outerHTML)},false);e.addEventListener("dragover",function(e){e.preventDefault()},false);e.addEventListener("drop",function(e){if (a!==this){this.parentNode.removeChild(a);this.insertAdjacentHTML("beforebegin",e.dataTransfer.getData("text/html"));c(this.previousSibling)}b=[];Array.from(this.parentNode.children,f=>{b+=f.lastChild.lastChild.href.split("delete=")[1]+"\n"});d(b);return false},false)}[].forEach.call(document.querySelectorAll("ul"),c);function d(a){let b=new FormData(),c=new XMLHttpRequest();b.append("test",a);c.open("post","./index.php");c.send(b)}</script>
</body>
</html>';