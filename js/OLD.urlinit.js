/*  This script is inspired by location.search and the urlinit.js script from upenn   *
 *  http://tesla.pcbi.upenn.edu/annoj/js/urlinit.js. However, the functions of        *
 *  deltrack, addtrack, alterpath and active, in addition to the original hide and    *
 *  setting location funtions, are created to allow user monitoring Anno-J genome     *
 *  browser dynamically, easily and fully.          Author: Huaming Chen 2015-08-28   */

/*  call use location.search to get query variables, from upenn site, revised by HC   */
function getQueryVariable(variable) 
{
	if (!variables)
	{ 
		if (!queryPost) queryPost = window.location.search.substring(1);
		variables = decodeURI(queryPost).split("&");
	} 
	for (var i=0;i<variables.length;i++) 
	{ 
		var pair = variables[i].split("="); 
		if (pair[0] == variable) return pair[1]; 
	} 
} 

/*  can tell it to set assembly, position, bases, and pixels values, from upenn site  */
var variables;
a=getQueryVariable('assembly'); if (a) { AnnoJ.config.location.assembly = a;}
a=getQueryVariable('position'); if (a) { AnnoJ.config.location.position = a;}
a=getQueryVariable('bases'   ); if (a) { AnnoJ.config.location.bases	= a;}
a=getQueryVariable('pixels'  ); if (a) { AnnoJ.config.location.pixels   = a;}

/*  can tell it to remove track(s) from AnnoJ.config.tracks            -- added by HC *
 *  Usage : deltrack=track_name;track_name;track_path;track_parent_path               */
remove_str = getQueryVariable('deltrack');
if ( remove_str )
{
	remove_tracks = remove_str.split(";");

	for (var i=0; i < remove_tracks.length; i++) {
		a = decodeURI(remove_tracks[i]);
		for (var j=AnnoJ.config.tracks.length-1; j>=0; j--) {
			if (a==AnnoJ.config.tracks[j].name)
				AnnoJ.config.tracks.splice(j, 1);
			else if (in_path(a, AnnoJ.config.tracks[j].path))
				AnnoJ.config.tracks.splice(j, 1);
		}
	}
	AnnoJ.config.tracks = AnnoJ.config.tracks.filter(Boolean);
}

/* can tell it to add custom tracks from outside of AnnoJ.config      -- added by HC *
 * Usage : addtrack=type:ReadsTrack;data:http://yourserver/fetcher.php;              *
 *		   name:your_name;path:your_path;height:40;scale:0.1;                *
 *		   id:your_id;color:{read:'883333',rColor:'1'};;...track2...         *
 * Track attributes: id, name, type, path, height, boxHeight, scale, single, data,   *
 *		   color, showArrows, showLabels, showControls, iconCls              *
 * Note: The attributes type and data must be included in 'addtrack' string.         *
 * attributes are separated by ";", and tracks are separated by ";;".                */
tracks_str = getQueryVariable('addtrack');
if ( tracks_str )
{
	var track_sa = tracks_str.split(";;");
	var tracks = new Array(); var k = 1;
	for (var i=0; i<track_sa.length; i++)
	{
		var configs = track_sa[i].split(";");
		var track = new Array();
		for (var j=0; j<configs.length; j++)
		{
			var attr = configs[j].substring(0, configs[j].indexOf(":"));
			var value = configs[j].substring(configs[j].indexOf(":")+1);
			track[attr] = value;
		}
		if (track['type'] && track['data'])
		{
			if (!track['id']) track['id'] = 'custom_'+k;
			if (!track['path']) track['path'] = 'Customs';
			if (!track['name']) track['name'] = 'custom_'+k;
			tracks[k] = track; k++;
		}
	}
	AnnoJ.config.tracks = AnnoJ.config.tracks.concat(tracks);
	AnnoJ.config.tracks = AnnoJ.config.tracks.filter(Boolean);
}

/*  can tell it to remove and re-arrange the track_paths.                 -- added by HC   * 
 *  Usage: alterpath=PATH:rule_str;PATH2:rule_str2 ...  PATH : the configured path,        *
 *  rule_str = 1,0,-1,2, ... the rules to re-order the path. Old path A/B/C/D -> B/A/D     *
 *  For exmaple: alterpath=mRNA:1,0,-1,2. If the real Path was mRNA/MGI/10C/Leaf, it would *
 *  be MGI/mRNA/Leaf after path altering. The old index 0 mRNA -> new index 1, 1 MGI -> 0, *
 *  2 10C -> -1 deleted, and 3 Leaf -> 2.                                                  */ 
