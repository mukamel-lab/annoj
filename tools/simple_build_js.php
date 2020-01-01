<?PHP
ini_set('display_errors', 'On');
require_once 'util_module.php';
require_once 'login_session.php';
?>
/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
	// shorthand
	var Tree = Ext.tree;

	var tree = new Tree.TreePanel({
		id: 'TreePanel',
		useArrows: true,
		autoScroll: true,
		animate: true,
		enableDD: true,
		containerScroll: true,
		border: false,
		//height: 480,
		// auto create TreeLoader
		dataUrl: 'simple_build_db.php',

		root: {
			nodeType: 'async',
			text: 'Tracks',
			draggable: false,
			id: 'Track',
			iconCls: 'silk_house'
		},
	});

	var treeEditor = new Tree.TreeEditor(tree, {}, {
		cancelOnEsc: true,
		completeOnEnter: true,
		selectOnFocus: true,
		allBlank: false,
	});

	// render the tree
	tree.render('tree-div');
	tree.getRootNode().expand();

	var source = new Tree.TreePanel({
		id: 'SourcePanel',
		useArrows: true,
		autoScroll: true,
		animate: true,
		enableDD: true,
		containerScroll: true,
		border: false,
		//height: 480,
		// auto create TreeLoader
		dataUrl: 'simple_build_db.php',

		root: {
			nodeType: 'async',
			text: 'Source',
			draggable: false,
			id: 'Source',
			iconCls: 'silk_world'
		}
	});

	// render the source
	source.render('source-div');
	source.getRootNode().expand();
});

function expandChildFolder(node)
{
	if (!node.hasChildNodes()) return;
	else node.eachChild(function(mynode) { mynode.expand(true); expandChildFolder(mynode); });
}

function collapsChildFolder(node)
{
	node.eachChild(function(mynode) { mynode.collapse(true); });
}

function expands()
{
	expands.count = ++expands.count || 1;
	if (expands.count%2==1)
	{
		expandChildFolder(Ext.getCmp('TreePanel').getRootNode());
	}
	else 
	{
		collapsChildFolder(Ext.getCmp('TreePanel').getRootNode());
	}
}

function checkNodeData(node)
{
	var attr = node.attributes;
	if (attr.Data_Type == "SuffixBoth" || attr.Data_Type == "SuffixPage" || attr.Data_Type == "SuffixTrack")
	{
		if (attr.Genome_URL==null || attr.Genome_URL=='') return "Genome_URL";
		if (attr.Organism==null || attr.Organism=='') return "Organism";
		if ((attr.Author==null || attr.Author=='') && (attr.Provider_Name==null || attr.Provider_Name=='')) return "Author";
		if ((attr.Email==null || attr.Email=='') && (attr.Provider_Mail==null || attr.Provider_Mail=='')) return "Email";
		if (attr.Page_Title==null || attr.Page_Title=='') return "Page_Title";
	}

	if (attr.Data_Type == "PageTrack" || attr.Data_Type == "SimpleData" || attr.Data_Type == "SimpleTrack" || attr.Data_Type == "MySQLData" || attr.Data_Type == "MySQLTrack")
	{
		if (attr.Track_Type==null || attr.Track_Type=='') return "Track_Type";
		if (attr.Track_URL==null || attr.Track_URL=='') return "Track_URL";
		if (attr.Icon==null || attr.Icon=='') return "Track_Icon";
	}
	if (attr.Data_Type == "SimpleData" || attr.Data_Type == "SimpleTrack" || attr.Data_Type == "MySQLData" || attr.Data_Type == "MySQLTrack")
	{
		if (attr.Include==null || attr.Include=='') return "Include";
		if (attr.Table_List==null || attr.Table_List=='') return "Table";
		if (attr.Title_List==null || attr.Title_List=='') return "Title";
		if (attr.Info_List==null || attr.Info_Lis=='') return "Info";
	}
	if (attr.Data_Type == "MySQLData" || attr.Data_Type == "MySQLTrack")
	{
		if (attr.Server==null || attr.Server=='') return "Server";
	}

	return '';
}

function getNodeData(node)
{
	var txt = '';
	var attr = node.attributes;
	txt += "Data_Type =:= "+attr.Data_Type+" ,,, Node_Name =:= "+node.text+" ,,, Node_Path =:= "+node.path;
	if (attr.Data_Type == "SuffixBoth" || attr.Data_Type == "SuffixPage")
	{
		if (attr.Genome_URL==null || attr.Genome_URL=='')
			attr.error = "Genome_URL";
		else txt += " ,,, Genome_URL =:= "+attr.Genome_URL;
		if (attr.Bookmarks_URL==null || attr.Bookmarks_URL=='') ;
		else txt += " ,,, Bookmarks_URL =:= "+attr.Bookmarks_URL;
		if (attr.Analysis_URL==null || attr.Analysis_URL=='') ;
		else txt += " ,,, Analysis_URL =:= "+attr.Analysis_URL;
		txt += " ,,, Author =:= "+ (attr.Author);
		txt += " ,,, Email =:= "+ (attr.Email);
		txt += " ,,, Page_Title =:= "+ (attr.Page_Title);
		txt += " ,,, Notes =:= "+ (attr.Notes);
		txt += " ,,, Citation =:= "+ (attr.Citation);
		txt += " ,,, Assembly =:= "+ (attr.Assembly);
		txt += " ,,, Position =:= "+ (attr.Position);
		txt += " ,,, Bases =:= "+ (attr.Bases);
		txt += " ,,, Pixels =:= "+ (attr.Pixels);
		txt += " ,,, Yaxis =:= "+ (attr.Yaxis);
		txt += " ,,, Max =:= "+ (attr.Max);
		txt += " ,,, Min =:= "+ (attr.Min);
		txt += " ,,, Multi =:= "+ (attr.Multi);
		txt += " ,,, Baseline =:= "+ (attr.Baseline);
		txt += " ,,, Display =:= "+ (attr.Display);
		txt += " ,,, Activate =:= "+ (attr.Activate);
		txt += " ,,, GlobalScale =:= "+ (attr.GlobalScale);
		txt += " ,,, HiC_d =:= "+ (attr.HiC_d);
		txt += " ,,, Priority =:= "+ (attr.Priority);
		if (attr.AllowCORS != null && attr.AllowCORS != '')
			txt += " ,,, AllowCORS =:= "+ (attr.AllowCORS);
		if (attr.DynamicJS != null && attr.DynamicJS != '') 
			txt += " ,,, DynamicJS =:= "+ (attr.DynamicJS);
		if (attr.FinalVersion != null && attr.FinalVersion != '')
			txt += " ,,, FinalVersion =:= "+ (attr.FinalVersion);
	}
	if (attr.Data_Type == "SuffixBoth" || attr.Data_Type == "SuffixTrack")
	{
		txt += " ,,, Provider_Name =:= "+ (attr.Provider_Name);
		txt += " ,,, Provider_Mail =:= "+ (attr.Provider_Mail);
		txt += " ,,, Institution =:= "+ (attr.Institution);
		txt += " ,,, Institution_Link =:= "+ (attr.Institution_Link);
		txt += " ,,, Institution_Logo =:= "+ (attr.Institution_Logo);
		txt += " ,,, Organism =:= "+ (attr.Organism);
	}
	if (attr.Data_Type == "ActiveTrack")
	{
		txt += " ,,, Track_URL =:= "+ (attr.Track_URL);
	}
	if (attr.Data_Type == "PageTrack" || attr.Data_Type == "SimpleData" || attr.Data_Type == "SimpleTrack" || attr.Data_Type == "MySQLData" || attr.Data_Type == "MySQLTrack")
	{
		txt += " ,,, Track_Name =:= "+ attr.Track_Name;
		txt += " ,,, Track_Type =:= "+ attr.Track_Type;
		txt += " ,,, Track_Path =:= "+ attr.Track_Path;
		txt += " ,,, Track_URL =:= "+ attr.Track_URL;
		txt += " ,,, Icon =:= "+ attr.Icon;
		txt += " ,,, Height =:= "+ attr.Height;
		txt += " ,,, boxHeight =:= "+ attr.boxHeight;
		txt += " ,,, boxWidthMin =:= "+ attr.boxWidthMin;
		txt += " ,,, Scale =:= "+ attr.Scale;
		txt += " ,,, Single =:= "+ attr.Single;
		txt += " ,,, Control =:= "+ attr.Control;
		txt += " ,,, alignControl =:= "+ attr.alignControl;
		txt += " ,,, showWalks =:= "+ attr.showWalks;
		txt += " ,,, NoArrows =:= "+ attr.NoArrows;
		txt += " ,,, NoLabels =:= "+ attr.NoLabels;
		txt += " ,,, Reads =:= "+ attr.Reads;
		txt += " ,,, Class =:= "+ attr.Class;
		txt += " ,,, Policy =:= "+ attr.Policy;
		txt += " ,,, Color =:= "+ attr.Color;
		txt += " ,,, Include =:= "+ attr.Include; 
		txt += " ,,, Path =:= "+ attr.Path; 
		txt += " ,,, Table_List =:= "+ attr.Table_List;
		txt += " ,,, Title_List =:= "+ attr.Title_List;
		txt += " ,,, Info_List =:= "+ attr.Info_List;
		if (attr.Server != null && attr.Server != '')
			txt += " ,,, Server =:= "+ (attr.Server);
		if (attr.Options_List != null && attr.Options_List != '')
			txt += " ,,, Options_List =:= "+ attr.Options_List;
		if (attr.Append_Assembly != null)
			txt += " ,,, Append_Assembly =:= "+ (attr.Append_Assembly);
		if (attr.iBase != null && attr.iBase != '')
			txt += " ,,, iBase =:= "+ (attr.iBase);	
		txt += " ,,, Force =:= "+ attr.Force;
	}

	return txt; 
}

