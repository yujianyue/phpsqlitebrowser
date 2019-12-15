<?php $title = "php Db browser For sqlite"; ?><!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo $title; ?></title>
<meta name="author" content="yujianyue, admin@ewuyi.net">
<meta name="copyright" content="www.12391.net">
<style>
*{margin:0; padding:0; font-size:12px;}
h1{ font-size:18px;}
a{padding:5px 6px;color:blue;line-height:150%;text-decoration:none;}
a:hover{border:1px solid #a2c6d3;background-color:#0180CF;color:white;}
b{padding:5px 10px;background-color:#0180CF;color:white;}
select{width:99%;line-height:150%;}
table{margin:5px auto;border-left:1px solid #a2c6d3;border-top:3px solid #0180CF;width:96vw;}
table td{border-right:1px solid #a2c6d3;border-bottom:1px solid #a2c6d3;}
table td{padding:5px;word-wrap:break-word;word-break:break-all;}
.tt{background:#e5f2fa;line-height:150%;font-size:14px;padding:5px 9px;}
</style>
</head>
<body>
<?php
/*

php.sqlite.browser V20191215

功能：列出网站目录下的sqlite数据并浏览：
1. 遍历文件夹下所有.db,.sqlite文件供选并记忆；
2. 然后该数据文件下所有表供选并记忆；
3. 列出该表下所有字段及内容；
由于不保证每个数据都有唯一不重复字段，暂无表的增改删计划
未设密码，推荐文件夹下更名为任意文件名使用以保障数据安全

问题反馈：15058593138 (同微信号)
或发邮件：admin@ewuyi.net

另外可能后续开发
php access;php mysql;php csv;asp access;asp excel
等版本,敬请关注

*/


 $dbcokie = $_COOKIE['dbsname'];
 $dbsname = $_GET['db']; //get datas参数
 $rewname = $_GET['tb']; //get table参数
if(file_exists($dbsname)){
 setcookie('dbsname',$dbsname,time()+60*60*24*31,"/"); //31天记忆数据库
}else{
 if(file_exists($dbcokie)){
 $dbsname = $dbcokie;
 }else{
 $dbsname = "sqlite.db";
 }
}

 $kbmb = "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td><!--txt--></td></tr></table>";

function listFile($dirName){
 global $dbsname;
 $dbtypes = "-sqlite-db-"; //识别指定sqlite的后缀
if ($handle = opendir($dirName)) {
while (false !== ($item = readdir($handle ))) {
if ( $item != "." && $item != ".." ) {
if ( is_dir($dirName.$item) ) {
 $listar .= listFile($dirName.$item."/");
} else {
 $filetv = explode(".",$item);
 $filetp = end($filetv);
 $fileph = $dirName.$item;
 if(stristr($dbtypes,"-{$filetp}-")){
if($fileph==$dbsname){
$listar .= "<option value=\"{$fileph}\" selected>$fileph</option>";
}else{
$listar .= "<option value=\"{$fileph}\">$fileph</option>";
}
 }
}
}
}
closedir($handle);
return $listar;
}
}

 echo "<div style=\"margin:0 auto;overflow:auto;width:99%;height:95vh;\">";
 if(!file_exists($dbsname)){
 echo str_replace("<!--txt-->","暂未选择数据",$kbmb);
 }
 echo "<table cellspacing=\"0\" cellpadding=\"0\"><tr>";
 echo "<td width=\"100\">选择数据库:<br>";
 echo "<select onchange=\"window.location='?db='+this.value;\" />";
 $lister = listFile("./"); //推荐根目录下的文件夹下使用
 if(strlen($lister)>10){echo $lister;}else{echo "<option value=\"\">无sqlite数据</option>";}
 echo "</select>";
 echo "</td></tr></table>";
 $db = new SQLite3($dbsname); //先修改数据库名称
 if(!$db){
$ers = $db->lastErrorMsg();
echo str_replace("<!--txt-->","读取失败:{$ers}",$kbmb);
 }else{
 $result = $db->query('select * from sqlite_master WHERE type = "table"');
 $rawname = "-"; $rawhtml = ""; $ic=0;
while($rows = $result->fetchArray(SQLITE3_ASSOC)){
 $rowname = $rows['name']; $rawname .= $rowname."-"; $ic++;
if($rewname == ""){ $rewname = $rowname; }
if($rowname == $rewname){
$rawhtml .= "<option value=\"{$rowname}\" selected>$rowname</option>";
}else{
$rawhtml .= "<option value=\"{$rowname}\">$rowname</option>";
}
 }
if($ic<1){
 echo str_replace("<!--txt-->","暂无表格",$kbmb);
}else{
 $rawhtml = "<select onchange=\"window.location='?tb='+this.value;\" />$rawhtml</select>";
 echo str_replace("<!--txt-->","选择数据表:<br>$rawhtml",$kbmb);
}
}

if(!stristr($rawname,"-{$rewname}-")){
 echo str_replace("<!--txt-->","请先选择数据库和表格",$kbmb);
}else{

 $ret = $db->query("SELECT * from {$rewname}");
 echo "<table cellspacing=\"0\" cellpadding=\"0\">\r\n";
 $ia = 0;
 //echo "<caption><b>{$rewname}</b>({$dbsname})</caption>\r\n";
 while($row = $ret->fetchArray(SQLITE3_ASSOC)){
 $ia++;
 if($ia=="1"){
 echo "<tr class=\"tt\">";
 foreach ($row as $val=>$vals){
 echo "<td><nobr>$val</nobr></td>";
 }
 echo "</tr>";
 }
 echo "<tr>";
 foreach ($row as $val=>$vals){
 echo "<td>$vals</td>";
 }
 echo "</tr>";
 }
 $db->close();
 if($ia<1){
 echo "<tr><td>暂无数据</td></tr>";
 }else{
 echo $domas;
 }
 echo "</table>";
}
 echo "</div>";
?></body>
</html>