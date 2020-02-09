<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
  <title>BICCN: Mouse primary motor cortex (MOp) cell types</title>

  <link type='text/css' rel='stylesheet' href='annoj_cndd/aj2_css_dev/ext-all.css' />
  <script type='text/javascript' src='annoj_cndd/js/ext-base-3.2.js'></script>
  <script type='text/javascript' src='annoj_cndd/js/ext-all-3.2.js'></script>

  <link type='text/css' rel='stylesheet' href='annoj_cndd/aj2_css_dev/viewport.css' />
  <link type='text/css' rel='stylesheet' href='annoj_cndd/aj2_css_dev/plugins.css' />
  <link type='text/css' rel='stylesheet' href='annoj_cndd/aj2_css_dev/salk.css' />
  <script type='text/javascript' src='annoj_cndd/js/excanvas.js'></script>
  <!-- <script type='text/javascript' src='annoj_cndd/js/aj-CEMBA.js'></script> -->
  <script type='text/javascript' src='annoj_cndd/js/aj-netX-EAM.js'></script>
  
  <!-- cluster info -->
  <script type="text/javascript" src="miniatlas_cluster_round2.js"></script>
  <?php 
  include './annoj_cndd/includes/loadCsv.php'; 
  $fn = 'miniatlas_tracks.csv';
  loadCsv($fn)
  ?>

  <!-- Config -->
  <script type='text/javascript'>
  
  AnnoJ.config = {

    info : {
      title  : 'Mouse MOp BICCN',
      genome  : '',
      contact  : '',
      email : '',
      institution : ''
    },
    tracks : [
    //Models
    
    {
      id   : 'gene_model_mm10',
      name : 'Gene Models (mm10)',
      type : 'ModelsTrack',
      path : 'Annotation models',
      data : './browser/fetchers/models/genes_mm10.php',
      height : 100,
      showControls : true, cls :  "AJ_track AJ_darkborder",
    },
    {
      id   : 'gene_model_mm10_gencode_vM2',
      name : 'Gene Models (mm10 Gencode vM2)',
      type : 'ModelsTrack',
      path : 'Annotation models',
      data : './browser/fetchers/models/genes_mm10_gencode.php',
      height : 100,
      showControls : true, cls :  "AJ_track AJ_darkborder",
    },

    ],

  active : [// *** Gene models
  'gene_model_mm10',
  ],
  genome    : './browser/fetchers/mus_musculus.php',
  bookmarks : './browser/fetchers/mus_musculus.php',
  stylesheets : [
  {
    id   : 'css1',
    name : 'Plugins CSS',
    href : 'css/plugins.css',
    active : true
  },{
    id   : 'css2',
    name : 'SALK CSS',
    href : 'css/salk.css',
    active : true
  }
  ],
  location : {
    assembly : '1',
    position : '132487763',
    bases    : 300,
    pixels   : 1
  },
  admin : {
    name  : 'Eran Mukamel',
    email : 'emukamel@ucsd.edu',
    notes : 'University of California, San Diego'
  },
};