function getEmptyNodes(node)
{
	var allNodes = new Array();
	if (node.isLeaf()) return null;
	else if (!node.hasChildNodes() || !node.expanded) return node;
	else
	{
		node.eachChild(function(mynode)
		{
			if (getEmptyNodes(mynode)!=null)
				allNodes = allNodes.concat(getEmptyNodes(mynode));
		});
	}
	return allNodes;
}

function getAllChildNodes(node)
{
	var allNodes = new Array();
	if (node.isLeaf())
	{
		return node;
	}
	else
	{
		node.eachChild(function(mynode)
		{
			if (node.path=='') mynode.path = mynode.text.replace(/ /gi, "_");
			else mynode.path = node.path+'/'+mynode.text;
			allNodes = allNodes.concat(getAllChildNodes(mynode));
		});
	}
	return allNodes;
}

function run_order()
{
	var root = Ext.getCmp('TreePanel').getRootNode();
	root.path = '';
	var allChildNodes = getAllChildNodes(root);
	var txt = ''; 
	for (var i=0; i<allChildNodes.length; i++)
	{
		if (i>0) txt += " ;;; ";
		txt += getNodeData(allChildNodes[i]);
	}

	if (txt.length < 65536)
		document.getElementById('x_All_Track').value = txt;
	else if (txt.length > 650000*2)
	{
		alert("Error: Input size > 1.3M (" + txt.length + ").");
		return false;
	}
	else
	{
		var txtz = new Array (40); 
		var j=0; var k = 0; 
		for (var m=0; m<40; m++) txtz[m] = '';
		for (m=0; m<40; m++)
		{
			j = 65000*m; k = 65000*(m+1);
			txtz[m] = txt.substring(j, k);
			if (txt.length < k) break;
		}
		document.getElementById('x_All_Track').value = txtz[0];
		document.getElementById('x_All_Track1').value = txtz[1];
		document.getElementById('x_All_Track2').value = txtz[2];
		document.getElementById('x_All_Track3').value = txtz[3];
		document.getElementById('x_All_Track4').value = txtz[4];
		document.getElementById('x_All_Track5').value = txtz[5];
		document.getElementById('x_All_Track6').value = txtz[6];
		document.getElementById('x_All_Track7').value = txtz[7];
		document.getElementById('x_All_Track8').value = txtz[8];
		document.getElementById('x_All_Track9').value = txtz[9];

		document.getElementById('x_All_Track10').value = txtz[10];
		document.getElementById('x_All_Track11').value = txtz[11];
		document.getElementById('x_All_Track12').value = txtz[12];
		document.getElementById('x_All_Track13').value = txtz[13];
		document.getElementById('x_All_Track14').value = txtz[14];
		document.getElementById('x_All_Track15').value = txtz[15];
		document.getElementById('x_All_Track16').value = txtz[16];
		document.getElementById('x_All_Track17').value = txtz[17];
		document.getElementById('x_All_Track18').value = txtz[18];
		document.getElementById('x_All_Track19').value = txtz[19];

		document.getElementById('x_All_Track20').value = txtz[20];
		document.getElementById('x_All_Track21').value = txtz[21];
		document.getElementById('x_All_Track22').value = txtz[22];
		document.getElementById('x_All_Track23').value = txtz[23];
		document.getElementById('x_All_Track24').value = txtz[24];
		document.getElementById('x_All_Track25').value = txtz[25];
		document.getElementById('x_All_Track26').value = txtz[26];
		document.getElementById('x_All_Track27').value = txtz[27];
		document.getElementById('x_All_Track28').value = txtz[28];
		document.getElementById('x_All_Track29').value = txtz[29];

		document.getElementById('x_All_Track30').value = txtz[30];
		document.getElementById('x_All_Track31').value = txtz[31];
		document.getElementById('x_All_Track32').value = txtz[32];
		document.getElementById('x_All_Track33').value = txtz[33];
		document.getElementById('x_All_Track34').value = txtz[34];
		document.getElementById('x_All_Track35').value = txtz[35];
		document.getElementById('x_All_Track36').value = txtz[36];
		document.getElementById('x_All_Track37').value = txtz[37];
		document.getElementById('x_All_Track38').value = txtz[38];
		document.getElementById('x_All_Track39').value = txtz[39];
	}
	return true;
}

function pass_test()
{
	var tree = Ext.getCmp('TreePanel');
	var root = tree.getRootNode();
	//expandChildFolder(root);
	var empyFolders = getEmptyNodes(root);
	if (empyFolders.length>0)
	{
		var labelText = '';
		if (empyFolders.length==1)
			labelText = "Warning: There is an empty folder. Please expand or remove it, and then submit again!";
		else labelText = "Warning: There are "+empyFolders.length+" empty folders. Please expand or remove them, and then submit again!";
		alert(labelText);
		expandChildFolder(root);
		return false;
	}
	var allChildNodes = getAllChildNodes(root); 
	var txt = ''; var k = 0; var tp;
	for (var i=0; i<allChildNodes.length; i++)      //>
	{
		txt = checkNodeData(allChildNodes[i]);
		if (txt!='')
		{
			var wn = "Warning : "+txt+" is empty in node "+allChildNodes[i].text;
			alert(wn);
			allChildNodes[i].select();
			return false;
		}
		tp = allChildNodes[i].attributes['Data_Type'];	
		if (tp == 'PageTrack' || tp == 'SimpleData' || tp == 'MySQLData' || tp == 'SimpleTrack' || tp == 'MySQLTrack') k++; 
	}
	if (k==0) return false;
	return true;
}

function runv()
{
	if (pass_test())
	{
		var r = confirm("Page is tested & passed. Do you want to save?");
		if (r==true) return run_order();
	}
	return false;
}

function getNodeByText(node, text)
{
	var an = new Array;
	if (node.text==text) return node; 
	if (!node.hasChildNodes()) return;
	else
	{
		node.eachChild(function(mynode)
		{
			an = an.concat(getNodeByText(mynode, text));
		});
	}
	return an;
}

function trashNode (rmv )
{
	if (rmv=='Remove')
	{
		trashNode.node=null;
		return false;
	}
	if (trashNode.node==null)
	{
		var root = Ext.getCmp('TreePanel').getRootNode();
		trashNode.node = root.findChild("text", "Trash", true);
		if (trashNode.node==null)
		{
			root.appendChild ({id : "x_Trash" , text : "Trash", cls : "folder", iconCls : "silk_bin",  "Data_Type" : "TrashFolder"});
			return false;
		}
		else trashNode.node.expand();
	}
	if (trashNode.node!=null) 
	{
		var node = trashNode.node;
		if (!node.hasChildNodes()) return false;
		var txt = ''; var k=0;
		node.eachChild(function(mynode) 
		{ 
			if (k!=0) txt += " ,,, ";
			txt += mynode.id;  k++;
		});
		if (txt!='')
		{
			if (k>1) var lb = "Trash bin has "+k+" items. Are you sure that you want to remove them?";
			else var lb = "Trash bin has "+k+" item. Are you sure that you want to remove it?";
			var r = confirm(lb);
			if (r==true) 
			{
				document.getElementById('x_Trash_Bin').value = txt;
				return true;
			}
		}
	}	
	return false;
}

function rollbackPage()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null)
	{
		alert("Warning: No node selected. Please select a node first.");
		return false;
	}
	else
	{
		var r = confirm("Do you want to roll back old page?");
		if (r==true)
		{
			document.getElementById('x_Rollback_Page').value = node.text;
			return true;
		}
	}
	return false;
}

