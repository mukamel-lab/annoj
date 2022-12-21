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
	return '';
} 

/*  can tell it to set assembly, position, bases, and pixels values, from upenn site  */
var variables;
var celltype='exc', groupby='modality', colorby='modality', ensemble=2, ens=2, modalities = 'mcg:enhancer:atac:scRNA', cluster='', tracknames='', gene='';
if (typeof(AnnoJ)=='undefined') { 
	var AnnoJ={}, a; 
	AnnoJ.config = {};
	AnnoJ.config.location = {};
	}
a=getQueryVariable('assembly'); if (a) { AnnoJ.config.location.assembly = a;}
a=getQueryVariable('position'); if (a) { AnnoJ.config.location.position = a;}
a=getQueryVariable('bases'   ); if (a) { AnnoJ.config.location.bases	= a;}
a=getQueryVariable('pixels'  ); if (a) { AnnoJ.config.location.pixels   = a;}

var scaleFactors=getQueryVariable('scaleFactor');
scaleFactors = scaleFactors.split(':');
if (scaleFactors=="") { scaleFactors=[1,1,1]; }
var scaleFactors_inv = [];
for (i=0; i<scaleFactors.length; i++) {
	// scaleFactors_inv[i] = 1/parseFloat(scaleFactors[i]);
	scaleFactors_inv[i] = parseFloat(scaleFactors[i]);
}
var scaleFactor = {'atac': scaleFactors_inv[0], 'scrna': scaleFactors_inv[1], 'snrna': scaleFactors_inv[2]};
AnnoJ.config.scaleFactor = AnnoJ.config.scaleFactorInit = {...scaleFactor};

var input_vars = ["celltype", "groupby", "colorby", "ensemble", "modalities", "cluster","tracknames","gene"];
for (i=0; i<input_vars.length; i++) {
	eval('a=getQueryVariable("'+input_vars[i]+'"); if (a) { '+input_vars[i]+'=a;} ');
}

a=getQueryVariable('location'); if (a) { 
	loc = a.split(':');
	if (loc[0]) AnnoJ.config.location.assembly = loc[0];
	if (loc[1]) AnnoJ.config.location.position = parseInt(loc[1]);
	if (loc[2]) AnnoJ.config.location.bases    = parseInt(loc[2]);
	if (loc[3]) AnnoJ.config.location.pixels   = parseInt(loc[3]);
}

// Automatically detect the modality of tracks where it's not set already
if (AnnoJ.config.tracks) {
	for (i=0; i<AnnoJ.config.tracks.length; i++) {
		var track=AnnoJ.config.tracks[i];
		if (!track['modality']) {
			if (track['path'].includes('RNA')) {
				track['modality']='rna';
			} else if (track['path'].includes('Methyl') | track['path'].includes('mC')) {
				track['modality']='mcg';
			} else if (track['path'].includes('ATAC') ) {
				track['modality']='atac';
			} else if (track['path'].includes('ChIP') ) {
				track['modality']='chip';
			}
		}
	}
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