// Add tracks
var new_tracks=[]; 
var track_height=20;
for (i=0; i<tracks.length; i++) {
  var hidden = false;
  if (/^#/.test(tracks[i].ensemble)) { hidden = true; }
  tracks[i].ensemble = tracks[i].ensemble.replace('#','')
  track = {
    'id'   : 'mcg_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster+'_CGN',
    'name' : 'mCG '+tracks[i].name+' C'+tracks[i].cluster,
    'type' : 'MethTrack',
    'path' : 'Epigenome/DNA Methylation',
    'data' : './browser/fetchers/mc/mc_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
    'iconCls' : 'salk_meth',
    'height' : track_height,
    'class' : 'CG -COV',
    'single': true,
    'showControls' : false,
    'modality' : 'mcg', 'hidden': hidden, 'cluster': tracks[i].cluster,
    'trackdata' : tracks[i],
  };
  new_tracks.push(track);

  // **** mCH
  track = {
      'id'   : 'mcac_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster+'_CAC',
      'name' : 'mCAC '+tracks[i].name+' C'+tracks[i].cluster,
      'type' : 'MethTrack',
      'path' : 'Epigenome/DNA Methylation',
      'data' : './browser/fetchers/mc/mc_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'_CAC.php',
      'iconCls' : 'salk_meth',
      'height' : track_height,
      'class' : 'CH -COV',
      'single': true,
      'showControls' : false, 
      'modality' : 'mcac','hidden': hidden, 'cluster': tracks[i].cluster,
      'trackdata' : tracks[i],
    };
  new_tracks.push(track);

  track = {
    'id'   : 'atac_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
    'name' : 'ATAC '+tracks[i].name+' C'+tracks[i].cluster,
    'type' : 'PairedEndTrack',
    'path' : 'Epigenome/ATAC-Seq',
    'data' : './browser/fetchers/atac/atac_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
    'iconCls' : 'salk_bed',
    'height' : track_height,
    'scale': atac_scales[tracks[i].cluster],
    'single': true,'modality' : 'atac', 'hidden': hidden, 'cluster': tracks[i].cluster,
    'trackdata' : tracks[i],
  }
  new_tracks.push(track);

  track = {
    'id'   : 'snRNA_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
    'name' : 'snRNA '+tracks[i].name+' C'+tracks[i].cluster,
    'type' : 'PairedEndTrack',
    'path' : 'RNA/snRNA-Seq',
    'data' : './browser/fetchers/rna/RNA_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
    'iconCls' : 'silk_bricks',
    'height' : track_height,
    'scale': snrna_scales[tracks[i].cluster],
    'single': true,
    'color' : {count: '#ff0000'},
    'modality' : 'snrna', 
    'hidden': hidden, 
    'trackdata' : tracks[i],
    'cluster': tracks[i].cluster,
    
  };
  new_tracks.push(track);

  track = {
    'id'   : 'scRNA_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
    'name' : 'scRNA '+tracks[i].name+' C'+tracks[i].cluster,
    'type' : 'PairedEndTrack',
    'path' : 'RNA/scRNA-Seq',
    'data' : './browser/fetchers/rna/scRNA_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
    'iconCls' : 'silk_bricks',
    'height' : track_height,
    'scale': scrna_scales[tracks[i].cluster],
    'single': true,
    'color' : {count: '#af0770'},
    'modality' : 'scrna', 'hidden': hidden, 'cluster': tracks[i].cluster,
    'trackdata' : tracks[i],
    };
  new_tracks.push(track);
} 

// REPTILE enhancers

var enhancer_clusters = [
"mESC",
"ens2_L23_IT",
"ens2_L5_PT",
"ens2_L45_IT_1",
"ens2_L45_IT_2",
"ens2_L5_IT_S100b",
"ens2_L6_CT",
"ens2_L6_IT",
"ens2_L6_NP",
"ens2_Lamp5",
"ens2_Pvalb_Calb1",
"ens2_Pvalb_Reln",
"ens2_Sst",
"ens2_Vip",
]; 
var enhancer_parent_cluster = [
"mESC",
"C4_2",
"C9_1",
"C1_1",
"C1_2",
"C3_2",
"C2_3",
"C3_1",
"C7_1",
"C6_6",
"C5_2",
"C5_1",
"C5_3",
"C6_3",
];
var enhancer_group = [
"mESC",
"L2/3 IT",
"L5 PT",
"L4/5 IT_1",
"L4/5 IT_2",
"L5 IT",
"L6 CT",
"L6 IT",
"L6 NP",
"Lamp5",
"Pvalb Calb1",
"Pvalb Reln",
"Sst",
"Vip",
];
var enhancer_cellclass = [
"mESC",
"exc",
"exc",
"exc",
"exc",
"exc",
"exc",
"exc",
"exc",
"inh:cge",
"inh:mge",
"inh:mge",
"inh:mge",
"inh:cge",
];

for (var i=0; i<enhancer_clusters.length; i++) {
  cluster = enhancer_clusters[i];
  track = {
    'id'   : 'Enhancer_'+cluster,
    'name' : 'Enhancer '+cluster.replace('ens2_','ens2 '+enhancer_parent_cluster[i]+' '),
    'type' : 'PairedEndTrack',
    'path' : 'Enhancers',
    'data' : './browser/fetchers/dmr/ENHANCER_'+cluster+'.php',
    'iconCls' : 'dmr',
    'height' : 5,
    'scale': 100,
    'single': true,
    'color' : {read: '#000000'},
    'modality' : 'enhancer', 
    'cluster' : enhancer_parent_cluster[i].replace(/^C/,''), 
    'hidden' : false,
    'trackdata' : {'enhancer_group': enhancer_group[i], 'name': '0'+enhancer_group[i], 'cellclass': enhancer_cellclass[i]},
  };
  new_tracks.push(track);
}
AnnoJ.config.tracks = AnnoJ.config.tracks.concat(new_tracks);

AnnoJ.config.settings.yaxis = 100;

</script>

<!-- Enable URL queries -->
<script type='text/javascript'> var queryPost; </script>
<script type='text/javascript' src='./browser/js/urlinit.js'></script>

<script type='text/javascript'>
  // Set active tracks
var re_ens = new RegExp('_ens'+ensemble+'_');
var re_modality, modality = [];
var AllModalities = ['mcg','enhancer','mcac','atac','scRNA','snRNA'];
var showModalities=modalities.replace(/:$/,'').split(':');

// Select the tracks to be shown
function myTrackFilter(track) {
  var out=false;
  try {
    out = (RegExp(celltype).test(track.trackdata.cellclass) || celltype=='all') && 
      re_ens.test(track['id']) &&
      showModalities.includes(track['modality']) &&
      !track['hidden'];
  } catch {
    out = false;
  }
  return out;
}
var currTracks=AnnoJ.config.tracks.filter(myTrackFilter);
// var currTracks=AnnoJ.config.tracks.filter(x => RegExp(celltype).test(x.trackdata.cellclass) & re_ens.test(x['id']) & 
//   showModalities.includes(x['modality']) & !x['hidden']);

// Sort the tracks by cell type, then modality
function modalityIndex(a) {
  var out=-1;
  if (a['modality']) {
    var mod=a['modality'];
    if (mod=='enhancer') { mod=AllModalities[0]; }
    out=AllModalities.findIndex(function (x) { return RegExp(x,'i').test(mod)});
    // if (mod=='enhancer') { mod='0a'; }
  } else { out=-1; }
  return out
}
function getCellTypeModality(a, groupby) {
  // var x=a['cluster'];
  switch (groupby) {
    case 'modality':
      var x=a['trackdata']['enhancer_group'].replace('/','');
      // if (a['modality']!='enhancer') {
      //   x = x+'0';
      // }
      x = modalityIndex(a)+'_'+x;
      break;
    case 'celltype':
      var x=a['trackdata']['enhancer_group']+a['trackdata']['name'];
      x = x+'_'+modalityIndex(a);
      break;
    default:
      break;
  }
  return x;
}
currTracks.sort(function(a, b) { return (getCellTypeModality(a,groupby) > getCellTypeModality(b,groupby)) ? 1 : -1});
for (i=0; i<currTracks.length; i++) {
  AnnoJ.config.active.push(currTracks[i].id);
  var nextTrack=currTracks[i+1];
  // if (groupby=='modality') {
  //   if (nextTrack) {
  //     if (nextTrack['modality']=='enhancer') {
  //           nextTrack=currTracks[i+2];
  //     }
  //   }
  // }
  // Add a border between track groups
  if (nextTrack) {
    // console.log(currTracks[i].id, modalityIndex(currTracks[i]), modalityIndex(nextTrack));
    if ((groupby=='celltype' && currTracks[i].modality!='enhancer' &&
     (currTracks[i].cluster!=nextTrack.cluster || nextTrack.modality=='enhancer')) || 
      (groupby=='modality' && (currTracks[i].modality!=nextTrack.modality) && 
        currTracks[i].modality!='enhancer' && nextTrack.modality!='enhancer')) {
        AnnoJ.config.tracks.find(x => x['id']==currTracks[i].id).cls =  "AJ_track AJ_darkborder";
    }
  }
}

    // Set track colors
    var my_celltype='', modality='', color='', tmp='';
    for (i=0; i<AnnoJ.config.tracks.length; i++) {
      var track=AnnoJ.config.tracks[i];
      [modality,tmp,my_celltype,cellclass] = track.name.split(' ');
      // if (celltype) {var x = celltype.match(/^C[0-9]+_[0-9]/); celltype=x;}
      if (my_celltype) { my_celltype = my_celltype.match(/^C[0-9]+_[0-9]/);}
      if (my_celltype) { my_celltype = my_celltype[0].replace('C',''); } else { my_celltype = ''; }
      switch (colorby) {
        case 'celltype':
        color = cluster_colors[my_celltype];
        break;
        case 'modality':
        color = cluster_colors[modality];
        break;
        case 'cellclass':
        color = cluster_colors[cellclass];
        break;
      }
      // AnnoJ.config.tracks[i].cluster_rank = cluster_ranks[celltype]
      switch (modality) {
        case 'mCG': 
        AnnoJ.config.tracks[i].color = {'CG': color};
        break;
        case 'mCAC': 
        AnnoJ.config.tracks[i].color = {'CG': color};
        break;
        case 'ATAC':
        AnnoJ.config.tracks[i].color = {'count': color, 'read': color};
        break;
        case 'RNA':
        AnnoJ.config.tracks[i].color = {'count': color};
        break;
      }
    }

    // // Set track height
    // var ntracks = AnnoJ.config.active.length;
    // var track_height = Math.min(Math.max(window.innerHeight/ntracks,7),30);
    // console.log(ntracks)
    // console.log(track_height)
    // for (i=0; i<AnnoJ.config.tracks.length; i++) {
    //   if (AnnoJ.config.tracks[i].type != 'ModelsTrack' && AnnoJ.config.tracks[i].path!='Enhancers' ) {
    //         AnnoJ.config.tracks[i]['height'] = track_height;
    //       }
    // }
    </script>
</head>

<body>

  <noscript>
    <table id='noscript'><tr>
      <td><img src='hs/img/Anno-J.jpg' /></td>
      <td>
        <p>Anno-J cannot run because your browser is currently configured to block Javascript.</p>
        <p>To use the application please access your browser settings or preferences, turn Javascript support back on, and then refresh this page.</p>
        <p>Thankyou, and enjoy the application!<br /></p>
      </td>
    </tr></table>
  </noscript>

</body>

</html>