function activeNode(pick)
{
	if (pick=='Remove')
	{
		activeNode.node=null;
		return false;
	}
	if (activeNode.node==null)
	{
		var root = Ext.getCmp('TreePanel').getRootNode();
		activeNode.node = root.findChild("text", "Active", true);
	}

	if (activeNode.node==null)
	{
		var page = root.firstChild;
		if (page==null)
		{
			alert("Warning: No page exists!"); 
			return false;
		}
		var idx = "activeFolder";
		page.appendChild ({id : idx, text : "Active", cls : "folder", "Data_Type" : "ActiveFolder"});
		activeNode.node = page.lastChild;
		page.insertBefore(activeNode.node, page.firstChild);
		activeNode.node.expand();
	}

	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null)
	{
		alert("Warning: No node selected. Please select a node first.");
		return false;
	}
	else 
	{
		if (activeNode.node.contains(tree.getNodeById(node.id)))
			alert("Warning: Node '"+node.text+"' has already been picked upto ActiveFolder.");
		else if (activeNode.node.findChild("text", node.text, true)!=null)
			alert("Warning: Node '"+node.text+"' has already been picked upto ActiveFolder.");
		else
		{
			var ak = "active_"+node.id;
			activeNode.node.appendChild({id:ak,text:node.text,cls:"file",iconCls:node.attributes.iconCls,"leaf":true,"Track_URL":node.attributes.Track_URL,"Data_Type":"ActiveTrack"});
		}
		nextNode = node.nextSibling;				//Aug. 1 H. C.
		if (nextNode!=null) nextNode.select();			//Aug. 1 H. C.

	}
}

function moveNode ()
{
	var dtree = Ext.getCmp('TreePanel');
	var dnode = dtree.getSelectionModel().getSelectedNode();
	var stree = Ext.getCmp('SourcePanel');
	var snode = stree.getSelectionModel().getSelectedNode();

	if (snode==null)
	{
		alert("Error: No source node selected. Please select a node first.");
	}
	else if (dnode==null)
	{
		alert("Error: No target folder selected. Please select a folder first.");
	}
	else if (dnode.isLeaf())
	{
		alert("Error: Selected target is not a folder.");
	}
	else
	{
		dnode.expand();
		nextNode = snode.nextSibling;
		if (nextNode!=null) nextNode.select();
		dnode.appendChild(snode);
	}
}

function editNode ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) 
	{
		alert("Warning: No node selected. Please select a node first.");
		return false;
	}

	var attr = node.attributes;
	text = openEditNode();
	document.getElementById('x_div2').innerHTML = text;
	document.getElementById('x_div').style.display = "";
	attr2id();
	return false;
}

function closeDiv ()
{
	document.getElementById('x_div').style.display = "none";
}

function closeDivEC ()
{
	document.getElementById('x_div_ec').style.display = "none";
}

function editCitation ()
{
	var htmlCitation = openEditCitation();
	document.getElementById('x_div_ec2').innerHTML = htmlCitation;
	document.getElementById('x_div_ec').style.display = "";
	return false;
}

function sortText (a, b)
{
	return ( ( a.text == b.text ) ? 0 : ( ( a.text > b.text ) ? 1 : -1 ) );
}

function rsortText (a, b)
{
	return sortText(b, a);
}

function sortNode ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();

	if (node==null)
		alert("Warning: No node selected. Please select a node first.");
	else
	{
		node.expand(); 
		if (node.hasChildNodes())
		{
			if (node==sortNode.node && sortNode.mo!=null && sortNode.mo==1)
			{
				sortNode.mo = 2;
				node.sort(rsortText);
			}
			else
			{
				sortNode.mo = 1;
				node.sort(sortText);
			}
		}
		else alert("Warning: No folder node selected. Please select a folder first.");
	}
	sortNode.node = node;
}


function removeNode ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null)
		alert("Warning: No node selected. Please select a node first.");
	else if (node==tree.getRootNode())
		alert("Warning: Root node can not be removed.");
	else
	{
		nextNode = node.nextSibling;				//Aug. 1 H.C.
		if (nextNode!=null) nextNode.select();			//Aug. 1 H.C.

		if (node.attributes['Data_Type']=='ActiveFolder') activeNode('Remove');
		if (node.attributes['Data_Type']=='TrashFolder') trashNode('Remove');
		node.remove();
	}
}

function addNode ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) node = tree.getRootNode(); 
	if (!node.isLeaf())
	{
		addNode.no = ++addNode.no || 1;
		var idx = "new_folder_id_"+addNode.no;
		node.expand();
		if (node==tree.getRootNode() && !node.hasChildNodes())
		{
			//node.appendChild({id : idx, text : "NewPage", cls : "folder", iconCls : "silk_page_red", "Data_Type" : "PageBase" });
			var newNode =  new Ext.tree.TreeNode({id : idx, text : "NewPage", cls : "folder", iconCls : "silk_page_red", "Data_Type" : "PageBase" });
			node.appendChild(newNode);
			addNode.no = ++addNode.no;
			idx = "new_folder_id_"+addNode.no;
			newNode.appendChild({id : idx, text : "Annotation Models", cls : "folder" });
		}
		else
			node.appendChild({id : idx, text : "New Folder", cls : "folder" });
	}	
}

function doneEditNode()
{
	id2attr();
	pass2tree(true);
	closeDiv();
	return false;
}

function doneEditCitation()
{
	var logo = document.getElementById("x_Citation_Logo").value;
	var url = document.getElementById("x_Citation_URL").value;
	var name = document.getElementById("x_Citation_Name").value;
	var title = document.getElementById("x_Citation_Title").value;
	var journal = document.getElementById("x_Citation_Journal").value;
	var pages = document.getElementById("x_Citation_Pages").value;
	var email = document.getElementById("x_Citation_Email").value;
	var citation = name + ' <b>' + title + '</b> <i> '+ journal + '</i> ' + pages; 
	var html = "<br><div>";

	if (logo != '')
	{
		if (url != '')  html += '<a href="' + url + '" target=_blank>';
		if (logo.indexOf('.gif')>0 || logo.indexOf('.png')>0 || logo.indexOf('.jpg')>0)
			html += '<img src="' + logo + '">';
		else html += '<h3>' + logo + '</h3>';
		if (url != '')  html += '</a>';
		html += '<br>';
	}
	else if (url != '')
	{
		html += '<a href="' + url + '" target=_blank><h3>' + title + '</h3></a><br>';
	}
	else html += '<h3>' + title + '</h3><br>';
	html += '</div>' + '<table style="font-size:10px;cellpadding:3px;">' + '<tr><td valign=top><b>Author: </b></td><td>' + name + '</td></tr>' + '<tr><td valign=top><b>Title: </b></td><td>' + title + '</td></tr>' + '<tr><td valign=top><b>Journal: </b></td><td><i>' + journal + '</i> ' + pages + '</td></tr>' + '<tr><td><b>Contact: </b></td><td><a href=mailto:' + email + ' target=_blank>' + email + '</a></td></tr>' +'<tr><td valign=top><b>Citation: </b></td><td>' + citation + '</td></tr>' + '</table><br>';
	document.getElementById("x_Citation").value = html;
	closeDivEC();
	return false;
}

function getDataTypeNo (attr)
{
	if (attr.Data_Type==null || attr.Data_Type=='PageBase' || attr.Data_Type=='TrackBase') return 0;
	if (attr.Data_Type=='ActiveFolder' || attr.Data_Type=='ActiveTrack') return 0;
	if (attr.Data_Type=='SuffixBoth') return 12;
	if (attr.Data_Type=='SuffixTrack') return 10;
	if (attr.Data_Type=='SuffixPage') return 2;
	if (attr.Data_Type=='SimpleData' || attr.Data_Type=='MySQLData' || attr.Data_Type=='SimpleTrack' || attr.Data_Type=='MySQLTrack') return 56;
	if (attr.Data_Type=='PageData' || attr.Data_Type=='PageTrack') return 6;
	if (attr.Data_Type=='SimpleFolder' || attr.Data_Type=='MySQLFolder' || attr.Data_Type=='TrackFolder') return 34;
	return 4;
}

