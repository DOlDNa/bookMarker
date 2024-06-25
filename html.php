<?=
'<!doctype html>',
'<html lang=', LANG[$lang], '>',
'<head>',
'<meta charset=utf-8>',
'<title>bookMarker</title>',
'<style>#search,#url,[type=text]{color:#cdcdcd;background-color:rgba(0,0,0,.9);border:1px solid transparent;font-size:large;padding:.4em .8em;width:35%}#search{margin-bottom:1em}*{box-sizing:border-box;overflow-wrap:anywhere}.disabled{opacity:.2}.nav{font-size:xx-large;vertical-align:top;padding-left:.5em;padding-right:.5em}a,body{color:#cdcdcd;text-decoration:none}a:hover{opacity:.8}address{margin:1em}#add{margin-left:.5em}aside,h1,header form,main ul:nth-child(3),main ul:last-child,footer form{text-align:center}aside{border:thin solid dimgray;border-left:none;border-right:none;margin:0 auto 1em;padding:1em;width:40%}body,div,main,main ul,footer{display:flex}body,header,menu,main ul{margin:0;padding:0}body{background-color:#2e2e2e;flex-flow:column;height:100vh}div,footer form{flex:1}footer{bottom:0;padding:1em}footer a{align-self:center;background-color:rgba(255,255,255,.8);padding:.1em}h1 a{color:ghostwhite;font:normal 400 1em "URW Gothic";letter-spacing:5pt}h1 img{vertical-align:middle;margin-right:.4em;animation:bounce 2s}h1{margin:0;padding-top:.5em}header,footer{background-color:rgba(0,0,0,.5);position:sticky}header{box-shadow:0 1px 5px 1px rgba(0,0,0,.4);top:0;z-index:10}', ($sort_by_time ? '' : 'main ul:before{content:"'. GRABHERE[$lang]. '";cursor:move;padding-left:1em}'), 'main ul li:first-child{text-align:left;width:100%}main ul li:last-child{padding-left:1em}main ul li:nth-child(2),main ul li:nth-child(3){text-align:right;white-space:pre}main ul:nth-child(even),.current{background-color:rgba(0,0,0,.2)}main ul{align-items:center}main{flex-direction:column;margin-bottom:5em;width:85vw}menu a{position:relative}menu a,main ul li a{display:block;padding:1.5em}menu a,main ul{border-bottom:solid thin dimgray}menu,main ul{list-style:none}menu{border-right:solid thin dimgray;width:15vw}@keyframes bounce{0%,10%{transform:translateY(-100px)}20%,40%,60%{transform:translateY(0px)}30%{transform:translateY(-20px)}45%{transform:translateY(-4px)}}@media(max-width:800px){footer{padding:.5em}main ul li a,menu a,main ul li:nth-child(3){padding:1em;white-space:normal}main ul{flex-direction:column;align-items:flex-start}main ul:before{content:"";padding:0}main ul li{width:auto!important}main ul li:last-child{padding-left:0}}</style>',
'<link rel=icon href=favicon.ico>',
'</head>',
'<body>',
'<header>',
'<h1><a href=./ tabindex=-1><img src=icon.png width=27 height=60 alt=bookMarker>bookMarker</a></h1>',
'<form method=get>',
'<a class="nav', ($p > 1 ? '" href="./?p='. ($p - 1). (!$category ? '' : '&amp;category='. $category) : ' disabled'), '" tabindex=0 accesskey=z>', PREV[$lang], '</a>',
'<input id=search name=search type=search placeholder="'. SEARCH[$lang]. '" tabindex=0', (!isset($search) ? '' : ' value="'. h($search). '"'), ' accesskey=y>',
'<a class="nav', ($p < $max ? '" href="./?p='. ($p + 1). (!$category ? '' : '&amp;category='. $category) : ' disabled'), '" tabindex=0 accesskey=x>', NEXT[$lang], '</a>',
'</form>';
if (isset($duplicate))
{
	echo '<aside><strong>', DUP[$lang], '</strong>';
	foreach($duplicate as $duplink) echo '<address><a href="./?duplicate=', r($duplink), '">', h($duplink), '</a></address>';
	echo '</aside>';
}
echo
'</header>',
'<div>',
'<menu>';
if (isset($uniq))
{
	foreach($uniq as $akey => $menu)
	{
		$bkm[] = $menu;
		$accesskey = range('a', 'w')[$akey];
		echo '<li><a href="./?category=', r($menu), '"', ($menu === $category ? ' class=current' : ''), ' draggable=true accesskey="', $accesskey, '">', h($menu), '</a></li>';
		$options[] = '<option value="'. h($menu). '"'. ($menu === $category ? ' selected' : ''). '></option>';
	}
	if (!is_file($bkmtxt = $tmp. 'bkm.txt')) file_put_contents($bkmtxt, implode(PHP_EOL, $bkm));
}
echo
'</menu>',
'<main>';
if (isset($cl))
{
	$i = 0;
	while ($i < $d)
	{
		if (isset($date[$i], $uri[$i], $name[$i], $cat[$i]) && is_numeric(substr($date[$i], 0, strlen($date[$i]))))
		echo
		'<ul', ($sort_by_time ? '' : ' draggable=true'), ' id="a', $i, '">',
		'<li><a href="', $uri[$i], '" rel="noopener noreferrer" target="_blank" tabindex=1>', h($name[$i]), '</a></li>',
		'<li><a href="./?category=', r($cat[$i]), '">', h($cat[$i]), '</a></li>',
		'<li><small>', date(TIME[$lang], $date[$i]), '</small></li>',
		'<li><a href="./', (!$category ? '?' : '?category='. $category. '&amp;'), 'delete=', r($date[$i]. ','. $uri[$i]. ','. $name[$i]. ','. $cat[$i]), '" onclick="return confirm(\'', TRASH[$lang], '\')">', TRASH[$lang], '</a></li>',
		'</ul>';
		++$i;
		if ($cl <= $i) break;
	}
}
echo
'</main>',
'</div>',
'<footer>',
'<form method=post>',
'<input id=url type=url name=url placeholder="', URL[$lang], '" required>',
'<input id=categ type=text name=categ placeholder="', CATEG[$lang], '"',
(!isset($category) ? '' : ' value="'. $category. '"'),
(!isset($options) ? '' : ' autocomplete=on list=categlist><datalist id=categlist>'. implode($options). '</datalist'), '>',
'<input id=add type=image src=icon.png width=15 align=top>',
'</form>',
'<a href=#TOP>', TOP[$lang], '</a>',
'</footer>',
'<script>[].slice.call(document.querySelectorAll("ul")||[]).map((e,i)=>{e.addEventListener("contextmenu",ev=>{ev.preventDefault();u=ev.target.closest("ul"),r=u.lastChild.lastChild;if(r){s=r.href.split("delete=")[1],q=s.split("%2C"),t=document.createElement("input"),t.name="i"+u.id,t.type="text";t1=t.cloneNode(1),t2=t.cloneNode(1),t3=t.cloneNode(1),t.setAttribute("value",decodeURIComponent(q[0])),t1.setAttribute("value",decodeURIComponent(q[1])),t2.setAttribute("value",decodeURIComponent(q[2])),t3.setAttribute("value",decodeURIComponent(q[3]));ev.target.closest("ul").innerHTML=t.outerHTML+t1.outerHTML+t2.outerHTML+t3.outerHTML;[].slice.call(document.querySelectorAll("[name="+t.name+"]")||[]).map((en,ei)=>{en.addEventListener("keydown",evt=>{if("Enter"===evt.code){fetch("./?delete="+s).then(()=>d(f(document.getElementsByName(t.name)[0].value)+","+f(document.getElementsByName(t.name)[1].value)+","+f(document.getElementsByName(t.name)[2].value)+","+f(document.getElementsByName(t.name)[3].value)+"\n"))}})})}return false},false)}),ondragstart=({target})=>{dragged=target,id=target.id,dlist=document.querySelectorAll("ul");for(i1=0,l1=dlist.length;i1<l1;i1++){if(dragged===dlist[i1])idx=i1}},ondragover=({target,drop})=>{if("UL"===target.tagName&&id!==target.id){for(i2=0,l2=dlist.length;i2<l2;i2++){if(target===dlist[i2])drop=i2;if(idx>=drop)target.before(dragged);else target.after(dragged)}}},ondragend=({target})=>{list=document.querySelectorAll("ul li:last-child a"),b=[];for(i3=0,l3=list.length;i3<l3;i3++)b+=list[i3].href.split("delete=")[1]+"\n";d(b)},d=(a)=>{let b=new FormData();b.append("test",a);fetch("./index.php",{method:"POST",cache:"no-cache",body:b}).then(()=>location.reload())},f=(b)=>{return encodeURIComponent(b.replace(/,/g,"").trim())},document.querySelectorAll("li a[accesskey]").forEach(aa=>{if(aa.accessKey.match(/[a-w]/gi)){kbd=document.createElement("kbd");kbd.style.cssText="background:#222;font-size:xx-small;padding:.2rem;position:absolute;right:0;top:0";kbd.appendChild(document.createTextNode("Alt + Shift + "+aa.getAttribute("accesskey")));aa.appendChild(kbd)}})</script>',
'</body>',
'</html>';
