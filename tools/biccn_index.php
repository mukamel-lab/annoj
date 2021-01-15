<html>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <title>BICCN: Mouse primary motor cortex (MOp) cell types</title>
    <!-- <link type='text/css' rel='stylesheet' href='browser/css/navbar.css' /> -->

    <meta charset="utf-8">
    <script src="annoj_cndd/js/fontawesome.js"></script>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="annoj_cndd/css/bootstrap-select.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="annoj_cndd/js/bootstrap-select.js"></script>

    <style type="text/css">
    select.selectpicker {
        display: none;
    }
    /* Prevent FOUC }*/

    div {
        text-align: center;
    }

    .div1 {
        float: left;
        padding: 8px 8px 8px 32px;
    }

    .nav-item {
        margin: 5;
    }
    </style>

    <script type='text/javascript'>
    var queryPost;
    </script>
    <script type='text/javascript' src='./browser/js/urlinit.js'></script>

    <script type='text/javascript'>
    var firstLoad = true;

    function buildURL()
    {
        // Get current browser location and other settings and build a URL
        var modalities = Array.from(document.getElementById('select_modalities').selectedOptions).map(x => x['value']).join(':');
        src = "https://brainome.ucsd.edu/annoj/BICCN_MOp/biccn_annoj.php?";
        var annoj_frame = document.getElementById('annoj_frame');

        if (firstLoad)
        { // First load - get location and scaleFactor from URL
            firstLoad = false;
            var startingLoc = getQueryVariable('location');
            if (startingLoc)
            {
                src += 'location=' + startingLoc;
            }
            if (scaleFactor)
            {
                var scaleFactoru = [],
                modalitiesu = ['atac', 'scrna', 'snrna']
                for (var i = 0; i < modalitiesu.length; i++)
                {
                    scaleFactoru[i] = scaleFactor[modalitiesu[i]].toPrecision(3);
                }
                scaleFactoru = scaleFactoru.join(':');
                src += '&scaleFactor=' + scaleFactoru;
            }
            console.log(src)
        }
        else
        { // After first load - get location and scaleFactor from the AnnoJ frame
            firstLoad = false;
            var loc = annoj_frame.contentWindow.AnnoJ.getLocation();
            src += 'location=' + loc.assembly + ':' + loc.position + ':' + loc.bases + ':' + loc.pixels;
            var scaleFactor = [],
            modalitiesu = ['atac', 'scrna', 'snrna']
            for (var i = 0; i < modalitiesu.length; i++)
            {
                scaleFactor[i] = annoj_frame.contentWindow.AnnoJ.config.scaleFactor[modalitiesu[i]] * annoj_frame.contentWindow.AnnoJ.config.scaleFactorInit[modalitiesu[i]];
                scaleFactor[i] = scaleFactor[i].toPrecision(3);
            }
            scaleFactor = scaleFactor.join(':');
            src += '&scaleFactor=' + scaleFactor;
        };

        // var params = ['ensemble','celltype','colorby','groupby'];
        var params = ['ensemble', 'groupby'];
        for (i = 0; i < params.length; i++)
        {
            if (document.getElementById('select_' + params[i]).value)
            {
                src += '&' + params[i] + '=' + document.getElementById('select_' + params[i]).value;
            }
        }
        if (modalities)
        {
            src += '&modalities=' + modalities;
        }
        if (cluster)
        {
            src += '&cluster=' + cluster;
        }
        if (tracknames)
        { // User can set active tracks by name; overrides celltype
            src += '&tracknames=' + tracknames + '&celltype=none';
        }
        else
        {
            var celltype = document.getElementById('select_celltype').value;
            src += '&celltype=' + celltype;
            if (celltype == 'custom')
            {
                // If celltype is set to custom, get the tracks from the current window
                if (annoj_frame.contentWindow.AnnoJ)
                {
                    var activeTracks = annoj_frame.contentWindow.AnnoJ.getGUI().TrackSelector.getActiveIDs()
                    var allTracks = annoj_frame.contentWindow.AnnoJ.config.tracks.map((tr) => tr['id']);
                    var activeTracksIndex = activeTracks.map((tr) => allTracks.indexOf(tr)).join(':');
                    if (activeTracksIndex)
                    {
                        src += '&tracks=' + activeTracksIndex;
                    };
                }
                else
                {
                    var activeTracksIndex = getQueryVariable('tracks');
                    src += '&tracks=' + activeTracksIndex;
                }
            }
        }

        return src
    }

    function myRefresh()
    {
        // Refresh the iframe containing AnnoJ
        src = buildURL();
        document.getElementById('annoj_frame').src = src;
    }

    function getLink()
    {
        // Update the URL in the window
        src = buildURL();
        src = src.replace('biccn_annoj.php', 'index.php');
        document.getElementById('srcBox').value = src;
        window.history.pushState("object or string", "", src);
    }

    function copyLink()
    {
        // Copy link to the current state to the clipboard

        /* Select the text field */
        var copySrc = document.getElementById('srcBox')
        copySrc.style.visibility = 'visible'; // The box must be visible to copy
        copySrc.select();
        copySrc.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");
        copySrc.style.visibility = 'hidden';
    }

    function setParams()
    {
        // Get parameters from URL and set the dropdowns
        // var params = ['ensemble','celltype','colorby','groupby'];
        var params = ['ensemble', 'celltype', 'groupby'],
        a;
        for (i = 0; i < params.length; i++)
        {
            a = getQueryVariable(params[i]);
            if (a)
            {
                document.getElementById('select_' + params[i]).value = a;
            }
        }
    }

    function toggleTrackSelectionDiv()
    {
        if (document.getElementById('select_celltype').value == 'custom')
        {
            var disp='none',disp2='block';
        }
        else
        {
            var disp = 'block',disp2='none';
        }
        var track_selection = document.getElementsByClassName('track-selection');
        for (j = 0; j < track_selection.length; j++)
        {
            track_selection[j].style.display = disp;
        }
        var custom_track_selection = document.getElementsByClassName('custom-track-selection');
        for (j = 0; j < custom_track_selection.length; j++)
        {
            custom_track_selection[j].style.display = disp2;
        }
    }

    function set_trackslist() {
    	// After the AnnoJ frame is loaded
        // Get tracks
        var tracks = document.getElementById('annoj_frame').contentWindow.AnnoJ.config.tracks;
        var active = document.getElementById('annoj_frame').contentWindow.AnnoJ.config.active;
        $('#select_tracks').empty(); // Empty the options
        var custom_track_selection = document.getElementById('select_tracks');
        tracks.sort(function(a,b) { return  a.name.localeCompare(b.name) + 1000*(active.includes(b.id) - active.includes(a.id)) }); // Sort the track list by name
        for (j=0; j<tracks.length; j++)
        {
            var option = document.createElement("option");
            option.text=tracks[j].name;
            if (active.includes(tracks[j].id)) { option.selected = true; }
            custom_track_selection.add(option);
        }
        $('.selectpicker').selectpicker('refresh');
    }

    </script>