function openEditCitation()
{
	var html = '';
	html += '<form method=POST><br>';
	html += '<input type="SUBMIT" NAME="Submit" onClick="return false;" style="display:none">';
	html += '<span class="label"> &nbsp; </span><span class="field1"><b>Citation:</b></span>';
	html += '<hr width=95%>';
	html += '<span class="label">Logo: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Logo" NAME="Citation_Logo" value=""></span><br>';
	html += '<span class="label">URL: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_URL" NAME="Citation_URL" value=""></span><br>';
	html += '<span class="label">Author: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Name" NAME="Citation_Name" value=""></span><br>';
	html += '<span class="label">Title: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Title" NAME="Citation_Title" value=""></span><br>';
	html += '<span class="label">Journal: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Journal" NAME="Citation_Journal" value=""></span><br>';
	html += '<span class="label">Vol/Pages: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Pages" NAME="Citation_Pages" value=""></span><br>';
	html += '<span class="label">Email: &nbsp; </span><span class="field"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Citation_Email" NAME="Citation_Email" value=""></span><br>';
	html += '</form>';
	html += '<br>';
	html += '<span class="label" style="text-align:left;width:180px;height:25px;"> &nbsp; &nbsp; <span style="cursor:pointer;" onClick="javascript:closeDivEC();">Cancel</span></span><span class="label" style="text-align:right;width:180px;"><span style="cursor:pointer;" onClick="javascript:doneEditCitation();">Done</span> &nbsp; </span><br>';
	return html;
}

