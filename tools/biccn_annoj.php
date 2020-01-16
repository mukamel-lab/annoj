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
  <script type='text/javascript' src='annoj_cndd/js/aj-CEMBA.js'></script>

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
      showControls : true
    },
    {
      id   : 'gene_model_mm10_gencode_vM2',
      name : 'Gene Models (mm10 Gencode vM2)',
      type : 'ModelsTrack',
      path : 'Annotation models',
      data : './browser/fetchers/models/genes_mm10_gencode.php',
      height : 100,
      showControls : true
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
    'name' : 'mCG ens'+tracks[i].ensemble+' C'+tracks[i].cluster+' '+tracks[i].name,
    'type' : 'MethTrack',
    'path' : 'Epigenome/DNA Methylation',
    'data' : './browser/fetchers/mc/mc_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
    'iconCls' : 'salk_meth',
    'height' : track_height,
    'class' : 'CG -COV',
    'single': true,
    'showControls' : false,
    'cellclass' : tracks[i].cellclass, 'modality' : 'mcg', 'hidden': hidden,
  };
  new_tracks.push(track);

    // **** mCH - Not included for now
    // track = {
    //     'id'   : 'mch_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster+'_CGN',
    //     'name' : 'mCH ens'+tracks[i].ensemble+' C'+tracks[i].cluster+' '+tracks[i].name,
    //     'type' : 'MethTrack',
    //     'path' : 'DNA Methylation',
    //     'data' : './browser/fetchers/mc/mc_Round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'_CGN_Merge_mc.php',
    //     'iconCls' : 'salk_meth',
    //     'height' : track_height,
    //     'class' : '-CG CH -COV',
    //     'single': true,
    //     'showControls' : false,
    //     'cellclass' : tracks[i].cellclass, 'modality' : 'mch',
    //   };
    // new_tracks.push(track);

    track = {
      'id'   : 'atac_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
      'name' : 'ATAC ens'+tracks[i].ensemble+' C'+tracks[i].cluster+' '+tracks[i].name,
      'type' : 'PairedEndTrack',
      'path' : 'Epigenome/ATAC-Seq',
      'data' : './browser/fetchers/atac/atac_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
      'iconCls' : 'salk_bed',
      'height' : track_height,
      'scale': atac_scales[tracks[i].cluster],
      'single': true,
      'cellclass' : tracks[i].cellclass, 'modality' : 'atac', 'hidden': hidden,
    }
    new_tracks.push(track);

    track = {
      'id'   : 'snRNA_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
      'name' : 'snRNA ens'+tracks[i].ensemble+' C'+tracks[i].cluster+' '+tracks[i].name,
      'type' : 'PairedEndTrack',
      'path' : 'RNA/snRNA-Seq',
      'data' : './browser/fetchers/rna/RNA_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
      'iconCls' : 'silk_bricks',
      'height' : track_height,
      'scale': snrna_scales[tracks[i].cluster],
      'single': true,
      'color' : {count: '#ff0000'},
      'cellclass' : tracks[i].cellclass, 'modality' : 'snrna', 'hidden': hidden,
    };
    new_tracks.push(track);

    track = {
      'id'   : 'scRNA_ens'+tracks[i].ensemble+'_C'+tracks[i].cluster,
      'name' : 'scRNA ens'+tracks[i].ensemble+' C'+tracks[i].cluster+' '+tracks[i].name,
      'type' : 'PairedEndTrack',
      'path' : 'RNA/scRNA-Seq',
      'data' : './browser/fetchers/rna/scRNA_round'+tracks[i].ensemble+'_C'+tracks[i].cluster+'.php',
      'iconCls' : 'silk_bricks',
      'height' : track_height,
        'scale': scrna_scales[tracks[i].cluster],
        'single': true,
        'color' : {count: '#af0770'},
        'cellclass' : tracks[i].cellclass, 'modality' : 'scrna', 'hidden': hidden,
      };
      new_tracks.push(track);
    }

// REPTILE enhancers

var enhancer_clusters = [
"mESC",
"ens2_L23_IT",
"ens2_L5_ET",
"ens2_L5_IT_Rspo1",
"ens2_L5_IT_Rspo2",
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
"C9_1_1",
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
  [ens,celltype1,celltype2] = track.name.split(' ');
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
    'cellclass' : enhancer_cellclass[i],
    'modality' : 'enhancer'
  };
  new_tracks.push(track);
}
AnnoJ.config.tracks = AnnoJ.config.tracks.concat(new_tracks);

</script>

<!-- Enable URL queries -->
<script type='text/javascript'> var queryPost; </script>
<script type='text/javascript' src='./browser/js/urlinit.js'></script>

<script type='text/javascript'>
  // Set active tracks
var re_celltype = new RegExp(celltype);
var re_ens = new RegExp('_ens'+ensemble+'_');
var re_modality, modality = [];
var modalities = ['mcg','enhancer','mch','atac','scRNA','snRNA'];
var showctxts=showctxt.replace(/:$/,'').split(':');

// Select the tracks to be shown
var currTracks=AnnoJ.config.tracks.filter(x => RegExp(celltype).test(x['cellclass']) & re_ens.test(x['id']) & 
  showctxts.includes(x['modality']) & !x['hidden']);

// Sort the tracks by cell type, then modality
function modalityIndex(a) {
  return modalities.findIndex(function (x) { return RegExp(x,'i').test(a['modality'])});
}
function getCellTypeModality(a, groupby) {
  var x = a['name'].split(' ');
  x.splice(0,1);
  switch (groupby) {
    case 'modality':
      x = modalityIndex(a)+'_'+x.join('_');
      break;
    case 'celltype':
      x = x.join('_')+'_'+modalityIndex(a);
      break;
    default:
      break;
  }
  return x;
}
currTracks.sort(function(a, b) { return (getCellTypeModality(a,groupby) > getCellTypeModality(b,groupby)) ? 1 : -1});
for (i=0; i<currTracks.length-1; i++) {
  AnnoJ.config.active.push(currTracks[i].id);
  // if (modalityIndex(currTracks[i])==showctxts.length-1) {
  if (modalityIndex(currTracks[i])>modalityIndex(currTracks[i+1])) {
    AnnoJ.config.tracks.find(x => x['id']==currTracks[i].id).cls =  "AJ_track AJ_darkborder";
  }
}

    // Set track colors
    var celltype='', modality='', color='', tmp='';
    for (i=0; i<AnnoJ.config.tracks.length; i++) {
      var track=AnnoJ.config.tracks[i];
      [modality,tmp,celltype,cellclass] = track.name.split(' ');
      // if (celltype) {var x = celltype.match(/^C[0-9]+_[0-9]/); celltype=x;}
      if (celltype) {celltype = celltype.match(/^C[0-9]+_[0-9]/);}
      if (celltype) { celltype = celltype[0].replace('C',''); } else { celltype = ''; }
      switch (colorby) {
        case 'celltype':
        color = cluster_colors[celltype];
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