</head>

<body>
    <nav class="navbar navbar-default" style="margin:0;">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <span class="glyphicon glyphicon-chevron-down" data-toggle="collapse" href="#topbar" title="Show/hide options" style="line-height: inherit; font-size:18;"></span>
                </li>
                <li class="nav-item">
                    <a href="http://biccn.org" target="_blank" style="padding: 0">
                        <img src="browser/img/logo_BICCN.png" height="34">
                    </a>
                </li>
                <li class="nav-item">
                    <button title="About the AnnoJ browser." onclick="window.open('https://brainome.ucsd.edu/howto_annoj.html');" class="btn btn-info"><i class="fa fa-question-circle" aria-hidden="true"></i> About
                    </button>
                </li>
                <li class="nav-item">
                    <button onclick="window.open('https://brainome.ucsd.edu/portal/Ens218');" title="Go to cell browser to view clusters and genes" class="btn btn-primary">
                        <img src="browser/img/Logo_ClearBg3.png" style="height:20px; padding:0px;"> Launch cell browser</button>
                    </li>
                    <li class="nav-item">
                        <button onclick="getLink(); copyLink();" class="btn btn-primary">
                            <i class="fa fa-link" aria-hidden="true"></i> Copy shareable link</button>
                            <input type="text" id="srcBox" size="1" style="visibility:hidden;">
                        </input>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="nav-item">
                        External resources:
                    </li>
                    <li class="nav-item">
                        <button onclick="window.open('http://www.mouseconnectome.org/Dinoskin/page/cemba');" title="Go to mouse connectome browser to view anatomy and connectivity data (Hongwei Dong lab)" class="btn btn-info">
                            <img src="browser/img/mouseconnectome.png" style="height:20px; padding: 0px;"> Anatomy browser
                        </button>
                    </li>
                    <li class="nav-item">
                        <button onclick="window.open('https://nemoanalytics.org');" title="Go to cell browser to view clusters and genes" class="btn btn-info">
                            <img src="browser/img/nemo-logo.svg" style="height:20px; padding:0px;"> NeMO analytics</button>
                        </li>
                    </ul>
                </div>
                <div class="collapse in" id="topbar">

                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <li class="nav-item">
                                <select id="select_celltype" class="selectpicker" data-width="auto" onchange="getLink(); toggleTrackSelectionDiv();" data-toggle="tooltip" data-placement="top" title="Cells to show" data-live-search="true">
                                    <option value="inh" selected>Cells to show...</option>
                                    <option value="all">All cell types</option>
                                    <option value="exc">Excitatory neurons</option>
                                    <option value="inh">Inhibitory neurons</option>
                                    <option value="mge">MGE-derived neurons</option>
                                    <option value="cge">CGE-derived neurons</option>
                                    <option value="none">None</option>
                                    <option data-divider="true"></option>
                                    <option value="custom">Custom track list</option>
                                </select>
                            </li>


                            <li class="nav-item track-selection">
                                <select id="select_ensemble" class="selectpicker" data-width="auto" onchange="getLink();" data-toggle="tooltip" data-placement="top" title="Cluster depth">
                                    <option value="2" selected>Cluster depth...</option>
                                    <option value="2">Level 1: Cell classes</option>
                                    <option value="3">Level 2: Cell types</option>
                                </select>
                            </li>
                            <li class="nav-item track-selection">
                                <select id="select_modalities" class="selectpicker" onchange="getLink();" data-width="auto" multiple data-width="auto" data-toggle="tooltip" data-placement="top" data-header="Modalities to show" data-actions-box="true">
                                    <option selected value="mcg" selected> mCG</option>
                                    <option value="mcac"> mCAC</option>
                                    <option value="atac" selected> ATAC</option>
                                    <option value="scrna" selected> scRNA</option>
                                    <option value="snrna"> snRNA</option>
                                    <option value="enhancer"> Enhancers</option>
                                </select>
                            </li>
                            <li class="nav-item track-selection">
                                <select id="select_groupby" class="selectpicker" data-width="auto" onchange="getLink();" data-toggle="tooltip" data-placement="top" title="Group tracks by">
                                    <option value="modality" selected>Group tracks by...</option>
                                    <option value="celltype">Cell type</option>
                                    <option value="modality">Modality</option>
                                </select>
                            </li>


                            <li class="nav-item custom-track-selection" style="display:none">
                                <select id="select_tracks" class="selectpicker" onchange="getLink();" data-width="auto" multiple data-width="auto" data-toggle="tooltip" data-placement="top" title="Tracks" data-actions-box="true" data-live-search="true" data-selected-text-format="count">
                                </select>
                            </li>

                            <li class="nav-item">
                                <p>
                                    <button onclick="myRefresh(); getLink();" class="btn btn-primary">Reload browser</button>
                                </p>
                            </li>

                        </ul>
                    </div>
                </div>

            </nav>

            <iframe id="annoj_frame" target="_blank" src="about:blank" width=100% height=100% onload="set_trackslist();">
                iframe
            </iframe>

        </body>

<script>
    $(document).ready(function ()
    {
        $('.glyphicon').click(function ()
        {
            $(this).toggleClass("glyphicon-chevron-down").toggleClass("glyphicon-chevron-up");
        });

        if (modalities)
        {
            $('#select_modalities').selectpicker('deselectAll');
            $('#select_modalities').selectpicker('val', modalities.split(':'));
        }

        setParams();
        // initURL();
        myRefresh();
        getLink();
        $('.selectpicker').selectpicker('refresh');
        
        toggleTrackSelectionDiv();

        set_trackslist();
    }); 
</script>

</html> 