function openEditNode()
{
	var html = '';
	var bt=-3;

	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) return alert("Warning: No node is selected!");
	var attr = node.attributes;
	bt = getDataTypeNo (attr);

	html += '<form method=POST><br>';
	html += '<input type="SUBMIT" NAME="Submit" onClick="return false;" style="display:none">';
	html += '<span class="label">Node: &nbsp; </span><span class="field1"><b>'+node.text+'</b></span>';
	html += '<span class="field" style="width:100px;"> &nbsp; <select class="CEL" style="width:90px;" id="x_Data_Type" NAME="Data_Type"><option value="">Data_Type: <option value="PageBase">PageBase<option value="SimpleFolder">SimpleFolder<option value="MySQLFolder">MySQLFolder<option value="PageFolder">PageFolder<option value="ActiveFolder">ActiveFolder<option value="SimpleTrack">SimpleTrack<option value="MySQLTrack">MySQLTrack<option value="PageTrack">PageTrack<option value="ActiveTrack">ActiveTrack<option value="SimpleData">SimpleData<option value="MySQLData">MySQLData<option value="SuffixBoth">SuffixBoth</select></span>';
	html += '<hr width=97%>'; 
	if (Math.floor(bt/10)==1)
	{
		html += '<span class="label">Author: &nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Provider_Name" NAME="Provider_Name" value=""></span>';
		html += '<span class="label2">Email:&nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Provider_Mail" NAME="Provider_Mail" value=""></span><br>';
		html += '<span class="label">Institution: &nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Institution" NAME="Institution" value=""></span>';
		html += '<span class="label2">Inst url: &nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Institution_Link" NAME="Institution_Link" value=""></span><br>';
		html += '<span class="label">Inst Logo: &nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Institution_Logo" NAME="Institution_Logo" value=""></span><br>';
		html += '<span class="label">Organism: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Organism" NAME="Organism" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("organisms"); ?></span><br>';
	}
	if (Math.floor(bt/10)>1)
	{
		html += '<span class="label">Include: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Include" NAME="Include" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("includes"); ?></span><br>';
		if (attr.Data_Type!='SimpleFolder' && attr.Data_Type!='SimpleTrack' && attr.Data_Type!='SimpleData') 
			html += '<span class="label">Server: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Server" NAME="Server" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("servers"); ?></span><br>';
		//html += '<span class="label">Path: &nbsp; </span><span class="field"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Path" NAME="Path" value=""></span><br>';
	}
	if (Math.floor(bt/10)==5)
	{	
		html += '<span class="label">Table: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Table_List" NAME="Table_List" value=""></span><br>';
		html += '<span class="label">Title: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Title_List" NAME="Title_List" value=""></span><br>';
		html += '<span class="label">Info: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Info_List" NAME="Info_List" value=""></span><br>';
	}
	if ((Math.floor(bt/10)==3 || Math.floor(bt/10)==5) && (attr.Data_Type=='MySQLFolder' || attr.Data_Type=='MySQLTrack' || attr.Data_Type=='MySQLData'))
		html += '<span class="label">Customs: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Options_List" NAME="Options_List" value=""></span><br>';
	if (Math.floor(bt/10)==5 || bt%10==6)
	{
		html += '<span class="label">Track url: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Track_URL" NAME="Track_URL" value=""></span><br>';
		html += '<span class="label">Track name: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Track_Name" NAME="Track_Name" value=""></span><br>';
	}
	if (Math.floor(bt/10)==1 || bt%10==2)
	{
		html += '<span class="label">Genome: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Genome_URL" NAME="Genome_URL" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("genomes"); ?></span><br>';
		html += '<span class="label">Bookmarks:&nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Bookmarks_URL" NAME="Bookmarks_URL" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("bookmarks"); ?></span><br>';
		html += '<span class="label">Analysis:&nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Analysis_URL" NAME="Analysis_URL" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("analyses"); ?></span><br>';
	}

	if (bt>12)
	{
		if (bt%10!=4 || attr.Data_Type=='MySQLFolder' || attr.Data_Type=='MySQLTrack' || attr.Data_Type=='MySQLData') 
		{
			html += '<nobr><span class="label">Setting: &nbsp; </span>';
			if (attr.Data_Type=='MySQLFolder' || attr.Data_Type=='MySQLTrack' || attr.Data_Type=='MySQLData') 
			{
				html += '<span class="label" style="height:25px;text-align:left;width:128px;"> <input TYPE="checkbox" id="x_Append_Assembly" NAME="Append_Assembly" value=true > Append assembly</span>';
				html += '<span class="label" style="height:25px;text-align:left;width:84px;"> <input TYPE="checkbox" id="x_iBase" NAME="iBase" value=true > Index zero</span>';
			}
			if (bt%10!=4) html += '<span class="label" style="height:25px;text-align:left;width:80px;"> <input TYPE="checkbox" id="x_Force" NAME="Force" value=true > Force to overwrite</span>';
			html += '</nobr><br>';
		}
	}
	if (bt%10==2)
	{
		html += '<span style="height:5px;"></span><br>';
		html += '<span class="label">Curator: &nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Author" NAME="Author" value=""></span>';
		html += '<span class="label2">Email:&nbsp; </span><span class="field"><input class="CEL" style="width:150px;" TYPE="TEXT" id="x_Email" NAME="Email" value=""></span><br>';

		html += '<span class="label">Page title: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Page_Title" NAME="Page_Title" value=""></span><br>';
		html += '<span class="label">Notes: &nbsp; </span><span class="field2"><input class="CEL" style="width:360px;" TYPE="TEXT" id="x_Notes" NAME="Notes" value=""></span><br>';
		html += '<nobr><span class="label">Citation: &nbsp; </span><span class="field2"><input class="CEL" style="width:340px;" TYPE="TEXT" id="x_Citation" NAME="Citation" value=""> <span style="vertical-align:bottom" onClick="javascript:editCitation();return false;"><img style="vertical-align:bottom" src="../img/silk/icons/page_edit.png"></span></span></nobr><br>';
		html += '<nobr><span class="label">Locations: &nbsp; </span>';
		html += '<span class="field" style="width:61px;height:24px;"> Chr: <input class="CEL" style="width:27px;" TYPE="TEXT" id="x_Assembly" NAME="Assembly" value=""></span>';
		html += '<span class="field" style="width:118px;">Pos: <input class="CEL" style="width:85px;" TYPE="number" id="x_Position" NAME="Position" value=""></span>';
		html += '<span class="field" style="width:74px;">Bases: <input class="CEL" style="width:29px;" TYPE="number" id="x_Bases" NAME="Bases" value=""></span>';
		html += '<span class="field" style="width:79px;">Pixels: <input class="CEL" style="width:29px;" TYPE="number" id="x_Pixels" NAME="Pixels" value=""></span></nobr>';
		html += '<br>'; 
	}

	if (bt%10==4 || bt%10==6)
	{
		html += '<span class="label">Track type: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Track_Type" NAME="Track_Type" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("track_types"); ?></span><br>';
		html += '<span class="label" style="height:25px;">Track icon: &nbsp; </span><span class="field1"><input class="CEL" style="width:255px;" TYPE="TEXT" id="x_Icon" NAME="Icon" value=""></span><span class="field1" style="width:100px;"> &nbsp; <?PHP echo read_genomes_x_info_string("salk_icons"); ?></span><br>';

		html += '<nobr><span class="label">Options: &nbsp; </span>';
		html += '<span class="field" style="width:80px;">Height: <input class="CEL" style="width:30px;" TYPE="number" id="x_Height" NAME="Height" value=""></span>';
		html += '<span class="field" style="width:73px;">Scale: <input class="CEL" style="width:30px;" TYPE="number" id="x_Scale" NAME="Scale" value=""></span>';
		html += '<span class="field" style="width:98px;">boxHeight: <input class="CEL" style="width:30px;" TYPE="number" id="x_boxHeight" NAME="boxHeight" value=""></span>';
		html += '<span class="field" style="width:98px;">boxWidthMin: <input class="CEL" style="width:30px;" TYPE="number" id="x_boxWidthMin" NAME="boxWidthMin" value=""></span></nobr><br>';
		html += '<nobr><span class="label">   &nbsp; </span>';
		html += '<span class="field" style="width:96px;">alignCtrl: <input class="CEL" style="width:30px;" TYPE="number" id="x_alignControl" NAME="alignControl" value=""></span>';
		html += '<span class="field" style="width:55px;"><input class="CEL" style="width:9px;" TYPE="checkbox" id="x_Single" NAME="Single" value=true> Single</span>';
		html += '<span class="field" style="width:76px;"><input class="CEL" style="width:9px;" TYPE="checkbox" id="x_Arrows" NAME="NoArrows" value=false> No Arrows</span>';
		html += '<span class="field" style="width:73px;"><input class="CEL" style="width:9px;" TYPE="checkbox" id="x_Labels" NAME="NoLabels" value=false> No Labels</span>';
		html += '<span class="field" style="width:72px;"><input class="CEL" style="width:9px;" TYPE="checkbox" id="x_Control" NAME="Control" value=true> showCtrl</span></nobr><br>';
		html += '<nobr><span class="label">   &nbsp; </span>';
		html += '<span class="field" style="width:72px;"><input class="CEL" style="width:9px;" TYPE="checkbox" id="x_showWalks" NAME="showWalks" value=true> showWalks</span></nobr><br>';
		html += '<nobr><span class="label">  Color:&nbsp; </span>';
		html += '<span class="field" style="width:355px;"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Color" NAME="Color" value=""> &nbsp;<input type="color" style="width:18;height:18;" onChange="var v=document.getElementById(\'x_Color\').value; if (v.substr(v.length-1)==\',\') document.getElementById(\'x_Color\').value+=\' read : \'+document.getElementById(\'x_Color_Input\').value; else document.getElementById(\'x_Color\').value=\'read : \'+document.getElementById(\'x_Color_Input\').value;" id="x_Color_Input" value="#66AAAA"> &nbsp; read : 883333</span></nobr><br>'; 
		html += '<nobr><span class="label">  Class:&nbsp; </span>';
		html += '<span class="field" style="width:355px;"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Class" NAME="Class" value=""> &nbsp; &nbsp; "CG CHG CHH"</span></nobr><br>';
		html += '<nobr><span class="label">  Policy:&nbsp; </span>';
		html += '<span class="field" style="width:355px;"><input class="CEL" style="width:250px;" TYPE="TEXT" id="x_Policy" NAME="Policy" value=""> &nbsp; &nbsp; "1:100:10,1:1:10"</span></nobr><br>';
	}
	if (bt%10==2)
	{
		html += '<nobr><span class="label">Settings: &nbsp; </span>';
		html += '<span class="field" style="width:76px;"> Baseline: <input class="CEL" style="width:27px;" TYPE="number" id="x_Baseline" NAME="Baseline" value="0"></span> ';
		html += '<span class="field" style="width:125px;">Display: <select class="CEL" style="width:79px;" id="x_Display" NAME="Display" value=""><option value="0">Histogram</option><option value="1">Heatmap</option></select></span> ';
		html += '<span class="field" style="width:148px;">Global Scale: <select class="CEL" style="width:82px;" id="x_GlobalScale" NAME="GlobalScale" value=""><option value="0">Individual(Screen)</option><option value="1">Uniform(Screen)</option><option value="2">Fixed(Genome)</option></select></span> ';
		html += '</nobr><br>';	
		html += '<nobr><span class="label">&nbsp; </span>';
		html += '<span class="field" style="width:71px;"> Yaxis: <input class="CEL" style="width:27px;" TYPE="number" id="x_Yaxis" NAME="Yaxis" value="20"></span>';
		html += '<span class="field" style="width:142px;"> Resolution - Max: <input class="CEL" style="width:44px;" TYPE="number" id="x_Max" NAME="Max" value="" placeholder="200000"></span>';
		html += '<span class="field" style="width:60px;"> Min: <input class="CEL" style="width:30px;" TYPE="number" id="x_Min" NAME="Min" value="" placeholder="0.05"></span>';
		html += '</nobr><br>';
		html += '<nobr><span class="label">&nbsp; </span>';
		html += '<span class="field" style="width:70px;"> Multi: <input class="CEL" style="width:27px;" TYPE="number" id="x_Multi" NAME="Multi" value="1"></span>';
		html += '<span class="field" style="width:92px;">HiC_d: <select class="CEL" style="width:42px;" id="x_Hic_d" NAME="HiC_d" value=""><option value="0">0</option><option value="1">1</option></select></span>';
		html += '<span class="field" style="width:125px;">Activate: <select class="CEL" style="width:79px;" id="x_Activate" NAME="Activate" value="0"><option value="0">Single</option><option value="1">Folder</option></select></span> </nobr><br>';
		html += '<nobr><span class="label">Priority: &nbsp; </span>';
		html += '<span class="field" style="width:100px;height:27px;"> <select class="CEL" style="width:82px;" id="x_Priority" NAME="Priority" value=""><option value="Public">Public</option><option value="Protected">Protected</option><option value="Private">Private</option></select></span>';
		html += '<span class="label" style="height:27px;text-align:left;width:65px;"> <input TYPE="checkbox" id="x_AllowCORS" NAME="AllowCORS" value=1> CORS</span> ';
		html += '<span class="label" style="height:27px;text-align:left;width:96px;"> <input TYPE="checkbox" id="x_DynamicJS" NAME="DynamicJS" value=1> Dynamic JS</span> ';
		html += '<span class="label" style="height:27px;text-align:left;width:88px;"> <input TYPE="checkbox" id="x_FinalVersion" NAME="FinalVersion" value=1> Final </span>';
		html += '</nobr>';
	}
	if (bt!=0 && (bt%10==4 || bt==30))
	{
		html += '<br><nobr><span class="label">  &nbsp; </span>';
		html += '<span class="label" style="text-align:left;width:115px;"> <input TYPE="checkbox" id="x_GlobalSet" NAME="GlobalSet" value=true > Set thru subtree</span>';
		html += '<span class="label" style="text-align:left;width:80px;"> <input TYPE="checkbox" id="x_ForceSet" NAME="ForceSet" value=true > Override</span>';
		//html += '<span class="label" style="text-align:left;width:80px;"> <input TYPE="checkbox" id="x_Normalize" NAME="Normalize" value=true > Normalize</span></nobr>';
		html += '</nobr>';
	}
	html += '</form>';
	html += '<br>';
	if (bt!=0 && (bt%10==4 || bt==30))
	{
		html += '<nobr><span class="label" style="text-align:left;width:180px;height:25px;"> &nbsp; <span style="cursor:pointer;" onClick="javascript:closeDiv();">Cancel</span></span><span class="label" style="text-align:center;width:118px;height:25px;"> &nbsp; <span style="cursor:pointer;" onClick="javascript:normalizeNode();">Normalize</span></span><span class="label" style="text-align:right;width:180px;"><span style="cursor:pointer;" onClick="javascript:doneEditNode();">Done</span> &nbsp; </span></nobr>';
	}
	else
	{
		html += '<span class="label" style="text-align:left;width:240px;height:25px;"> &nbsp; <span style="cursor:pointer;" onClick="javascript:closeDiv();">Cancel</span></span><span class="label" style="text-align:right;width:240px;"><span style="cursor:pointer;" onClick="javascript:doneEditNode();">Done</span> &nbsp; </span>';
	}
	return html;
}