path_str = getQueryVariable('alterpath');
if ( path_str )
{
	path_a = path_str.split(";");
	for (var i=0; i < path_a.length; i++) {
		b = path_a[i].split(":");
		p = decodeURI(b[0]);
		if (b[1]) r = b[1].split(",");
		for (var j=0; j < AnnoJ.config.tracks.length; j++) {
			d = AnnoJ.config.tracks[j].path;
			if (in_path(p, d))
			{
				AnnoJ.config.tracks[j].path = re_order (d, r);
			}
		}
	}
}

//  can tell it to hide tracks. hide=track_id;track_name;track_path ... from upenn, revised by HC
hide_str = getQueryVariable('hide');
if ( hide_str ) 
{
	hide_tracks = hide_str.split(";");

	// enable to hide tracks by track_name, by track_path. next 12 lines added by HC 
	for (var i=0; i < hide_tracks.length; i++) {
		a = decodeURI(hide_tracks[i]);
		for (var j=0; j < AnnoJ.config.tracks.length; j++) {
			if (-1!=AnnoJ.config.active.indexOf(AnnoJ.config.tracks[j].id))
			{
				if (a==AnnoJ.config.tracks[j].name)
					hide_tracks.push(AnnoJ.config.tracks[j].id);
				else if (in_path(a, AnnoJ.config.tracks[j].path))
					hide_tracks.push(AnnoJ.config.tracks[j].id); 
			}
		}
	}

	for (var i=0; i < hide_tracks.length; i++) {
		AnnoJ.config.active.splice(AnnoJ.config.active.indexOf(hide_tracks[i]), 1);
	}
}

/*  can tell it to activate tracks.  active=track_id;track_name;track_path ... added by HC *
 *  can insert a track to the active list by using active=track_name:1;track_id:4          */
active_str = getQueryVariable('active');
if ( active_str )
{
	active_tracks = active_str.split(";");
	for (var i=0; i < active_tracks.length; i++) 
	{
		var c = decodeURI(active_tracks[i]).split(":");
		var a = c[0]; var b = AnnoJ.config.tracks.length; if (c[1]) b = c[1];
		for (var j=0; j < AnnoJ.config.tracks.length; j++) 
		{
			if (-1==AnnoJ.config.active.indexOf(AnnoJ.config.tracks[j].id))
			{
				if (a==AnnoJ.config.tracks[j].name || a==AnnoJ.config.tracks[j].id)
				{
					if (b) AnnoJ.config.active.splice(b, 0, AnnoJ.config.tracks[j].id);
					else AnnoJ.config.active.push(AnnoJ.config.tracks[j].id);
				}
				else if (in_path(a, AnnoJ.config.tracks[j].path))
				{
					if (b) AnnoJ.config.active.splice(b, 0, AnnoJ.config.tracks[j].id);
					else AnnoJ.config.active.push(AnnoJ.config.tracks[j].id);
				}
			}
		}
	}
	AnnoJ.config.active = AnnoJ.config.active.filter(Boolean);
}

/*  re-order the track paths -1 = delete, 0-n new -- added by HC */
function re_order (path, rules)
{
	var newPath = new Array ();
	var s = path.split("/");
	var n = (rules.length > s.length) ? rules.length : s.length;
	for (var i=0; i<n; i++)
	{
		if (typeof (rules[i]) == 'undefined') newPath[i] = s[i]
		else
		{
			if (rules[i]!=-1)
			{
				if (typeof (s[i]) != 'undefined') newPath[rules[i]] = s[i];
			}
		}
	}
	return newPath.filter(Boolean).join("/");
}

/*  check if path str1 is belong to path str2   -- added by HC */
function in_path (str1, str2)
{
	if (strncmp(str1, str2, str1.length)==0)
	{
		var s1 = str1.split("/");
		var s2 = str2.split("/");
		var k = 0;
		for (var i=0; i<s1.length; i++)
		{
			if (s1[i]==s2[i]) k++;
		}
		if (k==s1.length) return true;
	}
	return false;
}

/*  strncmp two strings in length like in C    -- added by HC */ 
function strncmp ( str1, str2, length ) 
{
    var s1 = (str1+'').substr(0, length);
    var s2 = (str2+'').substr(0, length);
    return ( ( s1 == s2 ) ? 0 : ( ( s1 > s2 ) ? 1 : -1 ) );
}

