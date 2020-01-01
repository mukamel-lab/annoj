<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session2.php';
?>
<head>
<title>jBuilder Tool</title>
</head>
<body style="margin:0;overflow:hidden;">
<div id="x_list" style="float:left;text-align:right;display:inline;margin:0 auto;width:130px;background-color:#DFF;height:99%">
<font style="font-family: Verdana, sans-serif, Arial; font-size: 0.8em; color: #408040;">
<P>&nbsp;<u><b><nobr>jBuilder <?PHP echo trim(file_get_contents('.version')); ?>:</nobr></b></u>&nbsp;&nbsp;<br><br>
<font style="font-size:1.0em;font-weight:bold;">
<P> &nbsp; <a href='settings.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';">Settings</a>&nbsp;&nbsp;&nbsp;&nbsp;<br><br>
<P> &nbsp; <a href='simple_build.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';">jBuilder</a>&nbsp;&nbsp;&nbsp;&nbsp;<br><br>
<?PHP if ($_SESSION['aj_group']=='admin') { ?>
<P> &nbsp; <a href='admins.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';">jAdmins</a>&nbsp;&nbsp;&nbsp;&nbsp;<br><br>
<?PHP } ?>
</font>
<font style="font-size: 0.8em;">
<?PHP if ($_SESSION['aj_group']=='admin') { ?>
<P> &nbsp; <a href='build_genome.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Build Genomes</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<P> &nbsp; <a href='build_model.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Build Models</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<?PHP } ?>
<P> &nbsp; <a href='build_cors.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Build CORS</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<P> &nbsp; <a href='add_table_index.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Add Indexes</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<P> &nbsp; <a href='preload_cache.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Preload Caches</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<P> &nbsp; <a href='remove_cache.php' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';"><nobr>Delete Caches</nobr></a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
<br><br>
<P><nobr>User: <?PHP echo $aj_user; ?>&nbsp;&nbsp;&nbsp;&nbsp;</nobr><br>
<nobr>Group: <?PHP if (strpos($_SESSION['aj_group'],',')==false) echo $_SESSION['aj_group']; else echo substr($_SESSION['aj_group'],0,strpos($_SESSION['aj_group'],',')); ?>&nbsp;&nbsp;&nbsp;&nbsp;</nobr><br>
<font style="font-size: 1.25em;font-weight:bold;">
<P>&nbsp; &nbsp;&nbsp;  <a href='?Logout=Logout' target="x_display_right" onMouseOver="style.color='B03040';" onMouseOut="style.color='';">Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;<br>
</font></font>
</div>
<div id="x_tools" style="display:inline;margin:0 auto;">
<iframe id="x_display_right" name="x_display_right" style="height:98%;align:center;background-color:#FFD;" frameBorder="0"></iframe>
</div>
<script language=javascript>
	document['body'].style.overflow = 'hidden';
	parent.document.getElementById('x_display_right').style.width = (document['body'].offsetWidth-132) +'px';

window.onresize = function()
{
	document['body'].style.overflow = 'hidden';
	parent.document.getElementById('x_display_right').style.width = (document['body'].offsetWidth-132) +'px';
}
</script>
</body>