function id2attr()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) return;
	var attr = node.attributes;

	if (null != document.getElementById("x_Data_Type") && '' != document.getElementById("x_Data_Type").value) attr.Data_Type = document.getElementById("x_Data_Type").value;
	if (null != document.getElementById("x_Provider_Name") && '' != document.getElementById("x_Provider_Name").value) attr.Provider_Name = document.getElementById("x_Provider_Name").value;
	if (null != document.getElementById("x_Provider_Mail") && '' != document.getElementById("x_Provider_Mail").value) attr.Provider_Mail = document.getElementById("x_Provider_Mail").value;
	if (null != document.getElementById("x_Institution") && '' != document.getElementById("x_Institution").value) attr.Institution = document.getElementById("x_Institution").value;
	if (null != document.getElementById("x_Institution_Link") && '' != document.getElementById("x_Institution_Link").value) attr.Institution_Link = document.getElementById("x_Institution_Link").value;
	if (null != document.getElementById("x_Institution_Logo") && '' != document.getElementById("x_Institution_Logo").value) attr.Institution_Logo = document.getElementById("x_Institution_Logo").value;
	if (null != document.getElementById("x_Organism") && '' != document.getElementById("x_Organism").value) attr.Organism = document.getElementById("x_Organism").value;
	if (null != document.getElementById("x_Include") && '' != document.getElementById("x_Include").value) attr.Include = document.getElementById("x_Include").value;
	if (null != document.getElementById("x_Server") && '' != document.getElementById("x_Server").value) attr.Server = document.getElementById("x_Server").value;
	if (null != document.getElementById("x_Path") && '' != document.getElementById("x_Path").value) attr.Path = document.getElementById("x_Path").value;
	if (null != document.getElementById("x_Table_List") && '' != document.getElementById("x_Table_List").value) attr.Table_List = document.getElementById("x_Table_List").value;
	if (null != document.getElementById("x_Title_List") && '' != document.getElementById("x_Title_List").value) attr.Title_List = document.getElementById("x_Title_List").value;
	if (null != document.getElementById("x_Info_List") && '' != document.getElementById("x_Info_List").value) attr.Info_List = document.getElementById("x_Info_List").value;
	if (null != document.getElementById("x_Options_List") && '' != document.getElementById("x_Options_List").value) attr.Options_List = document.getElementById("x_Options_List").value;
	if (null != document.getElementById("x_Name_List") && '' != document.getElementById("x_Name_List").value) attr.Name_List = document.getElementById("x_Name_List").value;
	if (null != document.getElementById("x_File_List") && '' != document.getElementById("x_File_List").value) attr.File_List = document.getElementById("x_File_List").value;
	if (null != document.getElementById("x_Track_URL") && '' != document.getElementById("x_Track_URL").value) attr.Track_URL = document.getElementById("x_Track_URL").value;
	if (null != document.getElementById("x_Track_Name") && '' != document.getElementById("x_Track_Name").value) attr.Track_Name = document.getElementById("x_Track_Name").value;
	if (null != document.getElementById("x_Track_Path") && '' != document.getElementById("x_Track_Path").value) attr.Track_Path = document.getElementById("x_Track_Path").value;
	if (null != document.getElementById("x_Track_Type") && '' != document.getElementById("x_Track_Type").value) attr.Track_Type = document.getElementById("x_Track_Type").value;
	if (null != document.getElementById("x_Genome_URL") && '' != document.getElementById("x_Genome_URL").value) attr.Genome_URL = document.getElementById("x_Genome_URL").value;
	if (null != document.getElementById("x_Bookmarks_URL") && '' != document.getElementById("x_Bookmarks_URL").value) attr.Bookmarks_URL = document.getElementById("x_Bookmarks_URL").value;
	if (null != document.getElementById("x_Analysis_URL") && '' != document.getElementById("x_Analysis_URL").value) attr.Analysis_URL = document.getElementById("x_Analysis_URL").value;
	if (null != document.getElementById("x_Append_Assembly")) if (document.getElementById("x_Append_Assembly").checked) attr.Append_Assembly = true; else attr.Append_Assembly = '';
	if (null != document.getElementById("x_iBase")) if (document.getElementById("x_iBase").checked) attr.iBase = true; else attr.iBase = '';
	if (null != document.getElementById("x_Force_Track")) if (document.getElementById("x_Force_Track").checked) attr.Force_Track = true; else attr.Force_Track = '';
	if (null != document.getElementById("x_Author") && '' != document.getElementById("x_Author").value) attr.Author = document.getElementById("x_Author").value;
	if (null != document.getElementById("x_Email") && '' != document.getElementById("x_Email").value) attr.Email = document.getElementById("x_Email").value;
	if (null != document.getElementById("x_Page_Title") && '' != document.getElementById("x_Page_Title").value) attr.Page_Title = document.getElementById("x_Page_Title").value;
	if (null != document.getElementById("x_Notes") && '' != document.getElementById("x_Notes").value) attr.Notes = document.getElementById("x_Notes").value;
	if (null != document.getElementById("x_Citation") && '' != document.getElementById("x_Citation").value) attr.Citation = document.getElementById("x_Citation").value;
	if (null != document.getElementById("x_Assembly") && '' != document.getElementById("x_Assembly").value) attr.Assembly = document.getElementById("x_Assembly").value;
	if (null != document.getElementById("x_Position") && '' != document.getElementById("x_Position").value) attr.Position = document.getElementById("x_Position").value;
	if (null != document.getElementById("x_Bases") && '' != document.getElementById("x_Bases").value) attr.Bases = document.getElementById("x_Bases").value;
	if (null != document.getElementById("x_Pixels") && '' != document.getElementById("x_Pixels").value) attr.Pixels = document.getElementById("x_Pixels").value;
	if (null != document.getElementById("x_Baseline") && '' != document.getElementById("x_Baseline").value) attr.Baseline = document.getElementById("x_Baseline").value;
	
	if (null != document.getElementById("x_Display") && '' != document.getElementById("x_Display").value) attr.Display = document.getElementById("x_Display").value;
	if (null != document.getElementById("x_GlobalScale") && '' != document.getElementById("x_GlobalScale").value) attr.GlobalScale = document.getElementById("x_GlobalScale").value;
	if (null != document.getElementById("x_HiC_d") && '' != document.getElementById("x_HiC_d").value) attr.HiC_d = document.getElementById("x_HiC_d").value;
	if (null != document.getElementById("x_Activate") && '' != document.getElementById("x_Activate").value) attr.Activate = document.getElementById("x_Activate").value;
	if (null != document.getElementById("x_Multi") && '' != document.getElementById("x_Multi").value) attr.Multi = document.getElementById("x_Multi").value;
	if (null != document.getElementById("x_Yaxis") && '' != document.getElementById("x_Yaxis").value) attr.Yaxis = document.getElementById("x_Yaxis").value;
	if (null != document.getElementById("x_Max") && '' != document.getElementById("x_Max").value) attr.Max = document.getElementById("x_Max").value;
	if (null != document.getElementById("x_Min") && '' != document.getElementById("x_Min").value) attr.Min = document.getElementById("x_Min").value;

	if (null != document.getElementById("x_Priority") && '' != document.getElementById("x_Priority").value) attr.Priority = document.getElementById("x_Priority").value;
	if (null != document.getElementById("x_AllowCORS")) if (document.getElementById("x_AllowCORS").checked) attr.AllowCORS = true; else attr.AllowCORS = '';	
	if (null != document.getElementById("x_DynamicJS")) if (document.getElementById("x_DynamicJS").checked) attr.DynamicJS = true; else attr.DynamicJS = '';
	if (null != document.getElementById("x_FinalVersion")) if (document.getElementById("x_FinalVersion").checked) attr.FinalVersion = true; else attr.FinalVersion = '';
	if (null != document.getElementById("x_Force_Page")) if (document.getElementById("x_Force_Page").checked) attr.Force_Page = true; else attr.Force_Page = ''; 
	if (null != document.getElementById("x_Force")) if (document.getElementById("x_Force").checked) attr.Force = true; else attr.Force = '';     
	if (null != document.getElementById("x_Icon") && '' != document.getElementById("x_Icon").value) attr.Icon = document.getElementById("x_Icon").value;
	if (null != document.getElementById("x_Height") && '' != document.getElementById("x_Height").value) attr.Height = document.getElementById("x_Height").value;
	if (null != document.getElementById("x_Scale") && '' != document.getElementById("x_Scale").value) attr.Scale = document.getElementById("x_Scale").value;
	if (null != document.getElementById("x_boxHeight") && '' != document.getElementById("x_boxHeight").value) attr.boxHeight = document.getElementById("x_boxHeight").value;
	if (null != document.getElementById("x_boxWidthMin") && '' != document.getElementById("x_boxWidthMin").value) attr.boxWidthMin = document.getElementById("x_boxWidthMin").value;
	if (null != document.getElementById("x_Single")) if (document.getElementById("x_Single").checked) attr.Single = true; else attr.Single = '';
	if (null != document.getElementById("x_Control")) if (document.getElementById("x_Control").checked) attr.Control = true; else attr.Control = '';
	if (null != document.getElementById("x_showWalks")) if (document.getElementById("x_showWalks").checked) attr.showWalks = true; else attr.showWalks = '';
	if (null != document.getElementById("x_alignControl") && '' != document.getElementById("x_alignControl").value) attr.alignControl = document.getElementById("x_alignControl").value;
	if (null != document.getElementById("x_Arrows")) if (document.getElementById("x_Arrows").checked) attr.NoArrows = true; else attr.NoArrows = '';
	if (null != document.getElementById("x_Labels")) if (document.getElementById("x_Labels").checked) attr.NoLabels = true; else attr.NoLabels = '';
	if (null != document.getElementById("x_Class") && '' != document.getElementById("x_Class").value) attr.Class= document.getElementById("x_Class").value;
	if (null != document.getElementById("x_Policy") && '' != document.getElementById("x_Policy").value) attr.Policy= document.getElementById("x_Policy").value;
	if (null != document.getElementById("x_Color") && '' != document.getElementById("x_Color").value) attr.Color= document.getElementById("x_Color").value;
	if (null != document.getElementById("x_GlobalSet")) if (document.getElementById("x_GlobalSet").checked) attr.GlobalSet = true; else attr.GlobalSet = '';
	if (null != document.getElementById("x_ForceSet")) if (document.getElementById("x_ForceSet").checked) attr.ForceSet = true; else attr.ForceSet = '';
}

function attr2id ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) return;
	var attr = node.attributes;

	if (attr.Data_Type != null) document.getElementById("x_Data_Type").value = attr.Data_Type;
	if (attr.Provider_Name != null && null != document.getElementById("x_Provider_Name")) document.getElementById("x_Provider_Name").value = attr.Provider_Name;
	if (attr.Provider_Mail != null && null != document.getElementById("x_Provider_Mail")) document.getElementById("x_Provider_Mail").value = attr.Provider_Mail;
	if (attr.Institution != null && null != document.getElementById("x_Institution")) document.getElementById("x_Institution").value = attr.Institution;
	if (attr.Institution_Link != null && null != document.getElementById("x_Institution_Link")) document.getElementById("x_Institution_Link").value = attr.Institution_Link;
	if (attr.Institution_Logo != null && null != document.getElementById("x_Institution_Logo")) document.getElementById("x_Institution_Logo").value = attr.Institution_Logo;
	if (attr.Organism != null && null != document.getElementById("x_Organism")) document.getElementById("x_Organism").value = attr.Organism;
	if (attr.Include != null && null != document.getElementById("x_Include")) document.getElementById("x_Include").value = attr.Include;
	if (attr.Server != null && null != document.getElementById("x_Server")) document.getElementById("x_Server").value = attr.Server;
	if (attr.Path != null && null != document.getElementById("x_Path")) document.getElementById("x_Path").value = attr.Path;
	
	if (attr.Table_List != null && null != document.getElementById("x_Table_List")) document.getElementById("x_Table_List").value = attr.Table_List;
	if (attr.Title_List != null && null != document.getElementById("x_Title_List")) document.getElementById("x_Title_List").value = attr.Title_List;
	if (attr.Info_List != null && null != document.getElementById("x_Info_List")) document.getElementById("x_Info_List").value = attr.Info_List;
	if (attr.Options_List != null && null != document.getElementById("x_Options_List")) document.getElementById("x_Options_List").value = attr.Options_List;
	if (attr.File_List != null && null != document.getElementById("x_File_List")) document.getElementById("x_File_List").value = attr.File_List;
	if (attr.Name_List != null && null != document.getElementById("x_Name_List")) document.getElementById("x_Name_List").value = attr.Name_List;
	if (attr.Track_URL != null && null != document.getElementById("x_Track_URL")) document.getElementById("x_Track_URL").value = attr.Track_URL;

	if (null != document.getElementById("x_Track_Name")) document.getElementById("x_Track_Name").value = node.text;
	//if (attr.Track_Name != null && null != document.getElementById("x_Track_Name")) document.getElementById("x_Track_Name").value = attr.Track_Name;
	if (attr.Track_Path != null && null != document.getElementById("x_Track_Path")) document.getElementById("x_Track_Path").value = attr.Track_Path;
	if (attr.Track_Type != null && null != document.getElementById("x_Track_Type")) document.getElementById("x_Track_Type").value = attr.Track_Type;
	if (attr.Append_Assembly != null && attr.Append_Assembly!='' && null != document.getElementById("x_Append_Assembly")) document.getElementById("x_Append_Assembly").checked = true;
	if (attr.iBase != null && attr.iBase != '' && null != document.getElementById("x_iBase")) document.getElementById("x_iBase").checked = true;
	if (attr.Force != null && attr.Force != '' && null != document.getElementById("x_Force")) document.getElementById("x_Force").checked = true;
	if (attr.Author != null && null != document.getElementById("x_Author")) document.getElementById("x_Author").value = attr.Author;
	if (attr.Email != null && null != document.getElementById("x_Email")) document.getElementById("x_Email").value = attr.Email;
	if (attr.Page_Title != null && null != document.getElementById("x_Page_Title")) document.getElementById("x_Page_Title").value = attr.Page_Title;
	if (attr.Notes != null && null != document.getElementById("x_Notes")) document.getElementById("x_Notes").value = attr.Notes;
	if (attr.Citation != null && null != document.getElementById("x_Citation")) document.getElementById("x_Citation").value = attr.Citation;
	if (attr.Genome_URL != null && null != document.getElementById("x_Genome_URL")) document.getElementById("x_Genome_URL").value = attr.Genome_URL;
	if (attr.Bookmarks_URL != null && null != document.getElementById("x_Bookmarks_URL")) document.getElementById("x_Bookmarks_URL").value = attr.Bookmarks_URL;
	if (attr.Analysis_URL != null && null != document.getElementById("x_Analysis_URL")) document.getElementById("x_Analysis_URL").value = attr.Analysis_URL;
	if (attr.Assembly != null && null != document.getElementById("x_Assembly")) document.getElementById("x_Assembly").value = attr.Assembly;
	if (attr.Position != null && null != document.getElementById("x_Position")) document.getElementById("x_Position").value = attr.Position;
	if (attr.Bases != null && null != document.getElementById("x_Bases")) document.getElementById("x_Bases").value = attr.Bases;
	if (attr.Pixels != null && null != document.getElementById("x_Pixels")) document.getElementById("x_Pixels").value = attr.Pixels;
	if (attr.Icon != null && null != document.getElementById("x_Icon")) document.getElementById("x_Icon").value = attr.Icon;
	if (attr.Height != null && null != document.getElementById("x_Height")) document.getElementById("x_Height").value = attr.Height;
	if (attr.Scale != null && null != document.getElementById("x_Scale")) document.getElementById("x_Scale").value = attr.Scale;
	if (attr.boxHeight!= null && null != document.getElementById("x_boxHeight")) document.getElementById("x_boxHeight").value = attr.boxHeight;
	if (attr.boxWidthMin!= null && null != document.getElementById("x_boxWidthMin")) document.getElementById("x_boxWidthMin").value = attr.boxWidthMin;
	if (attr.Single!=null && attr.Single!='' && null != document.getElementById("x_Single")) document.getElementById("x_Single").checked = true;
	if (attr.showWalks!=null && attr.showWalks !='' && null != document.getElementById("x_showWalks")) document.getElementById("x_showWalks").checked = true;
	if (attr.Control!=null && attr.Control!='' && null != document.getElementById("x_Control")) document.getElementById("x_Control").checked = true;
	if (attr.alignControl!=null && attr.alignControl!='' && null != document.getElementById("x_alignControl")) document.getElementById("x_alignControl").value = attr.alignControl;
	if (attr.NoArrows!=null && attr.NoArrows!='' && null != document.getElementById("x_Arrows")) document.getElementById("x_Arrows").checked = true;
	if (attr.NoLabels!=null && attr.NoLabels!='' && null != document.getElementById("x_Labels")) document.getElementById("x_Labels").checked = true;
	if (attr.Yaxis != null && null != document.getElementById("x_Yaxis")) document.getElementById("x_Yaxis").value = attr.Yaxis;
	if (attr.Max != null && null != document.getElementById("x_Max")) document.getElementById("x_Max").value = attr.Max;
	if (attr.Min != null && null != document.getElementById("x_Min")) document.getElementById("x_Min").value = attr.Min;
	if (attr.Priority != null && null != document.getElementById("x_Priority")) document.getElementById("x_Priority").value = attr.Priority;
	if (attr.AllowCORS != null && attr.AllowCORS && null != document.getElementById("x_AllowCORS")) document.getElementById("x_AllowCORS").checked = true;
	if (attr.DynamicJS != null && attr.DynamicJS && null != document.getElementById("x_DynamicJS")) document.getElementById("x_DynamicJS").checked = true;
	if (attr.FinalVersion != null && attr.FinalVersion && null != document.getElementById("x_FinalVersion")) document.getElementById("x_FinalVersion").checked = true;
	if (attr.Force_Track != null && null != document.getElementById("x_Force_Track")) document.getElementById("x_Force_Track").checked = true;
	
	if (attr.Class != null && null != document.getElementById("x_Class")) document.getElementById("x_Class").value = attr.Class;
	if (attr.Policy != null && null != document.getElementById("x_Policy")) document.getElementById("x_Policy").value = attr.Policy;
	if (attr.Color != null && null != document.getElementById("x_Color")) document.getElementById("x_Color").value = attr.Color;
	if (attr.Baseline != null && null != document.getElementById("x_Baseline")) document.getElementById("x_Baseline").value = attr.Baseline;
	if (attr.Display != null && null != document.getElementById("x_Display")) document.getElementById("x_Display").value = attr.Display;
	if (attr.Activate != null && null != document.getElementById("x_Activate")) document.getElementById("x_Activate").value = attr.Activate;
	if (attr.HiC_d != null && null != document.getElementById("x_HiC_d")) document.getElementById("x_HiC_d").value = attr.HiC_d;
	if (attr.GlobalScale != null && null != document.getElementById("x_GlobalScale")) document.getElementById("x_GlobalScale").value = attr.GlobalScale;
	else if (null != document.getElementById("x_GlobalScale")) document.getElementById("x_GlobalScale").value = 2;
	if (attr.Multi != null && null != document.getElementById("x_Multi")) document.getElementById("x_Multi").value = attr.Multi;

	//if (attr.GlobalSet != null && null != document.getElementById("x_GlobalSet")) document.getElementById("x_GlobalSet").value = attr.GlobalSet;
	//if (attr.ForceSet != null && null != document.getElementById("x_ForceSet")) document.getElementById("x_ForceSet").value = attr.ForceSet;
}


function passThruTree (node, key, value, force)
{
	attr = node.attributes;
	for (var i=0; i<key.length; i++)
	{
		if (attr[key[i]]==null || force) attr[key[i]] = value[i];
	}
	if (!node.hasChildNodes()) return;
	node.eachChild (function(mynode) { passThruTree(mynode, key, value, force); });
}

function normalizeNode()
{
	//id2attr();
	if (normalizeSubNodes ()) closeDiv();
}

function normalizeSubNodes ()
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) return;
	var attr = node.attributes;

	if (null != document.getElementById("x_Scale") && '' != document.getElementById("x_Scale").value) attr.Scale = document.getElementById("x_Scale").value;
	if (attr.Scale == null)
	{
		alert ("Error : Scale value is null.");
		return false;
	}
	var scale = attr.Scale;
	var readno = -1;
	if (node.hasChildNodes())
	{
		node.eachChild(function(mynode)
		{
			if (mynode.attributes.Reads!=null && mynode.attributes.Reads>0)
			{
				if (readno==-1) readno = mynode.attributes.Reads;
				mynode.attributes.Scale = (scale*readno/mynode.attributes.Reads).toFixed(4);
			}
		});
	}
	return true;
}

function pass2tree (chk)
{
	var tree = Ext.getCmp('TreePanel');
	var node = tree.getSelectionModel().getSelectedNode();
	if (node==null) return alert("Warning: No node is selected. Please select a node first");
	var attr = node.attributes;
	if (chk==true && attr.GlobalSet!=true) return ;
	
	var key = new Array; var value = new Array;
	var keys = Object.keys(attr);

	for (var i=0,k=0; i<keys.length; i++)
	{
		if (keys[i]!='text' && keys[i]!='id' && keys[i]!='cls' && keys[i]!='iconCls' && keys[i]!='loader' && keys[i]!='Data_Type' && keys[i]!='GlobalSet' && keys[i]!='ForceSet')
		{
			key[k]=keys[i]; value[k]=attr[keys[i]]; k++;
		}
	}
	var force = attr.ForceSet | false;
	if (!node.hasChildNodes()) return;
	node.eachChild (function (mynode) { passThruTree (mynode, key, value, force); } );
	return true;
}

function utilNode ()
{
	if (pass2tree (false))
		alert("Parameters has been passed thru its subtree.");
}

<?PHP
function read_genomes_x_info_string ($fn)
{
	global $aj2;
	$html = '';
	if (!isset($aj2)) $aj2 == $_SESSION('aj_root');

	$fv = $_SERVER['DOCUMENT_ROOT']."/".$aj2."/genomes/.".strtolower($fn);
	if (file_exists($fv))
	{
		if ($fn=='genome' || $fn=='genomes') { $gn='Genome'; $gl='x_Genome_URL'; }
		else if ($fn=='organism' || $fn=='organisms') { $gn='Organism'; $gl='x_Organism'; }
		else if ($fn=='model' || $fn=='models') { $gn='Model'; $gl='x_Model'; }
		else if ($fn=='include' || $fn=='includes') { $gn='Include'; $gl='x_Include'; }
		else if ($fn=='mysql_include' || $fn=='mysql_includes') { $gn='Include'; $gl='x_Include'; }
		else if ($fn=='simple_include' || $fn=='simple_includes') { $gn='Include'; $gl='x_Include'; }
		else if ($fn=='server' || $fn=='servers') { $gn='Server'; $gl='x_Server'; }
		else if ($fn=='salk_icons') { $gn='Icon'; $gl='x_Icon'; }
		else if ($fn=='track_types') { $gn='Track_Type'; $gl='x_Track_Type'; }
		else if ($fn=='bookmarks') { $gn='Bookmarks_URL'; $gl='x_Bookmarks_URL'; }
		else if ($fn=='analyses') { $gn='Analysis_URL'; $gl='x_Analysis_URL'; }
		else return;

		$ga = explode (PHP_EOL, trim(file_get_contents($fv)));
		if (count($ga)>0)
		{
			$html .= '<select class="CEL" name="'.$gn.'_Options" style="width:90px;" onChange="document.getElementById(\\\''.$gl.'\\\').value=this.value; ">';
			$html .= '<option value="">Select from';
			foreach ($ga as $gv)
			{
				if ($gv[0]=='#') continue;
				$gz = explode("\t", $gv);
				$gx = trim($gz[1]);
				if (trim($gz[0])=='-') $html .= "<hr>";
				else if ($gn=="Genome")
				{
					if ($gx[0]=='/' || $gx[0]=='.') $html .= '<option value="'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
					else $html .= '<option value="'.$aj2.'/genomes/'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
				}
				else if ($gn=="Model")
				{
					if ($gx[0]=='/' || $gx[0]=='.') $html .= '<option value="'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
					else $html .= '<option value="'.$aj2.'/models/'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
				}
				else if ($gn=="Bookmarks_URL" || $gn=="Analysis_URL")
				{
					if (substr(trim($gz[1]), 0, 4)=="http")
						$html .= '<option value="'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
					else $html .= '<option value="'.$aj2.'/includes/'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';

				}
				else  $html .= '<option value="'.trim($gz[1]).'">&nbsp;'.trim($gz[0]).'</option>';
			}
			$html .= '</select>';
		}
	}
	return $html;
}
?>

var myDock;
var myDockOut;

function dockIn (self, size, pace)
{
	size = typeof size !== 'undefined' ? size : 48;
	pace = typeof pace !== 'undefined' ? pace : 1;
	if (myDock !== 'undefined') clearInterval(myDock);
	myDock = setInterval(function(){dockZoomIn(self, size, pace)}, 2);
}

function dockOut (self, size, pace)
{
	size = typeof size !== 'undefined' ? size : 36;
	pace = typeof pace !== 'undefined' ? pace : 2;
	if (myDockOut !== 'undefined') clearInterval(myDockOut);
	myDockOut = setInterval(function(){dockZoomOut(self, size, pace)}, 1);
}

function dockZoomIn (self, size, pace)
{
	var w = parseInt(self.style.width);
	var h = parseInt(self.style.height);
	if (w < size)
	{
		w += pace; h += pace;
		self.style.width = w.toString()+'px';
		self.style.height = h.toString()+'px';
	}
	else clearInterval(myDock);
}

function dockZoomOut (self, size, pace)
{
	var w = parseInt(self.style.width);
	var h = parseInt(self.style.height);
	if (w > size)
	{
		w -= pace; h -= pace;
		self.style.width = w.toString()+'px';
		self.style.height = h.toString()+'px';
	}
	else clearInterval(myDockOut);
}
