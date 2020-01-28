/*
 * ------------------------------------------------------------------------------
 *
 *  About Anno-J Networked Genome Browser X, 7.0, 6.0 & 5.0 (aj-netX-min.js)
 *
 *  Allow functionalities on touchscreen devices. Build network connections among 
 *  individual Anno-J browser pages.   
 *
 *  Author: Huaming Chen (2017-2019)
 *
 * ------------------------------------------------------------------------------
 *
 *  About Anno-J browser version 4.5 (aj-min9-src.js), 4.0-4.1.2 (aj-min8-src.js)
 *  and 3.5-3.7 (aj-min5-src.js)
 *
 *  Downloads and Documents : http://signal.salk.edu/aj2/docs 
 *
 *  Author: Huaming Chen (2013-2017)
 *
 * ------------------------------------------------------------------------------
 *
 *  About Anno-J browser version 2.5 and 3.0 : http://tabit.ucsd.edu
 *
 *  Author: Tao Wang
 *
 * ------------------------------------------------------------------------------
 *
 *  About Anno-J browser version 2.0 : http://www.annoj.org  
 *
 *  Copyright (c) 2008 Julian Tonti-Filippini (tontij01(at)student.uwa.edu.au)
 *
 *  This work (Anno-J) is licensed under the Creative Commons
 *  License:  Attribution-Noncommercial-Share Alike 3.0 Unported
 *  License Overview: http://creativecommons.org/licenses/by-nc-sa/3.0/
 *  License Specifics: http://creativecommons.org/licenses/by-nc-sa/3.0/legalcode
 *  Commercial Purposes:  Contact the authors
 *
 * ------------------------------------------------------------------------------
 */
Math.step = function(x) {
    var b = x[0],
        p = x[1],
        closer = x[2];
    if (b > p) {
        var f = Math.pow(10, Math.round((b + '').length) - 1);
        var d = b / f;
        if (closer) {
            b = (d == 1) ? b - f / 10 : b - f
        } else {
            b = b + f
        }
    } else if (b < p) {
        var f = Math.pow(10, Math.round((p + '').length) - 1);
        var d = p / f;
        if (closer) {
            p = p + f
        } else {
            p = (d == 1) ? p - f / 10 : p - f
        }
    } else {
        closer ? p++ : b++
    }
    return [b, p, closer];
}
var rColor = function(s) {
    if (s.indexOf("#") == 0) s = s.substring(1);
    var sR = s.substring(0, 2);
    var sG = s.substring(2, 4);
    var sB = s.substring(4);
    sR = 255 - parseInt(sR, 16);
    sG = 255 - parseInt(sG, 16);
    sB = 255 - parseInt(sB, 16);
    var nR = sR.toString(16);
    var nG = sG.toString(16);
    var nB = sB.toString(16);
    if (nR.length == 1) nR = '0' + nR;
    if (nG.length == 1) nG = '0' + nG;
    if (nB.length == 1) nB = '0' + nB;
    return "#" + nR + nG + nB
};
var isColor = function(s) {
    var el = document.createElement("div");
    el.style.color = s;
    return el.style.color.split(/\s+/).join('').toLowerCase()
};
var toColorArr = function(s) {
    var el = document.createElement("div");
    el.style.color = s;
    document.body.appendChild(el);
    var color = window.getComputedStyle(el).color;
    color = color.replace(/[^\d,]/g, '').split(',');
    document.body.removeChild(el);
    return color
};
var toColorSim = function(a, b) {
    var r = Math.abs(a[0] - b[0]);
    var g = Math.abs(a[1] - b[1]);
    var b = Math.abs(a[2] - b[2]);
    return Math.round(r * 0.3 + g * 0.50 + b * 0.20)
};
var toColorSimIndex = function(a, arr) {
    var index = -1;
    if (arr[0].constructor === Array) {
        var min = 256;
        for (var i = 0; i < arr.length; i++) {
            sim = toColorSim(a, arr[i]);
            if (sim < min) {
                min = sim;
                index = i
            }
        }
    }
    return index
};
var rgbColor = function(c, h) {
    var colors = {
        red: [0, 1, 1],
        blue: [1, 1, 0],
        green: [1, 0, 1],
        black: [1, 1, 1],
        purple: [0, 1, 0],
        yellow: [0, 0, 1],
        cyan: [1, 0, 0],
        'red-blue': [0, 1, 1, 1, 1, 0]
    };
    if (!colors.hasOwnProperty(c)) c = 'red';
    var r, g, b;
    if (colors[c].length == 6) {
        if (h < 128) {
            h = 255 - h * 2;
            r = 255 - h * colors[c][3];
            g = 255 - h * colors[c][4];
            b = 255 - h * colors[c][5];
        } else {
            h = (h - 128) * 2;
            r = 255 - h * colors[c][0];
            g = 255 - h * colors[c][1];
            b = 255 - h * colors[c][2];
        }
    } else {
        r = 255 - h * colors[c][0];
        g = 255 - h * colors[c][1];
        b = 255 - h * colors[c][2];
    }
    return 'rgb(' + r + ',' + g + ',' + b + ')';
};

function find(arr, param, value) {
    var hit = null;
    Ext.each(arr, function(item) {
        if (item[param] && item[param] == value) {
            hit = item;
            return false
        }
    });
    return hit
};
var findConf = function(id) {
    var track = find(AnnoJ.config.tracks, 'id', id);
    if (!track) track = AnnoJ.config.infoTrack;
    return track
};
var sortObj = function(obj, az) {
    var ordered = {};
    if (az) {
        Object.keys(obj).sort().forEach(function(key) {
            if (key !== 'coverage') ordered[key] = obj[key];
        });
    } else {
        Object.keys(obj).sort().reverse().forEach(function(key) {
            if (key !== 'coverage') ordered[key] = obj[key];
        });
    }
    if (obj['coverage']) ordered['coverage'] = obj['coverage'];
    return ordered
};
var toggleShow = function(event, config) {
    var self = config;
    var event = event || window.event || arguments[0];
    if (self.btnCmp) {
        if (!self.btnCmp.isVisible()) {
            if (event.type == 'click') {
                self.btnCmp.show();
                if (self.ctrlDIV) self.ctrlDIV.innerHTML = self.ctrlHide;
            } else if (event.type == 'mouseover') {
                var img = self.ctrlDIV.getElementsByTagName('IMG')[0];
                img.src = self.ctrlHideOverImg;
            } else if (event.type == 'mouseout') {
                if (self.ctrlDIV) self.ctrlDIV.innerHTML = self.ctrlShow;
            }
        } else {
            if (event.type == 'click') {
                self.btnCmp.hide();
                if (self.ctrlDIV) self.ctrlDIV.innerHTML = self.ctrlShow;
            } else if (event.type == 'mouseover') {
                var img = self.ctrlDIV.getElementsByTagName('IMG')[0];
                img.src = self.ctrlShowOverImg;
            } else if (event.type == 'mouseout') {
                if (self.ctrlDIV) self.ctrlDIV.innerHTML = self.ctrlHide;
            }
        }
    }
};
var openWindowWithPost = function(url, data) {
    var form = document.createElement('form');
    form.target = '_blank';
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';
    for (var key in data) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = data[key];
        form.appendChild(input)
    }
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};
var aj2 = (function() {
    var scriptSource = (function() {
        var script = document.currentScript;
        return script.getAttribute('src', 2);
    }());
    return scriptSource.split("/js/")[0];
}());
Array.prototype.remove = function(item) {
    for (var i = 0, len = this.length; i < len; i++) {
        if (this[i] == item) {
            this.splice(i, 1);
            return true
        }
    }
    return false
};
Array.prototype.insert = function(index, item) {
    var index = parseInt(index) || null;
    if (index == null || index >= this.length || index < 0) {
        this.push(item);
        return
    }
    this.splice(index, 0, item)
};
Array.prototype.search = function(item) {
    for (var i = 0, len = this.length; i < len; i++) {
        if (this[i] == item) {
            return i
        }
    }
    return -1
};
Array.prototype.unique = function() {
    var tmp = {},
        out = [];
    for (var i = 0, n = this.length; i < n; ++i) {
        if (!tmp[this[i]]) {
            tmp[this[i]] = true;
            out.push(this[i]);
        }
    }
    return out;
};
var setPolicy = function(policies, policy) {
    var ps = policies;
    var pn = policy.split(",");
    for (var i = 0, n = pn.length; i < n; i++) {
        var pz = pn[i].split(":");
        if (pz[0] == undefined || pz[1] == undefined) continue;
        for (var j = 0; j < ps.length; j++) {
            if (pz[0] == ps[j].bases && pz[1] == ps[j].pixels) {
                if (pz[2] != undefined && (pz[2] == 10 || pz[2] == 100)) ps[j].cache *= pz[2];
                if (pz[3] != undefined && pz[3] > ps[j].max) ps[j].max = pz[3];
                break;
            }
        }
    }
    return ps;
};
var isInfo = function(id) {
    if (id.indexOf('trackxxxx-') != -1)
        return true;
    else return false
};
if (!Ext) {
    var html = "<div style='margin:auto; width:600px; border:solid black 1px; padding:15px; margin-top:100px; font-family:arial; font-size:13px;'>";
    html += "<h1>Error: Ext not found</h1><br />";
    html += "<p>This application could not find the ExtJS Javascript libraries and consequently, cannot run.</p>";
    html += "<ul style='padding:10px; list-style:circle; font-size:12px;'>";
    html += "<li>Check that ExtJS libraries are included in the document &lt;head&gt; section</li>";
    html += "<li>Check that ExtJS libraries are included before this error checking routine</li>";
    html += "<li>Check that ExtJS libraries are being referenced from a correct URL</li>";
    html += "<li>Check that your internet connection is active</li>";
    html += "</ul>";
    html += "<p>Please notify your website administrator of this problem so that it may be fixed.</p>";
    html += "<p>ExtJS is available from <a href='http://www.extjs.com'>http://www.extjs.com</a></p>";
    html += "<p><a href='http://www.extjs.com/download'><img src='img/extjs.png' alt='Get ExtJS 2' /></a>";
    html += "</div>";
    window.onload = function() {
        document.body.innerHTML = html
    }
} else {
    Ext.Ajax.timeout = 600000;
    Ext.QuickTips.init();
}
var WebApp = (function() {
    function checkBrowser() {
        if (Ext.isGecko) {
            return true
        } else if (Ext.isIE) {
            var canvas = document.createElement("canvas");
            if (canvas.getContext) Ext.isIE9 = true;
            else Ext.isIE8 = true;
            return true
        } else if (Ext.isChrome) {
            return true
        } else if (Ext.isOpera) {
            return true
        } else if (Ext.isSafari) {
            return true
        } else {
            return false
        }
    };

    function alert(message, type) {
        switch (type) {
            case 'ERROR':
                break;
            case 'WARNING':
                break;
            default:
                type = 'INFO'
        };
        Ext.Msg.show({
            title: type,
            msg: message,
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox[type]
        })
    };

    function exception(e, message) {
        if (!Ext.isIE && console && console.log) console.log(e);
        if (!message) var message = 'An exception was encountered.';
        error(message + '<br /><br />Details:<br />File: ' + e.fileName + '<br />Line: ' + e.lineNumber + '<br />Info: ' + e.message)
    };

    function error(message) {
        alert(message, 'ERROR')
    };

    function warning(message) {
        alert(message, 'WARNING')
    };

    function notice(message) {
        alert(message, 'INFO')
    };
    return {
        checkBrowser: checkBrowser,
        alert: alert,
        exception: exception,
        error: error,
        warning: warning,
        notice: notice
    }
})();
var BaseJS = (function() {
    var emptyFunction = function() {};
    var defaultSyndication = {
        institution: {
            name: '',
            url: '',
            logo: ''
        },
        engineer: {
            name: '',
            email: ''
        },
        service: {
            title: '',
            version: '',
            description: '',
            request: {
                type: '',
                format: '',
                schema: ''
            },
            response: {
                type: '',
                format: '',
                schema: ''
            }
        }
    };
    var defaultRequest = {
        url: '',
        data: null,
        method: 'GET',
        success: emptyFunction,
        failure: emptyFunction,
        requestJSON: true,
        receiveJSON: true
    };

    function syndicate(params) {
        Ext.applyIf(params || {}, defaultRequest);
        request({
            url: params.url || '',
            data: {
                action: 'syndicate',
                table: params.table
            },
            requestJSON: false,
            receiveJSON: true,
            success: function(response) {
                Ext.applyIf(response.data || {}, defaultSyndication);
                params.success(response.data)
            },
            failure: params.failure || emptyFunction
        })
    };

    function syndicationToHTML(syndication) {
        var s = {};
        Ext.apply(s, syndication || {}, defaultSyndication);
        var html = "<div style='padding:2px;'>";
        html += "<div><a href='" + s.institution.url + "'><img src='" + s.institution.logo + "' alt='Data provider institutional logo' /></a></div>";
        html += "<div><b>Provider: </b><a href='" + s.institution.url + "'>" + s.institution.name + "</a></div>";
        html += "<div><b>Contact: </b><a href='mailto:" + s.engineer.email + "'>" + s.engineer.name + "</a></div>";
        html += "<hr />";
        html += "<div><b>" + s.service.title + "</b></div>";
        html += "<div>" + s.service.description + "</div>";
        return html + "</div>"
    };

    function objectToHTML(obj) {
        var html = '<ul>';
        for (var param in obj) {
            if (!obj.hasOwnProperty(param)) continue;
            if (typeof(obj[param]) == 'string') {
                html += '<li><b>' + param + ':</b> ' + obj[param] + '</li>'
            } else {
                html += objectToHTML(obj[param])
            }
        }
        return html + '</ul>'
    };

    function request(params) {
        Ext.applyIf(params || {}, defaultRequest);
        if (!params.url) return;
        if (params.method != 'GET' && params.method != 'POST') return;
        if (params.requestJSON) {
            params.data = {
                request: Ext.util.JSON.encode(params.data)
            }
        }
        Ext.Ajax.request({
            url: params.url,
            method: params.method,
            params: params.data,
            failure: function(response, options) {
                params.failure('Communication error: ' + response.responseText)
            },
            success: function(response, options) {
                if (!response) {
                    params.failure('Server error: no response');
                    return
                }
                if (params.receiveJSON) {
                    try {
                        response = Ext.util.JSON.decode(response.responseText);
                    } catch (ex) {
                        params.failure('Illegal JSON string: ' + response.responseText);
                        return
                    }
                    if (response.success == false) {
                        params.failure('Server error: ' + (response.message || 'unspecified server error'));
                        return
                    }
                    params.success(response)
                } else {
                    params.success(response.responseText)
                }
            }
        })
    };
    return {
        syndicate: syndicate,
        toHTML: syndicationToHTML,
        request: request
    }
})();
var tokens = 0;
var token_ready = true;
var cursor = {
    id: '',
    type: '',
    offsetTop: '',
    offsetHeight: ''
};
var InfoRequest = {
    ready: false,
    position: 0,
    bases: 10,
    pixels: 1,
    corr: []
};
var AnnoJ = (function() {
    var defaultConfig = {
        tracks: [],
        active: [],
        genome: '',
        sequence: '',
        bookmarks: '',
        styles: [],
        location: {
            assembly: '3',
            position: 15678,
            bases: 20,
            pixels: 1
        },
        admin: {
            name: '',
            email: '',
            notes: ''
        },
        cls: 'tracks',
        alignX: [],
        meanX: [],
        showApps: 0,
        jbuilder: 0,
        citation: ''
    };
    var config = defaultConfig;
    var GUI = {};
    var isReady = false;

    function init() {
        Ext.MessageBox.progress('Building');
        Ext.MessageBox.updateProgress(0.05, '', 'Checking browser...');
        if (!WebApp.checkBrowser()) {
            Ext.MessageBox.hide();
            return false
        }
        Ext.MessageBox.updateProgress(0.10, '', 'Applying configuration...');
        if (!AnnoJ.config) {
            Ext.MessageBox.hide();
            WebApp.error('Failed to load AnnoJ.config object.');
            return false
        }
        Ext.apply(config, AnnoJ.config || {}, defaultConfig);
        if (!AnnoJ.config.settings) AnnoJ.config.settings = {};
        Ext.each(AnnoJ.config.tracks, function(track) {
            if (!track.scale) track.scale = 1;
            if (!track.color || typeof(track.color) == 'string') track.color = {};
            if (track.type == 'HiCTrack') {
                if (!track.assembly) track.assembly = '1';
                if (!track.position) track.position = AnnoJ.config.location.position || 0;
                if (!track.style) track.style = 0;
                if (!track.unity) track.unity = 4;
                if (!track.offsety) track.offsety = 0
            }
        });
        if (!AnnoJ.config.maxlist) AnnoJ.config.maxlist = new Array();
        if (!AnnoJ.config.alignX) AnnoJ.config.alignX = new Array();
        if (!AnnoJ.config.meanX) AnnoJ.config.meanX = new Array();
        if (!AnnoJ.config.infoTrack) AnnoJ.config.infoTrack = {};
        Ext.MessageBox.updateProgress(0.15, '', 'Building GUI...');
        try {
            GUI = buildGUI()
        } catch (e) {
            Ext.MessageBox.hide();
            WebApp.exception(e, 'An exception occured when initializing graphical user interface.');
            return false
        };
        GUI.notice('Browser check passed', true);
        GUI.notice('Configuration loaded');
        GUI.notice('GUI constructed');
        Ext.MessageBox.updateProgress(0.3, '', 'Loading stylesheets...');
        GUI.notice('Stylesheets loaded');
        Ext.MessageBox.updateProgress(0.4, '', 'Syndicating genome...');
        Ext.MessageBox.updateProgress(0.5, '', 'Building ' + AnnoJ.config.tracks.length + ' tracks, please wait...');
        GUI.NavBar.syndicate({
            url: config.genome,
            success: function(response) {
                GUI.notice('Genome syndicated');
                GUI.NavBar.setLocation(config.location);
                buildTracks();
                GUI.notice('Tracks instantiated');
                Ext.MessageBox.updateProgress(1.0, '', 'Finished.');
                Ext.MessageBox.hide();
                AnnoJ.isReady = true
            },
            failure: function(string) {
                Ext.MessageBox.hide();
                error('Unable to load genomic metadata from address: ' + config.genome);
                Ext.MessageBox.alert('Error', 'Unable to load genomic metadata from address: ' + config.genome)
            }
        })
    };

    function buildTracks() {
        Ext.each(config.tracks, function(trackConfig, index) {
            try {
                var track;
                if (AnnoJ.config.active.indexOf(trackConfig.id) != -1)
                    track = new AnnoJ[trackConfig.type](trackConfig);
                else track = new AnnoJ.ProxyTrack(trackConfig);
            } catch (e) {
                config.tracks[index] = null;
                WebApp.error(e);
                if (!Ext.isIE) console.log(e);
                return
            };
            GUI.Tracks.tracks.manage(track);
            GUI.TrackSelector.manage(track);
        });
        GUI.TrackSelector.expand();
        GUI.TrackSelector.active.expand();
        GUI.TrackSelector.inactive.expand();
        Ext.each(GUI.Tracks.tracks.tracks, function(track) {
            if (!track.isProxy) {
                track.on('describe', function(syndication) {
                    GUI.InfoBox.echo(BaseJS.toHTML(syndication));
                })
            }
        });
        Ext.each(config.active, function(id) {
            var track = GUI.Tracks.tracks.find('id', id);
            if (track) {
                GUI.TrackSelector.activate(track);
                GUI.Tracks.tracks.open(track)
            }
        });
    };

    function buildGUI() {
        var Messenger = new AnnoJ.Messenger();
        var TrackSelector = new AnnoJ.TrackSelector({
            structure: config.structure,
            activeTracks: config.active
        });
        var Bookmarker = new AnnoJ.Bookmarker({
            datasource: config.bookmarks || aj2 + '/includes/common_bookmarks.php'
        });
        var SignOn = new AnnoJ.SignOn({
            datasource: config.bookmarks || aj2 + '/includes/common_bookmarks.php'
        });
        var Settings = new AnnoJ.Settings();
        var InfoBox = new AnnoJ.InfoBox();
        var AboutBox = new AnnoJ.AboutBox({
            admin: config.admin
        });
        var NavBar = new AnnoJ.Navigator();
        var InfoBar = new AnnoJ.InfoToolBar();
        var Tracks = new AnnoJ.Tracks({
            tbar: NavBar.ext,
            tracks: config.tracks,
            activeTracks: config.active
        });
        var TracksInfo = new AnnoJ.TracksInfo({
            region: 'south',
            height: screen.height / 2,
            tbar: InfoBar.ext,
            tracks: [],
            activeTracks: []
        });
        var citations = '';
        if (config.citation) {
            citations += config.citation
        }
        if (config.jbuilder) {
            citations += "<div style=\"padding:5px;\">This page's AnnoJ.config is built by <i>jBuilder</i> " + config.jbuilder + ". </div><br>";
        }
        if (citations != '') {
            AboutBox.addCitation(citations)
        }
        SignOn.manage();
        SignOn.hide();
        Bookmarker.load();
        var Accordion = new Ext.Panel({
            title: 'Configuration Box',
            region: 'north',
            layout: 'accordion',
            iconCls: 'silk_wrench',
            collapsible: true,
            split: true,
            minSize: 160,
            width: 240,
            height: window.innerHeight - 28,
            maxSize: 400,
            margins: '0 0 0 0',
            header: false,
            layoutConfig: {
                animate: true
            },
            items: [SignOn, AboutBox, Settings, TrackSelector, InfoBox, Bookmarker, Messenger]
        });
        var Container = new Ext.Panel({
            title: 'Configuration',
            region: 'west',
            iconCls: 'silk_wrench',
            collapsible: true,
            split: true,
            width: 240,
            maxSize: 400,
            margins: '0 0 0 0',
            collapseMode: 'header',
            layoutConfig: {
                animate: true
            },
            items: Accordion
        });
        if (AnnoJ.config.settings.accordion == 'collapsed') {
            Container.collapsed = true;
        } else if (AnnoJ.config.settings.accordion == 'hide') {
            Container.hidden = true
        }
        var toggleExpand = function(item, next = false) {
            var itemz, items = Accordion.items.items;
            for (var i = 0; i < items.length; i++) {
                if (items[i] !== item) {
                    if (items[i].isVisible() && items[i].collapsed && !itemz)
                        itemz = items[i];
                    items[i].collapse();
                }
            }
            if (next && !item.collapsed && itemz) itemz.expand();
            else item.expand(true)
        }
        var Viewport = new Ext.Viewport({
            layout: 'border',
            items: [Container, Tracks]
        });
        NavBar.on('describe', function(syndication) {
            InfoBox.echo(BaseJS.toHTML(syndication));
            InfoBox.expand()
        });
        NavBar.on('browse', Tracks.tracks.setLocation);
        NavBar.on('dragModeSet', Tracks.setDragMode);
        Tracks.on('dragModeSet', NavBar.setDragMode);
        TrackSelector.on('openTrack', Tracks.tracks.open);
        TrackSelector.on('moveTrack', Tracks.tracks.reorder);
        TrackSelector.on('closeTrack', Tracks.tracks.myclose);
        Settings.hide();

        function alert(message, type, important) {
            Messenger[type](message);
            if (important) {
                Messenger.expand();
                Accordion.expand()
            }
        };

        function error(message, important) {
            alert(message, 'error', important)
        };

        function warning(message, important) {
            alert(message, 'warning', important)
        };

        function notice(message, important) {
            alert(message, 'notice', important)
        };
        return {
            Messenger: Messenger,
            TrackSelector: TrackSelector,
            Settings: Settings,
            Bookmarker: Bookmarker,
            InfoBox: InfoBox,
            AboutBox: AboutBox,
            SignOn: SignOn,
            NavBar: NavBar,
            Tracks: Tracks,
            TracksInfo: TracksInfo,
            Accordion: Accordion,
            Viewport: Viewport,
            Container: Container,
            toggleExpand: toggleExpand,
            alert: alert,
            error: error,
            warning: warning,
            notice: notice
        }
    };

    function getLocation() {
        return GUI.NavBar.getLocation()
    };

    function setLocation(location) {
        return GUI.NavBar.setLocation(location)
    };

    function alert(message, type) {
        GUI.Messenger.alert(message, type)
    };

    function error(message) {
        GUI.Messenger.error(message)
    };

    function warning(message) {
        GUI.Messenger.warning(message)
    };

    function notice(message) {
        GUI.Messenger.notice(message)
    };

    function pixels2bases(pixels) {
        return GUI.NavBar.pixels2bases(pixels)
    };

    function bases2pixels(bases) {
        return GUI.NavBar.bases2pixels(bases)
    };

    function xpos2gpos(xpos) {
        return GUI.NavBar.xpos2gpos(xpos)
    };

    function gpos2xpos(gpos) {
        return GUI.NavBar.gpos2xpos(gpos)
    };

    function getGUI() {
        return GUI
    };
    return {
        ready: true,
        init: init,
        alert: alert,
        isReady: isReady,
        error: error,
        warning: warning,
        notice: notice,
        getLocation: getLocation,
        setLocation: setLocation,
        pixels2bases: pixels2bases,
        bases2pixels: bases2pixels,
        xpos2gpos: xpos2gpos,
        gpos2xpos: gpos2xpos,
        getGUI: getGUI,
        Plugins: {},
        Helpers: {}
    }
})();
if (Ext) {
    Ext.onReady(AnnoJ.init);
}
AnnoJ.TrackSelector = (function() {
    var root = new Ext.tree.TreeNode({
        text: 'Tracks',
        allowDrag: false,
        allowDrop: false
    });
    var active = new Ext.tree.TreeNode({
        text: 'Active Tracks',
        allowDrag: false,
        allowDrop: true,
        leaf: false,
        expandable: true,
        expanded: true
    });
    var inactive = new Ext.tree.TreeNode({
        text: 'Inactive Tracks',
        allowDrag: false,
        allowDrop: true,
        leaf: false,
        expandable: true,
        expanded: true
    });
    root.appendChild(active);
    root.appendChild(inactive);

    function manage(track) {
        if (!track instanceof AnnoJ.BaseTrack && !track.isProxy) return;
        var parent = importPath(track.config.path);
        var node = new Ext.tree.TreeNode({
            id: 'tree_' + track.config.id,
            text: track.config.name,
            iconCls: track.config.iconCls,
            allowDrag: true,
            allowDrop: false,
            leaf: true
        });
        node.originalParent = parent;
        node.track = track;
        track.node = node;
        parent.appendChild(node);
        if (track instanceof AnnoJ.BaseTrack) track.on('close', inactivate)
    };

    function unmanage(track) {
        if (!track instanceof AnnoJ.Tracks) return;
        if (!track.node) return;
        track.node.remove();
        node.track = null;
        delete track.node;
        track.un('close', inactivate)
    };

    function inactivate(track) {
        if (track.config.id.indexOf('new-') >= 0) {
            var child = active.findChild('text', track.config.name);
            active.removeChild(child)
        } else track.node.originalParent.appendChild(track.node)
    };

    function inactivateAll() {
        Ext.each(active.childNodes, function(child) {
            inactivate(child.track)
        })
    };

    function activate(track) {
        active.appendChild(track.node)
    };

    function inactivatePath(node) {
        Ext.each(active.childNodes, function(child) {
            if (child == null) return;
            if (child.originalParent == node) {
                inactivate(child.track);
                return inactivatePath(node);
            }
        });
    };

    function activatePath(node) {
        Ext.each(node.childNodes, function(child) {
            if (child && child.isLeaf()) {
                active.appendChild(child);
                activatePath(node);
            }
        });
    };

    function insertBefore(track, before) {
        var child = active.findChild('text', before);
        active.insertBefore(track.node, child)
    };

    function importPath(path) {
        if (this.path == path) return this.parent;
        var dirs = path.split('/');
        var parent = inactive;
        Ext.each(dirs, function(dir) {
            var child = parent.findChild('text', dir);
            if (!child) {
                child = new Ext.tree.TreeNode({
                    text: dir,
                    allowDrag: false,
                    allowDrop: true,
                    leaf: false
                });
                parent.appendChild(child);
            }
            parent = child
        });
        this.path = path;
        this.parent = parent;
        return parent
    };

    function getActive() {
        var list = [];
        Ext.each(active.childNodes, function(child) {
            list.push(child.track)
        });
        return list
    };

    function getActiveIDs() {
        var list = [];
        Ext.each(active.childNodes, function(child) {
            list.push(child.track.config.id)
        });
        return list
    };
    return function(userConfig) {
        var defaultConfig = {
            title: 'Track Selection',
            iconCls: 'silk_package',
            border: false,
            autoScroll: true,
            ddScroll: true,
            enableDD: true,
            rootVisible: false,
            singleExpand: false,
            structure: [],
            activeTracks: []
        };
        var config = defaultConfig;
        Ext.apply(config, userConfig || {}, defaultConfig);
        AnnoJ.TrackSelector.superclass.constructor.call(this, config);
        this.addEvents({
            'openTrack': true,
            'closeTrack': true,
            'moveTrack': true
        });
        this.setRootNode(root);
        this.on('movenode', function(tree, node, oldParent, newParent, index, event) {
            if (oldParent == active) {
                if (newParent == active) {
                    this.fireEvent('moveTrack', node.track, node.nextSibling ? node.nextSibling.track : null)
                } else {
                    node.originalParent.appendChild(node);
                    this.fireEvent('closeTrack', node.track)
                }
            } else {
                if (newParent == active) {
                    this.fireEvent('openTrack', node.track, node.nextSibling ? node.nextSibling.track : null)
                } else if (node.originalParent && newParent != node.originalParent) {
                    node.originalParent.appendChild(node)
                } else {
                    return
                }
            }
        });
        if ('ontouchstart' in window) var clickNode = {};
        this.on(('ontouchstart' in window) ? 'click' : 'dblclick', function(node) {
            if ('ontouchstart' in window) {
                if (!clickNode.node || (event.timeStamp - clickNode.time) > 2100) {
                    clickNode.node = node;
                    clickNode.time = event.timeStamp;
                    return
                }
                if (clickNode.node != node) {
                    if (active == node.parentNode && node.parentNode == clickNode.node.parentNode) {
                        this.insertBefore(clickNode.node.track, node.track.config.name);
                        clickNode.node.ui.highlight();
                        clickNode.node = undefined;
                        return
                    } else if (node == active && clickNode.node.parentNode != active && clickNode.node.isLeaf()) {
                        active.appendChild(clickNode.node);
                        this.fireEvent('openTrack', clickNode.node.track, clickNode.node.nextSibling ? clickNode.node.nextSibling.track : null);
                        clickNode.node = undefined;
                        return
                    } else if (node == inactive && clickNode.node.parentNode == active) {
                        clickNode.node.originalParent.appendChild(clickNode.node);
                        this.fireEvent('closeTrack', clickNode.node.track);
                        clickNode.node = undefined;
                        return
                    }
                    clickNode.node = node;
                    clickNode.time = event.timeStamp;
                    return
                } else clickNode.node = undefined;
            }
            if (node.isLeaf()) {
                if (node.parentNode != active) {
                    active.appendChild(node);
                    this.fireEvent('openTrack', node.track, node.nextSibling ? node.nextSibling.track : null)
                } else {
                    node.originalParent.appendChild(node);
                    this.fireEvent('closeTrack', node.track)
                }
            } else {
                if (AnnoJ.config.settings.activate) {
                    if (!node.hasChildNodes()) {
                        inactivatePath(node);
                    } else {
                        var hasFolder = false;
                        Ext.each(node.childNodes, function(child) {
                            if (!child.isLeaf()) {
                                hasFolder = true;
                            }
                        });
                        if (!hasFolder) activatePath(node);
                    }
                }
            }
        });
        this.manage = manage;
        this.activate = activate;
        this.inactivate = inactivate;
        this.insertBefore = insertBefore;
        this.active = active;
        this.inactive = inactive;
        this.getActiveIDs = getActiveIDs;
        this.inactivateAll = inactivateAll
    }
})();
Ext.extend(AnnoJ.TrackSelector, Ext.tree.TreePanel);
AnnoJ.Navigator = function() {
    var self = this;
    this.addEvents({
        'browse': true,
        'describe': true,
        'dragModeSet': true
    });
    var Syndicator = (function() {
        var syndication = {};

        function syndicate(params) {
            if (!params.url) {
                if (params.failure) params.failure('Unable to syndicate as no URL was provided');
                return
            }
            BaseJS.syndicate({
                url: params.url,
                success: function(response) {
                    syndication = response;
                    Controls.setTitle(syndication.service.title);
                    if (syndication.genome.assemblies) {
                        Controls.bindAssemblies(syndication.genome.assemblies, AnnoJ.config.location.assembly);
                        AnnoJ.config.assemblies = syndication.genome.assemblies
                    }
                    if (params.success) {
                        params.success(response)
                    }
                },
                failure: function(string) {
                    if (params.failure) params.failure(string)
                }
            })
        };

        function get() {
            return syndication
        };
        return {
            get: get,
            syndicate: syndicate
        }
    })();
    var Navigator = (function() {
        var location = {
            assembly: '',
            position: 0,
            bases: 10,
            pixels: 1
        };
        var Assembly = (function() {
            var defaultConfig = {
                selected: '',
                options: [],
                verbose: false
            };
            var config = defaultConfig;

            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.selected || userConfig.options[0].id || '')
            };

            function set(value) {
                if (!value) return false;
                var valid = false;
                Ext.each(config.options, function(option) {
                    if (option.id == value) {
                        valid = true;
                        config.selected = value;
                        AnnoJ.config.location.assembly = value;
                        location.assembly = value;
                        Position.init({
                            min: 1,
                            max: option.size,
                            value: Position.get()
                        });
                        return false
                    }
                });
                if (config.verbose && !valid) AnnoJ.warning('Illegal assembly value selected (' + value + ')');
                return valid
            };

            function get() {
                return config.selected
            };
            return {
                init: init,
                get: get,
                set: set
            }
        })();
        var Position = (function() {
            var defaultConfig = {
                min: 0,
                max: 0,
                position: 0,
                verbose: false
            };
            var config = defaultConfig;
            var atMin = false;
            var atMax = false;

            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.position)
            };

            function get() {
                return config.position
            };

            function set(gpos) {
                var gpos = parseInt(gpos || 1);
                atMin = false;
                atMax = false;
                if (gpos <= config.min) {
                    gpos = config.min;
                    atMin = true;
                    if (config.verbose) AnnoJ.notice('At minimum position of assembly ' + Assembly.get())
                }
                if (gpos >= config.max) {
                    gpos = config.max;
                    atMax = true;
                    if (config.verbose) AnnoJ.notice('At maximum position of assembly ' + Assembly.get())
                }
                config.position = gpos;
                location.position = gpos
            };
            return {
                init: init,
                get: get,
                set: set,
                atMax: function() {
                    return atMax
                },
                atMin: function() {
                    return atMin
                }
            }
        })();
        var Zoom = (function() {
            var defaultConfig = {
                max: AnnoJ.config.settings.max || 200000,
                min: AnnoJ.config.settings.min || 0.05,
                bases: 10,
                pixels: 1,
                verbose: false
            };
            var config = defaultConfig;
            var atMin = false;
            var atMax = false;

            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.bases, config.pixels)
            };

            function scale(multiplier) {
                if (!multiplier) return;
                var bases = multiplier > 0 ? config.bases * multiplier : config.bases;
                var pixels = multiplier > 0 ? config.pixels : config.pixels * -multiplier;
                set(bases, pixels)
            };

            function step(closer) {
                var x = Math.step([config.bases, config.pixels, closer]);
                set(x[0], x[1])
            };

            function get() {
                return {
                    bases: config.bases,
                    pixels: config.pixels
                }
            };

            function set(bases, pixels) {
                if (!bases || !parseInt(bases)) bases = 1;
                if (!pixels || !parseInt(pixels)) pixels = 1;
                bases = parseInt(bases);
                pixels = parseInt(pixels);
                atMin = false;
                atMax = false;
                var ratio = bases / pixels;
                if (ratio >= config.max) {
                    bases = config.max;
                    pixels = 1;
                    if (bases < 1) {
                        pixels = Math.round(1 / bases);
                        bases = 1
                    }
                    atMax = true
                } else if (ratio <= config.min) {
                    bases = 1;
                    pixels = Math.round(1 / config.min);
                    if (pixels == 0) {
                        bases = Math.round(config.min);
                        pixels = 1
                    }
                    atMin = true
                } else {
                    if (bases > pixels) {
                        bases = Math.round(bases / pixels);
                        pixels = 1;
                        var f = Math.pow(10, Math.round((bases + '').length) - 1);
                        bases = f * Math.round(bases / f)
                    } else {
                        pixels = Math.round(pixels / bases);
                        bases = 1;
                        var f = Math.pow(10, Math.round((pixels + '').length) - 1);
                        pixels = f * Math.round(pixels / f)
                    }
                }
                config.bases = bases;
                config.pixels = pixels;
                location.bases = bases;
                location.pixels = pixels
            };
            return {
                init: init,
                scale: scale,
                step: step,
                set: set,
                atMax: function() {
                    return atMax
                },
                atMin: function() {
                    return atMin
                }
            }
        })();

        function getLocation() {
            return location
        };

        function setLocation(view) {
            var view = Ext.apply({}, view || {}, location);
            Assembly.set(view.assembly);
            Position.set(view.position);
            Zoom.set(view.bases, view.pixels);
            Controls.refreshControls();
            return location
        };

        function step(closer) {
            Zoom.step(closer)
        };

        function scale(multiplier) {
            if (!multiplier || !parseFloat(multiplier)) return;
            Zoom.scale(multiplier)
        };

        function bump(bases) {
            if (!bases || !parseInt(bases)) return;
            Position.set(location.position + parseInt(bases))
        };

        function pixels2bases(pixels) {
            if (!pixels || !parseInt(pixels)) return 0;
            return Math.round(parseInt(pixels) * location.bases / location.pixels)
        };

        function bases2pixels(bases) {
            if (!bases || !parseInt(bases)) return 0;
            return Math.round(parseInt(bases) * location.pixels / location.bases)
        };

        function xpos2gpos(xpos) {
            var edges = getEdges();
            return pixels2bases(xpos) + edges.g1
        };

        function gpos2xpos(gpos) {
            var edges = getEdges();
            return bases2pixels(gpos) - edges.x1
        };

        function getEdges() {
            var halfX = Math.round(Tbar.getBox().width / 2);
            var halfG = pixels2bases(halfX);
            var locG = location.position;
            var locX = bases2pixels(locG);
            return {
                g1: locG - halfG,
                g2: locG + halfG,
                x1: locX - halfX,
                x2: locX + halfX
            }
        };
        return {
            Assembly: Assembly,
            Position: Position,
            Zoom: Zoom,
            getLocation: getLocation,
            setLocation: setLocation,
            scale: scale,
            bump: bump,
            step: step,
            pixels2bases: pixels2bases,
            bases2pixels: bases2pixels,
            getEdges: getEdges,
            xpos2gpos: xpos2gpos,
            gpos2xpos: gpos2xpos
        }
    })();
    var Controls = (function() {
        var info = new Ext.Button({
            iconCls: 'silk_information',
            tooltip: 'Show information about the genome',
            handler: function() {
                self.fireEvent('describe', Syndicator.get())
            }
        });
        var title = new Ext.Toolbar.TextItem('Awaiting syndication...');
        var filler = new Ext.Toolbar.Fill();
        var defaultSettings = {
            baseline: 0,
            display: 0,
            scale: 2,
            multi: 1,
            activate: 0,
            hic_d: 0,
            yaxis: 100,
            ydelta: 5,
            bookmarks: [],
            highlight: {
                color: 'blue',
                size: 11
            },
            capture: {
                strand: 0,
                mode: 0,
                margin: 10
            },
            displays: {
                color: 'red'
            },
            jump: {
                mode: 0,
                pace: 5,
                screen: 1
            }
        };
        Ext.applyIf(AnnoJ.config.settings, defaultSettings);
        var checked1 = true;
        var checked2 = false;
        var scaleBox = new Ext.form.TextField({
            width: 32,
            value: 1,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        scaleBox.on('specialKey', function(config, event) {
            if (event.getKey() == event.ENTER) {
                var f = scaleBox.getValue();
                if (f == "") f = 1;
                scaleBox.setValue(f);
                var Tracks = AnnoJ.getGUI().Tracks;
                if (Tracks) {
                    for (var i = 0; i < Tracks.tracks.tracks.length; i++) {
                        var track = Tracks.tracks.tracks[i];
                        if (Tracks.tracks.isActive(track)) track.Toolbar.setScale(f);
                    }
                }
                AnnoJ.config.settings.multi = 1
            }
        });
        var GlobalscaleBox = new Ext.form.TextField({
            width: 32,
            value: AnnoJ.config.settings.yaxis,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        GlobalscaleBox.on('specialKey', function(config, event) {
            if (event.getKey() == event.ENTER) {
                var scaler = GlobalscaleBox.getValue();
                if (scaler == "") GlobalscaleBox.setValue(AnnoJ.config.settings.yaxis);
                AnnoJ.config.settings.yaxis = GlobalscaleBox.getValue();
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var dragMode = new Ext.CycleButton({
            showText: true,
            autoWidth: false,
            width: 110,
            prependText: '<b>Drag mode:</b> &nbsp;',
            tooltip: 'Action to be performed when you click and drag in a track',
            items: [{
                text: 'browse',
                iconCls: 'silk_cursor',
                checked: true
            }, {
                text: 'zoom',
                iconCls: 'silk_magnifier',
            }, {
                text: 'scale',
                iconCls: 'silk_arrow_inout'
            }, {
                text: 'resize',
                iconCls: 'silk_shape_handles'
            }, {
                text: 'highlight',
                iconCls: 'silk_lightbulb'
            }],
            changeHandler: function(btn, item) {
                self.fireEvent('dragModeSet', item.text)
            }
        });
        Ext.EventManager.addListener(window, 'keyup', function(event) {
            if (event.getTarget().tagName == 'INPUT') return;
            if (event.getKey() != 16) return;
            dragMode.toggleSelected()
        });

        function setDragMode(mode) {
            dragMode.suspendEvents();
            var num = 0;
            var max = dragMode.items.length;
            while (dragMode.getActiveItem().text != mode) {
                dragMode.toggleSelected();
                if (++num > max) break
            }
            dragMode.resumeEvents()
        };

        function setSelected(option, mode) {
            option.suspendEvents();
            var num = 0;
            var max = option.items.length;
            while (option.getActiveItem().text != mode) {
                option.toggleSelected();
                if (++num > max) break
            }
            option.resumeEvents()
        };
        var ratio = new Ext.form.TextField({
            width: 42,
            maskRe: /[0-9:]/,
            regex: /^[0-9]+:[0-9]+$/,
            selectOnFocus: true
        });
        ratio.on('blur', function(config, event) {
            var value = this.getValue() || '10:1';
            var bases = parseInt(value.split(':')[0]);
            var pixels = parseInt(value.split(':')[1]);
            var location = Navigator.setLocation({
                bases: bases,
                pixels: pixels
            });
            refreshControls();
            self.fireEvent('browse', Navigator.getLocation())
        });
        ratio.on('specialKey', function(config, event) {
            var value = this.getValue() || '10:1';
            var bases = parseInt(value.split(':')[0]);
            var pixels = parseInt(value.split(':')[1]);
            AnnoJ.config.location.bases = bases;
            AnnoJ.config.location.pixels = pixels;
            if (event.getKey() == event.ENTER) {
                var location = Navigator.setLocation({
                    bases: bases,
                    pixels: pixels
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var further = new Ext.Button({
            iconCls: 'silk_zoom_out',
            tooltip: 'Zoom out by a fixed increment',
            handler: function() {
                if (this.disabled) return;
                Navigator.step(false);
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var closer = new Ext.Button({
            iconCls: 'silk_zoom_in',
            tooltip: 'Zoom in by a fixed increment',
            handler: function() {
                if (this.disabled) return;
                Navigator.step(true);
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var higher = new Ext.Button({
            iconCls: 'silk_arrow_out',
            tooltip: '<nobr>Increase all track heights in certain pixels</nobr>',
            handler: function() {
                if (this.disabled) return;
                AnnoJ.getGUI().Tracks.tracks.each(function(track) {
                    track.setHeight(track.getHeight() + AnnoJ.config.settings.ydelta);
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var lower = new Ext.Button({
            iconCls: 'silk_arrow_in',
            tooltip: '<nobr>Decrease all track heights in certain pixels</nobr>',
            handler: function() {
                if (this.disabled) return;
                AnnoJ.getGUI().Tracks.tracks.each(function(track) {
                    track.setHeight(track.getHeight() - AnnoJ.config.settings.ydelta);
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var assembly = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            width: 50,
            grow: true,
            growMin: 40,
            growMax: 100,
            forceSelection: true,
            mode: 'local',
            displayField: 'id'
        });
        assembly.on('select', function(e) {
            AnnoJ.config.location.assembly = e.getValue();
            refreshControls();
        });
        var jumpLeft = new Ext.form.NumberField({
            width: 90,
            readOnly: true,
            allowNegative: false,
            allowDecimals: false,
            style: 'text-align: left',
            grow: true,
            growMin: 60,
            growMax: 130
        });
        var jumpRight = new Ext.form.NumberField({
            width: 90,
            readOnly: true,
            allowNegative: false,
            allowDecimals: false,
            grow: true,
            growMin: 60,
            growMax: 130
        });
        var jump = new Ext.form.NumberField({
            width: 125,
            allowNegative: false,
            allowDecimals: false,
            grow: true,
            growMin: 100,
            growMax: 150
        });
        jump.on('specialKey', function(config, event) {
            AnnoJ.config.location.assembly = assembly.getValue();
            AnnoJ.config.location.position = parseInt(this.getValue());
            if (event.getKey() == event.ENTER) {
                Navigator.setLocation({
                    assembly: assembly.getValue(),
                    position: parseInt(this.getValue())
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var go = new Ext.Button({
            iconCls: 'silk_server_go',
            text: '<b><font size=2>Go</font></b>',
            tooltip: '<nobr>Browse to the specified<br>chromosome position</nobr>',
            handler: function() {
                Navigator.setLocation({
                    assembly: assembly.getValue(),
                    position: parseInt(jump.getValue())
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var setting = new Ext.Button({
            iconCls: 'silk_cog',
            tooltip: 'Settings',
            handler: function(event) {
                if (!AnnoJ.getGUI().Settings.isVisible()) {
                    AnnoJ.getGUI().Settings.show();
                    AnnoJ.getGUI().Settings.expand();
                    AnnoJ.getGUI().Settings.manage(AnnoJ.config.settings);
                } else {
                    AnnoJ.getGUI().Settings.hide();
                }
            }
        });
        var initialized = false;

        function more_update() {
            var more_html = "<div style='padding-top:7px;padding-left:6px;padding-bottom:8px;'>";
            more_html += "<b>V</b>iew ... <br>";
            more_html += "</div><div style='padding-left:8px;padding-bottom:6px;'><nobr>";
            more_html += "<input type=checkbox onChange='if (this.checked) { AnnoJ.getGUI().Container.show(); AnnoJ.getGUI().Container.expand(); } else { AnnoJ.getGUI().Container.collapse(); }' ";
            if (!initialized || AnnoJ.getGUI().Container.isVisible()) more_html += "checked";
            more_html += "> Show Configuration ";
            more_html += "</div><div style='padding-left:8px;padding-bottom:6px;padding-right:10px;'><nobr>";
            more_html += "<input type=checkbox onChange='if (this.checked) { AnnoJ.getGUI().AboutBox.show(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().AboutBox); } else { AnnoJ.getGUI().AboutBox.hide(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().TrackSelector, true); }' ";
            if (!initialized || AnnoJ.getGUI().AboutBox.isVisible()) more_html += "checked";
            more_html += "> Show Credits panel ";
            more_html += "</div><div style='padding-left:8px;padding-bottom:6px;'><nobr>";
            more_html += "<input type=checkbox onChange='if (this.checked) { AnnoJ.getGUI().Bookmarker.show(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().Bookmarker); } else { AnnoJ.getGUI().Bookmarker.hide(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().TrackSelector, true); }' ";
            if (!initialized || AnnoJ.getGUI().Bookmarker.isVisible()) more_html += "checked";
            more_html += "> Show Bookmark panel ";
            more_html += "</div><div style='padding-bottom:6px;padding-left:8px;'><nobr>";
            more_html += "<input type=checkbox onChange='if (this.checked) { AnnoJ.getGUI().Messenger.show(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().Messenger); } else { AnnoJ.getGUI().Messenger.hide(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().TrackSelector, true); }' ";
            if (!initialized || AnnoJ.getGUI().Messenger.isVisible()) more_html += "checked";
            more_html += "> Show System panel ";
            more_html += "</div><div style='padding-left:8px;padding-bottom:10px;padding-right:10px;'><hr><nobr>";
            more_html += "<input type=checkbox onChange='if (this.checked) { AnnoJ.getGUI().SignOn.show(); AnnoJ.getGUI().SignOn.manage(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().SignOn); } else { AnnoJ.getGUI().SignOn.hide(); AnnoJ.getGUI().toggleExpand(AnnoJ.getGUI().TrackSelector, true); }' ";
            if (!initialized || AnnoJ.getGUI().SignOn.isVisible()) more_html += "checked";
            more_html += "> Show Sign On panel ";
            more_html += " </div>";
            return more_html
        }
        var more_text = more_update();
        initialized = true;
        var tip;

        function onClickMore(box) {
            more_text = more_update();
            tip = new Ext.ToolTip({
                html: more_text,
                autoShow: true,
                width: 150,
                layout: 'hbox',
                showDelay: 50,
                dismissDelay: 750,
                focusOnToFront: true,
                autoHide: false,
                stateful: false,
                getTargetXY: function() {
                    return [box.x - 126, box.y + 28]
                },
                listeners: {
                    hide: function() {}
                }
            });
            tip.show();
        }
        var more = new Ext.Button({
            iconCls: 'silk_more',
            tooltip: 'More ',
            handler: function(e) {
                if (!tip) onClickMore(e.getBox());
                else {
                    tip.hide();
                    tip.destroy();
                    tip = null
                }
            }
        });
        var apps = new Ext.Button({
            iconCls: 'silk_options',
            tooltip: 'Apps',
            handler: function(event) {
                if (26 <= AnnoJ.getGUI().NavBar.appsBar.el.getHeight()) {
                    AnnoJ.getGUI().NavBar.appsBar.el.setHeight(0);
                    AnnoJ.getGUI().NavBar.appsBar.el.hide();
                } else {
                    AnnoJ.getGUI().NavBar.appsBar.el.setHeight(26);
                    AnnoJ.getGUI().NavBar.appsBar.el.show();
                }
            }
        });
        var favs = new Ext.Button({
            iconCls: 'silk_star_lblack',
            tooltip: 'Bookmarks',
            handler: function(event) {
                var loc = AnnoJ.getLocation();
                var bookmark = {
                    name: AnnoJ.config.markname,
                    location: {
                        assembly: loc.assembly,
                        position: loc.position,
                        bases: loc.bases,
                        pixels: loc.pixels
                    }
                };
                var bookmarks = AnnoJ.config.settings.bookmarks;
                for (var i = 0, loci; i < bookmarks.length; i++) {
                    loci = bookmarks[i].location;
                    if (loci.assembly == loc.assembly && loci.position == loc.position && loci.bases == loc.bases && loci.pixels == loc.pixels) break;
                }
                if (AnnoJ.getGUI().Container.collapsed) AnnoJ.getGUI().Container.expand();
                if (AnnoJ.getGUI().Bookmarker.collapsed) AnnoJ.getGUI().Bookmarker.expand();
                if (i == bookmarks.length) {
                    AnnoJ.config.settings.bookmarks.push(bookmark);
                    AnnoJ.getGUI().Bookmarker.redraw()
                } else {
                    alert('This location \'' + loc.assembly + ':' + loc.position + '@' + loc.bases + ':' + loc.pixels + '\' is already bookmarked.');
                }
            }
        });
        var prevNext, pace, scrn, stopLoc, bookID = 0;
        var prev = new Ext.Button({
            iconCls: 'silk_arrow_left',
            tooltip: 'Scroll move to the left with one screen, <br>previous bookmark site, or animation.',
            handler: function() {
                var setts = AnnoJ.config.settings;
                if (setts.jump.animating) {
                    setts.jump.animating = false;
                    if (setts.jump.requestID)
                        window.cancelAnimationFrame(setts.jump.requestID);
                } else if (setts.jump.mode == 2) {
                    pace = setts.jump.pace || 5;
                    scrn = setts.jump.screen || 1;
                    var loc = AnnoJ.getLocation();
                    stopLoc = loc.position - AnnoJ.pixels2bases(scrn * Tbar.getSize().width);
                    prevNext = -1;
                    animate();
                } else if (setts.jump.mode == 1 && setts.bookmarks.length) {
                    bookID = (bookID + setts.bookmarks.length - 1) % setts.bookmarks.length;
                    if (setts.bookmarks[bookID] && setts.bookmarks[bookID].location) {
                        AnnoJ.setLocation(setts.bookmarks[bookID].location);
                        refreshControls();
                        self.fireEvent('browse', Navigator.getLocation())
                    }
                } else {
                    scrn = setts.jump.screen || 1;
                    var jWidth = Math.round(Tbar.getSize().width * scrn);
                    Navigator.bump(-Navigator.pixels2bases(jWidth));
                    refreshControls();
                    self.fireEvent('browse', Navigator.getLocation())
                }
            }
        });
        var next = new Ext.Button({
            iconCls: 'silk_arrow_right',
            tooltip: 'Scroll move to the right with one screen, <br>next bookmark site, or animation.',
            handler: function() {
                var setts = AnnoJ.config.settings;
                if (setts.jump.animating) {
                    setts.jump.animating = false;
                    if (setts.jump.requestID)
                        window.cancelAnimationFrame(setts.jump.requestID);
                } else if (setts.jump.mode == 2) {
                    pace = setts.jump.pace || 5;
                    scrn = setts.jump.screen || 1;
                    var loc = AnnoJ.getLocation();
                    stopLoc = loc.position + AnnoJ.pixels2bases(scrn * Tbar.getSize().width);
                    prevNext = 1;
                    animate();
                } else if (setts.jump.mode == 1 && setts.bookmarks.length) {
                    bookID = (bookID + setts.bookmarks.length + 1) % setts.bookmarks.length;
                    if (setts.bookmarks[bookID] && setts.bookmarks[bookID].location) {
                        AnnoJ.setLocation(setts.bookmarks[bookID].location);
                        refreshControls();
                        self.fireEvent('browse', Navigator.getLocation())
                    }
                } else {
                    scrn = setts.jump.screen || 1;
                    var jWidth = Math.round(Tbar.getSize().width * scrn);
                    Navigator.bump(Navigator.pixels2bases(jWidth));
                    refreshControls();
                    self.fireEvent('browse', Navigator.getLocation())
                }
            }
        });

        function animate() {
            var loc = AnnoJ.getLocation();
            var setts = AnnoJ.config.settings;
            if ((prevNext == 1 && loc.position > stopLoc) || (prevNext == -1 && (loc.position < stopLoc || loc.position <= 1))) {
                setts.jump.animating = false;
                if (setts.jump.requestID)
                    window.cancelAnimationFrame(setts.jump.requestID);
                return;
            }
            var j = AnnoJ.pixels2bases(pace) || 1;
            loc.position += j * prevNext;
            Ext.each(AnnoJ.getGUI().Tracks.tracks.tracks, function(track) {
                if (track.Canvas) {
                    track.setLocation(loc);
                }
            });
            loc = AnnoJ.setLocation(loc);
            setts.jump.animating = true;
            setts.jump.requestID = window.requestAnimationFrame(animate);
        }
        var checked3 = false,
            checked4 = false;
        if (!AnnoJ.config.searchType) AnnoJ.config.searchType = 'Page';
        if (AnnoJ.config.searchType == 'Page') checked3 = true;
        else checked3 = false;
        if (AnnoJ.config.searchType == 'Track') checked4 = true;
        else checked4 = false;
        var searchType = new Ext.CycleButton({
            showText: true,
            items: [{
                text: 'Page',
                checked: checked3
            }, {
                text: 'Find',
                checked: !checked3 && !checked4
            }],
            changeHandler: function(btn, item) {
                AnnoJ.config.searchType = item.text;
                ds.baseParams.action = AnnoJ.config.searchType;
            }
        });

        function track_data(conf) {
            var tracka = [];
            var start = conf.start || 0;
            var limit = conf.limit || 10;
            var query = conf.query;
            if (!query || query == '') return tracka;
            var no = 0;
            var Tracks = AnnoJ.getGUI().Tracks;
            for (var i = 0; i < Tracks.tracks.tracks.length; i++) {
                var track = Tracks.tracks.tracks[i];
                if (!Tracks.tracks.isActive(track)) {
                    var str = track.config.name + ' ' + track.config.path;
                    if (str.indexOf(query) != -1) {
                        if (no >= start && no < start + limit)
                            tracka.push({
                                index: no + 1,
                                id: track.config.id,
                                title: track.config.name,
                                url: track.config.data,
                                description: track.config.path
                            });
                        no++;
                    }
                }
            }
            ds.baseParams.count = no;
            return tracka;
        };
        var ds = new Ext.data.Store({
            url: AnnoJ.config.searchURL || aj2 + '/tools/search.php',
            baseParams: {
                action: AnnoJ.config.searchType,
                genome: '',
                page_url: window.location.protocol + '//' + window.location.host + window.location.pathname
            },
            listeners: {
                beforeload: function() {
                    if (this.baseParams.action == 'Find') {
                        var td = track_data({
                            start: this.lastOptions.params.start,
                            limit: this.lastOptions.params.limit,
                            query: this.baseParams.query
                        });
                        this.baseParams.tracks = JSON.stringify(td);
                    }
                }
            },
            reader: new Ext.data.JsonReader({
                root: 'rows',
                totalProperty: 'count',
                id: 'id'
            }, [{
                name: 'index',
                mapping: 'index',
            }, {
                name: 'id',
                mapping: 'id'
            }, {
                name: 'title',
                mapping: 'title'
            }, {
                name: 'url',
                mapping: 'url',
            }, {
                name: 'description',
                mapping: 'description'
            }])
        });
        var pageTpl = new Ext.XTemplate('<tpl for="."><div style="font-size:11px;" class="gi">', '{index}. <b>{title} : </b><span>{url} </span> <span>{description}</span>', '</div></tpl>');
        var search = new Ext.form.ComboBox({
            store: ds,
            displayField: 'id',
            typeAhead: false,
            cls: 'promote',
            style: {
                background: '#F8FCFF',
                border: 'solid 1px'
            },
            loadingText: 'Load ...',
            width: 325,
            maxHeight: 600,
            pageSize: 10,
            hideTrigger: false,
            collapsible: true,
            tpl: pageTpl,
            minChars: 3,
            minListWidth: 415,
            itemSelector: 'div.gi',
            emptyText: 'Search ...',
            onSelect: function(record) {
                if (AnnoJ.config.searchType == 'Find') {
                    var gui = AnnoJ.getGUI();
                    if (gui.Tracks.tracks.find('id', record.data.id)) {
                        var track = gui.Tracks.tracks.find('id', record.data.id);
                        var node = track.node;
                        gui.TrackSelector.active.appendChild(node);
                        gui.TrackSelector.fireEvent('openTrack', node.track, node.nextSibling ? node.nextSibling.track : null)
                    }
                } else {
                    if (ds.baseParams.genome == '')
                        ds.baseParams.genome = ds.reader.jsonData.genome;
                    BaseJS.request({
                        url: AnnoJ.config.searchURL || aj2 + '/tools/search.php',
                        method: 'POST',
                        requestJSON: false,
                        data: {
                            action: 'Script',
                            md: record.data.id
                        },
                        success: function(response) {
                            var x = 0;
                            Ext.each(response.tracks, function(trackConfig, index) {
                                if (!AnnoJ.getGUI().Tracks.tracks.find('id', trackConfig.id)) {
                                    try {
                                        track = new AnnoJ.ProxyTrack(trackConfig);
                                    } catch (e) {
                                        response.tracks[index] = null;
                                        WebApp.error(e);
                                        if (!Ext.isIE) console.log(e);
                                        return
                                    };
                                    AnnoJ.getGUI().Tracks.tracks.manage(track);
                                    AnnoJ.getGUI().TrackSelector.manage(track);
                                    AnnoJ.config.tracks.push(trackConfig);
                                    x++;
                                }
                            });
                            AnnoJ.notice('Browser has ' + x + ' more tracks added', false);
                        },
                        failure: function(message) {
                            AnnoJ.error('Failed to load page ' + id + ' (' + message + ')');
                        }
                    })
                }
                this.collapse()
            }
        });
        search.on('expand', function() {});

        function bindAssemblies(options, selected) {
            if (!options || options.length == 0) return;
            var temp = [];
            Ext.each(options, function(item) {
                temp.push([item.id])
            });
            if (!selected) selected = temp[0];
            var store = new Ext.data.SimpleStore({
                fields: ['id'],
                data: temp
            });
            assembly.bindStore(store);
            assembly.setValue(selected);
            Navigator.Assembly.init({
                options: options,
                selected: selected
            })
        };

        function refreshControls() {
            var view = Navigator.getLocation();
            assembly.setValue(AnnoJ.config.location.assembly);
            closer.enable();
            further.enable();
            if (Navigator.Zoom.atMin()) closer.disable();
            if (Navigator.Zoom.atMax()) further.disable();
            ratio.setValue(view.bases + ':' + view.pixels);
            prev.enable();
            next.enable();
            if (Navigator.Position.atMin()) prev.disable();
            if (Navigator.Position.atMax()) next.disable();
            jump.setValue(view.position);
            var edges = Navigator.getEdges();
            jumpLeft.setValue(edges.g1);
            jumpRight.setValue(edges.g2);
        };

        function loadSettings(settings) {
            var Tracks = AnnoJ.getGUI().Tracks;
            for (var i = 0; i < AnnoJ.config.active.length; i++) {
                var id = AnnoJ.config.active[i];
                for (var j = settings.active.length - 1; j >= 0; j--) {
                    if (id == settings.active[j]) break
                }
                if (j < 0) {
                    var track = Tracks.tracks.find('id', id);
                    if (track) Tracks.tracks.close(track)
                }
            }
            for (var i = 0; i < settings.active.length; i++) {
                var id = settings.active[i];
                for (var j = AnnoJ.config.active.length - 1; j >= 0; j--) {
                    if (id == AnnoJ.config.active[j]) break
                }
                if (j < 0) {
                    var track = Tracks.tracks.find('id', id);
                    if (track) {
                        var first = null;
                        if (i > 0) {
                            var id = settings.active[i - 1];
                            first = Tracks.tracks.find('id', id);
                        }
                        Tracks.tracks.manage(track);
                        Tracks.tracks.insert(track, first);
                        track.setLocation(AnnoJ.config.location)
                    }
                }
            }
            Ext.apply(AnnoJ.config, settings);
            scaleBox.setValue(1);
            GlobalscaleBox.setValue(AnnoJ.config.settings.yaxis);
            ratio.setValue(AnnoJ.config.location.bases + ':' + AnnoJ.config.location.pixels);
            assembly.setValue(AnnoJ.config.location.assembly);
            if (Tracks) {
                for (var i = 0; i < Tracks.tracks.tracks.length; i++) {
                    var track = Tracks.tracks.tracks[i];
                    var conf = find(AnnoJ.config.tracks, 'id', track.config.id);
                    if (!conf) {
                        track.Toolbar.setScale(conf.scale, true);
                    }
                }
            }
            Navigator.setLocation(AnnoJ.config.location);
            self.fireEvent('browse', Navigator.getLocation())
        };

        function setTitle(txt) {
            title.el.dom.innerHTML = txt
        };
        return {
            info: info,
            title: title,
            filler: filler,
            dragMode: dragMode,
            scaleBox: scaleBox,
            GlobalscaleBox: GlobalscaleBox,
            ratio: ratio,
            further: further,
            closer: closer,
            higher: higher,
            lower: lower,
            assembly: assembly,
            jump: jump,
            jumpLeft: jumpLeft,
            jumpRight: jumpRight,
            go: go,
            prev: prev,
            next: next,
            search: search,
            searchType: searchType,
            setting: setting,
            more: more,
            apps: apps,
            favs: favs,
            bindAssemblies: bindAssemblies,
            refreshControls: refreshControls,
            loadSettings: loadSettings,
            setTitle: setTitle,
            setSelected: setSelected,
            setDragMode: setDragMode
        }
    })();
    var Toolbar = new Ext.Toolbar({
        cls: 'AJ_Navbar',
        width: '100%',
        style: 'margin: 0px; padding: 0px; font-size: 11px; border: 0px;',
        items: [Controls.jumpLeft, ' ', ' ', ' ', '<b>Multi:</b>', Controls.scaleBox, ' ', ' ', '<b>Y-axis:</b>', Controls.GlobalscaleBox, ' ', ' ', '<b>Height:</b>', Controls.higher, Controls.lower, ' ', '-', ' ', Controls.dragMode, ' ', ' ', '<b>Zoom level:</b>', ' ', Controls.ratio, Controls.further, Controls.closer, ' ', ' ', '<b>Location:</b>', Controls.assembly, Controls.jump, ' ', ' ', Controls.go, ' ', ' ', Controls.prev, Controls.next, '->', Controls.jumpRight]
    });
    Toolbar.on('render', function() {
        this.un('render');
        Controls.refreshControls()
    });
    var Apps = (function() {
        var ehandler = function(method) {
            if (!AnnoJ.config.trks) return;
            if (method == 'Merge' || method == "Summation") {
                if (AnnoJ.config.trks.length < 2) {
                    Ext.MessageBox.alert('Warning', 'You must select two or more tracks for ' + method + '!');
                    return
                }
            }
            if (method == "Subtract" || method == "Intersection") {
                if (AnnoJ.config.trks.length != 2) {
                    Ext.MessageBox.alert('Warning', 'You must select exact two tracks for ' + method + '!');
                    return
                }
            }
            var trackConfig = {};
            trackConfig.urls = '';
            trackConfig.tracktype = '';
            var first = null;
            var newid = '';
            var newname = '';
            for (var i = 0; i < AnnoJ.config.trks.length; i++) {
                var id = AnnoJ.config.trks[i];
                var track = AnnoJ.getGUI().Tracks.tracks.find('id', id);
                if (track) {
                    trackConfig.urls += track.config.data + ',';
                    trackConfig.tracktype += track.config.type + ',';
                    newid += '-' + track.config.id;
                    newname += ' - ' + track.config.name;
                    if (!first) first = track
                }
            }
            var loc = AnnoJ.getLocation();
            trackConfig.data = AnnoJ.config.analysis || aj2 + '/includes/analysis.php';
            trackConfig.name = method + newname;
            trackConfig.type = first.config.type;
            trackConfig.iconCls = first.config.iconCls || 'silk_bricks';
            trackConfig.height = first.ext.getHeight() || first.config.height;
            trackConfig.path = 'Analyses';
            trackConfig.action = method;
            trackConfig.color = first.config.color || {};
            trackConfig.scale = first.config.scale || 1;
            trackConfig.assembly = AnnoJ.config.location.assembly;
            trackConfig.showControls = true;
            if (method == 'Correlation' || method == 'Intensity') {
                trackConfig.id = 'trackyyyy-0';
                trackConfig.height = 160;
                var trk = AnnoJ.getGUI().TracksInfo.tracks.tracks[0];
                if (trk) {
                    trk.close();
                    trk = null;
                    AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = null;
                }
            } else if (method == 'Peakcall') {
                trackConfig.height = 80;
                trackConfig.id = 'new-' + method + newid;
                if (first.config.type != 'ReadsTrack') {
                    Ext.MessageBox.alert('Warning', trackConfig.name + ' is not ReadsTrack, can not launch!');
                    return
                }
                BaseJS.request({
                    url: trackConfig.data,
                    method: 'POST',
                    requestJSON: false,
                    data: {
                        action: 'range',
                        assembly: trackConfig.assembly,
                        left: loc.left,
                        right: loc.right,
                        bases: loc.bases,
                        pixels: loc.pixels,
                        action2: method,
                        urls: trackConfig.urls,
                        tracktype: trackConfig.tracktype || trackConfig.type,
                        table: trackConfig.name
                    },
                    success: function(response) {
                        trackConfig.data = response.data;
                        trackConfig.type = 'IntensityTrack';
                        var trk = AnnoJ.getGUI().Tracks.tracks.find('id', trackConfig.id);
                        if (trk) {
                            Ext.MessageBox.alert('Warning', trackConfig.name + ' already exists!');
                            return
                        }
                        try {
                            var trk = new AnnoJ[trackConfig.type](trackConfig);
                        } catch (e) {
                            WebApp.error(e);
                            if (!Ext.isIE) console.log(e);
                            return
                        };
                        var j = AnnoJ.config.active.indexOf(first.config.id);
                        if (j >= 0) {
                            AnnoJ.config.active.splice(j, 0, trackConfig.id);
                            AnnoJ.config.tracks.splice(j, 0, trackConfig)
                        }
                        AnnoJ.getGUI().Tracks.tracks.manage(trk);
                        AnnoJ.getGUI().Tracks.tracks.insert(trk, first);
                        AnnoJ.getGUI().TrackSelector.manage(trk);
                        AnnoJ.getGUI().TrackSelector.insertBefore(trk, first.config.name);
                        AnnoJ.getGUI().Tracks.tracks.refresh();
                        trk.setLocation(loc);
                    },
                    failure: function(message) {
                        Ext.MessageBox.alert('Warning', 'Failed to call peaks!');
                        return;
                    }
                })
            } else {
                trackConfig.id = 'new-' + method + newid;
                var trk = AnnoJ.getGUI().Tracks.tracks.find('id', trackConfig.id);
                if (trk) {
                    Ext.MessageBox.alert('Warning', trackConfig.name + ' already exists!');
                    return false
                }
            }
            if (method == 'Peakcall') return;
            try {
                var trk = new AnnoJ[trackConfig.type](trackConfig);
            } catch (e) {
                WebApp.error(e);
                if (!Ext.isIE) console.log(e);
                return false
            };
            if (method == 'Correlation' || method == 'Intensity') {
                Ext.apply(AnnoJ.config.infoTrack, trackConfig);
                AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = trk;
                AnnoJ.getGUI().TracksInfo.tracks.open(trk);
                if (trk) trk.setLocation(loc);
            } else {
                var j = AnnoJ.config.active.indexOf(first.config.id);
                if (j >= 0) {
                    AnnoJ.config.active.splice(j, 0, trackConfig.id);
                    AnnoJ.config.tracks.splice(j, 0, trackConfig)
                }
                AnnoJ.getGUI().Tracks.tracks.manage(trk);
                AnnoJ.getGUI().Tracks.tracks.insert(trk, first);
                AnnoJ.getGUI().TrackSelector.manage(trk);
                AnnoJ.getGUI().TrackSelector.insertBefore(trk, first.config.name);
                trk.setLocation(loc);
                AnnoJ.getGUI().Tracks.tracks.refresh();
            }
        };
        var runMerge = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Merge',
            tooltip: '<nobr>Merge tracks</nobr>',
            handler: function() {
                ehandler("Merge")
            }
        });
        var runIntersection = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Intersect',
            tooltip: '<nobr>Get intersection</nobr>',
            handler: function() {
                ehandler("Intersection")
            }
        });
        var runSummation = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Sum',
            tooltip: '<nobr>Sum up tracks</nobr>',
            handler: function() {
                ehandler("Summation")
            }
        });
        var runSubtract = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Subtract',
            tooltip: '<nobr>Subtract tracks</nobr>',
            handler: function() {
                ehandler("Subtract")
            }
        });
        var runDNA = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'DNA tool',
            tooltip: '<nobr>DNA tool for retrieving DNA sequences of highlight regions.</nobr>',
            handler: function() {
                var box = AnnoJ.config.settings.highlight;
                if (!box.regions || box.regions.length <= 0) {
                    alert("You must select or highlight regions to retrieve your genomic DNA sequences and then use the tool for Reverse complement, Primer design, Translate, Fetching new sequence, and Save.");
                    return
                }
                var url = AnnoJ.config.genome;
                var data = {
                    assembly: box.assembly,
                    coordinates: box.regions.toString(),
                    action: 'DNA',
                    strand: 'W'
                };
                openWindowWithPost(url, data);
            }
        });
        var runCapture = new Ext.Button({
            iconCls: 'silk_capture',
            text: 'Capture',
            tooltip: '<nobr>Snapshot image of window or selected region.</nobr>',
            handler: function() {
                var allCanvas = document.getElementsByTagName("CANVAS");
                for (var i = 0, width = 0, height = 0; i < allCanvas.length; i++) {
                    if (allCanvas[i].width > width) width = allCanvas[i].width;
                    if (allCanvas[i].height > 0) height += allCanvas[i].height;
                }
                var imgCanvas = document.createElement('CANVAS');
                imgCanvas.width = width;
                imgCanvas.height = height;
                var imgCtx = imgCanvas.getContext('2d');
                if (AnnoJ.config.settings.capture.strand) {
                    imgCtx.translate(width, height);
                    imgCtx.scale(-1, -1);
                    for (var i = 0, h = height; i < allCanvas.length; i++) {
                        if (allCanvas[i].width != 0 && allCanvas[i].height != 0) {
                            if (i + 1 < allCanvas.length && allCanvas[i].parentNode.parentNode == allCanvas[i + 1].parentNode.parentNode) {
                                if (allCanvas[i + 1].width != 0 && allCanvas[i + 1].height != 0) {
                                    h -= allCanvas[i + 1].height;
                                    imgCtx.drawImage(allCanvas[i + 1], 0, h);
                                }
                                h -= allCanvas[i].height;
                                imgCtx.drawImage(allCanvas[i], 0, h);
                                i++;
                            } else {
                                h -= allCanvas[i].height;
                                imgCtx.drawImage(allCanvas[i], 0, h);
                            }
                        }
                    }
                } else {
                    for (var i = 0, h = 0; i < allCanvas.length; i++) {
                        if (allCanvas[i].width != 0 && allCanvas[i].height != 0) {
                            imgCtx.drawImage(allCanvas[i], 0, h);
                            h += allCanvas[i].height;
                        }
                    }
                }
                var outImgCanvas;
                var hBox = AnnoJ.config.settings.highlight;
                if (hBox == 0) {
                    AnnoJ.config.settings.highlight.canvas = null;
                    outImgCanvas = imgCanvas;
                } else {
                    var hXpos = (hBox.xpos && hBox.xpos.length) ? hBox.xpos[hBox.xpos.length - 1][0] : 0;
                    var hWidth = (hBox.xpos && hBox.xpos.length) ? hBox.xpos[hBox.xpos.length - 1][1] : width;
                    if (AnnoJ.config.settings.capture.strand)
                        hXpos = imgCanvas.width - hXpos - hWidth;
                    var hGap = AnnoJ.config.settings.capture.margin || 10;
                    var highCanvas = document.createElement('CANVAS');
                    highCanvas.width = hWidth;
                    highCanvas.height = height;
                    var highCtx = highCanvas.getContext('2d');
                    var highData = imgCtx.getImageData(hXpos, 0, hWidth, height);
                    highCtx.putImageData(highData, 0, 0);
                    if (!AnnoJ.config.settings.capture.mode || !AnnoJ.config.settings.highlight.canvas) {
                        AnnoJ.config.settings.highlight.canvas = highCanvas;
                        outImgCanvas = highCanvas;
                    } else {
                        var foreCanvas = AnnoJ.config.settings.highlight.canvas;
                        foreCtx = foreCanvas.getContext('2d');
                        outImgCanvas = document.createElement('CANVAS');
                        outImgCanvas.width = foreCanvas.width + hGap + hWidth;
                        outImgCanvas.height = height;
                        outImgCtx = outImgCanvas.getContext('2d');
                        var imgData = foreCtx.getImageData(0, 0, foreCanvas.width, foreCanvas.height);
                        outImgCtx.putImageData(imgData, 0, 0);
                        imgData = highCtx.getImageData(0, 0, highCanvas.width, highCanvas.height);
                        outImgCtx.putImageData(imgData, foreCanvas.width + hGap, 0);
                        AnnoJ.config.settings.highlight.canvas = outImgCanvas;
                    }
                }
                var aurl = document.createElement('A');
                aurl.download = 'aj_canvas_image.png';
                aurl.href = outImgCanvas.toDataURL().replace("image/png", "image/octet-stream");
                aurl.style.display = 'none';
                document.body.appendChild(aurl);
                aurl.click();
                document.body.removeChild(aurl);
            }
        });
        var runCancel = new Ext.Button({
            iconCls: 'silk_cancel',
            tooltip: '<nobr>Close Apps panel</nobr>',
            handler: function() {
                AnnoJ.getGUI().NavBar.appsBar.el.setHeight(0);
                AnnoJ.getGUI().NavBar.appsBar.el.hide();
            }
        });
        var runIntensity = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Intensity',
            tooltip: 'get Intensity',
            handler: function() {
                ehandler("Intensity")
            }
        });
        var runCorrelation = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Correlation',
            tooltip: 'Run pearson correlation',
            handler: function() {
                ehandler("Correlation")
            }
        });
        var runPeakcall = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Peak calling',
            tooltip: 'Peak calling',
            handler: function() {
                ehandler("Peakcall")
            }
        });
        return {
            runMerge: runMerge,
            runIntersection: runIntersection,
            runSummation: runSummation,
            runSubtract: runSubtract,
            runCapture: runCapture,
            runDNA: runDNA,
            runCancel: runCancel
        }
    })();
    var headBricks = new Ext.Button({
        iconCls: 'silk_bricks',
        tooltip: 'Switch to another genome, not finished.',
        handler: function() {}
    });
    var headApps = new Ext.Button({
        iconCls: 'silk_options_color'
    });
    var Tbar = new Ext.Toolbar({
        xtype: 'container',
        layout: 'anchor',
        defaults: {
            anchor: '0'
        },
        defaultType: 'toolbar',
        style: 'margin: 0px; padding: 0px;',
        items: [{
            cls: 'x-panel-header',
            height: 24,
            style: "margin: 0px; padding: 0px;",
            items: [Controls.info, Controls.title, "<b><font color=#15428b>AnnoJ Epigenome Browser at <a href=https://brainome.ucsd.edu>CNDD lab, UCSD</a>.  <a target=_blank href='https://brainome.ucsd.edu/howto_annoj.html'>User guide</a>.  </font></b>", ' ', AnnoJ.config.info.title, ' ' , AnnoJ.config.info.genome, ' ', AnnoJ.config.info.contact,' ', AnnoJ.config.info.email, ' ', AnnoJ.config.info.institution]
        },
        // {
        //     cls: 'x-panel-header',
        //     height: 28,
        //     style: "margin: 0px; padding: 2px;",
        //     items: [headBricks, '<b><font color=#15428b>Genome :</font></b>', ' ', Controls.info, Controls.title, '->', Controls.searchType, Controls.search, ' ', ' ', Controls.favs, Controls.apps, Controls.setting, Controls.more, '  ']
        // },
        {
            height: 28,
            style: 'margin: 0px; padding: 4px;',
            items: [Controls.jumpLeft, ' ', Toolbar, '->', Controls.jumpRight]
        }, {
            height: 0,
            style: 'margin: 0px; padding: 1px; visibility: hidden;',
            items: [headApps, '<b><font color=#2562BB>Apps :</font></b>', ' ', Apps.runMerge, ' ', Apps.runIntersection, ' ', Apps.runSummation, ' ', Apps.runSubtract, ' ', '-', ' ', Apps.runCapture, ' ', Apps.runDNA, '->', Apps.runCancel, ' ']
        }]
    });
    if (AnnoJ.config.settings.toolbar == 'hide') Tbar.hide();
    this.ext = Tbar;
    this.Apps = Apps;
    this.Toolbar = Toolbar;
    this.Controls = Controls;
    this.appsIcon = Controls.apps;
    this.appsBar = Tbar.items.items[2];
    this.getLocation = Navigator.getLocation;
    this.setLocation = Navigator.setLocation;
    this.pixels2bases = Navigator.pixels2bases;
    this.bases2pixels = Navigator.bases2pixels;
    this.xpos2gpos = Navigator.xpos2gpos;
    this.gpos2xpos = Navigator.gpos2xpos;
    this.syndicate = Syndicator.syndicate;
    this.setTitle = Controls.setTitle;
    this.setDragMode = Controls.setDragMode
};
Ext.extend(AnnoJ.Navigator, Ext.util.Observable);
AnnoJ.InfoToolBar = function() {
    var self = this;
    var Controls = (function() {
        var zoomMode = new Ext.CycleButton({
            showText: true,
            prependText: 'Zoom:',
            items: [{
                text: '1000:1'
            }, {
                text: '100:1'
            }, {
                text: '10:1',
                checked: true
            }, {
                text: '1:1'
            }, {
                text: '1:5'
            }, {
                text: '1:10'
            }],
            changeHandler: function(btn, item) {
                var value = item.text;
                InfoRequest.bases = parseInt(value.split(':')[0]);
                InfoRequest.pixels = parseInt(value.split(':')[1])
            }
        });
        var checked1 = true;
        if (AnnoJ.config.settings.hic_d == 1) checked1 = false;
        var HicOri = new Ext.CycleButton({
            showText: true,
            prependText: 'HiC Axis:',
            items: [{
                text: 'chr1(horizontal)',
                checked: checked1
            }, {
                text: 'chr2(vertical)',
                checked: !checked1
            }],
            changeHandler: function(btn, item) {
                if (item.text == 'chr1(horizontal)') AnnoJ.config.settings.hic_d = 0;
                if (item.text == 'chr2(vertical)') AnnoJ.config.settings.hic_d = 1
            }
        });
        return {
            zoomMode: zoomMode,
            HicOri: HicOri
        }
    })();
    var Toolbar = new Ext.Toolbar({
        cls: 'AJ_Navbar',
        items: [Controls.zoomMode, Controls.HicOri]
    });
    this.ext = Toolbar;
};
Ext.extend(AnnoJ.InfoToolBar, Ext.util.Observable);
var Mouse = function() {
    var self = this;
    this.addEvents({
        'dragStarted': true,
        'dragged': true,
        'dragEnded': true,
        'dragCancelled': true,
        'pressed': true,
        'released': true,
        'moved': true
    });
    var mouse = {
        x: 0,
        y: 0,
        down: false,
        drag: false,
        downX: 0,
        downY: 0,
        target: null
    };
    Ext.EventManager.addListener(window, 'scroll', function(event) {
        mouse.drag = false;
        mouse.down = false
    });
    Ext.EventManager.addListener(window, 'keydown', function() {
        if (mouse.drag) {
            mouse.drag = false;
            self.fireEvent('dragCancelled', mouse)
        }
    });
    this.getMouse = function() {
        return mouse
    }
};
Ext.extend(Mouse, Ext.util.Observable);
var Mouse = new Mouse();
AnnoJ.Bookmarker = (function() {
    var server = '';
    var input = new Ext.form.TextField({
        allowBlank: false,
        height: 18,
        width: 155
    });
    var addbtn = new Ext.Button({
        iconCls: 'silk_add_black',
        tooltip: '<nobr>Bookmark the current location.</nobr>',
        handler: function() {
            var name;
            if (!input.isValid()) name = AnnoJ.config.markname;
            else name = input.getValue();
            add(name)
        }
    });
    var savebtn = new Ext.Button({
        text: 'Save',
        iconCls: 'silk_disk',
        tooltip: 'Send all your bookmarks to <br>and save on the server side.',
        handler: function() {
            save();
            redraw()
        }
    });
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_bookmarks');
    var toolbar = new Ext.Toolbar({
        items: [' ', input, ' ', addbtn, savebtn]
    });

    function load() {
        if (!server) return;
        BaseJS.request({
            url: server.datasource,
            method: 'POST',
            requestJSON: false,
            data: {
                action: 'bookmarks_load'
            },
            success: function(response) {
                AnnoJ.config.settings.bookmarks = response;
                redraw()
            },
            failure: function(response) {
                AnnoJ.error(response)
            }
        })
    };

    function save() {
        if (!server) return;
        if (!AnnoJ.config.settings.bookmarks) return;
        if (!AnnoJ.config.settings.bookmarks.length) return;
        BaseJS.request({
            url: server.datasource,
            method: 'POST',
            requestJSON: false,
            data: {
                action: 'bookmarks_save',
                bookmarks: JSON.stringify(AnnoJ.config.settings.bookmarks)
            },
            success: function(response) {
                AnnoJ.notice(response.message, false)
            },
            failure: function(response) {
                AnnoJ.error(response)
            }
        })
    };

    function redraw() {
        body.update('');
        Ext.each(AnnoJ.config.settings.bookmarks, render)
    };

    function render(bookmark) {
        if (!bookmark) return;
        var row = new Ext.Element(document.createElement('DIV'));
        var del = new Ext.Element(document.createElement('DIV'));
        var nam = new Ext.Element(document.createElement('DIV'));
        row.addClass('AJ_bookmark');
        del.addClass('AJ_bookmark_delete');
        nam.addClass('AJ_bookmark_select');
        row.bookmark = bookmark;
        row.appendChild(del);
        row.appendChild(nam);
        row.appendTo(body);
        var loc = bookmark.location;
        nam.update(' ' + bookmark.name + ' (' + loc.assembly + ':' + loc.position + ' @ ' + loc.bases + ':' + loc.pixels + ')');
        del.on('click', function(event) {
            event.stopEvent();
            remove(row.bookmark);
            row.remove()
        });
        nam.on('click', function(event) {
            event.stopEvent();
            AnnoJ.setLocation(row.bookmark.location);
            AnnoJ.getGUI().Tracks.tracks.setLocation(row.bookmark.location);
        })
    };

    function add(name) {
        var loc = AnnoJ.getLocation();
        var bookmark = {
            name: name,
            location: {
                assembly: loc.assembly,
                position: loc.position,
                bases: loc.bases,
                pixels: loc.pixels
            }
        };
        var bookmarks = AnnoJ.config.settings.bookmarks;
        for (var i = 0, loci; i < bookmarks.length; i++) {
            loci = bookmarks[i].location;
            if (loci.assembly == loc.assembly && loci.position == loc.position && loci.bases == loc.bases && loci.pixels == loc.pixels) break;
        }
        if (i == bookmarks.length) {
            AnnoJ.config.settings.bookmarks.push(bookmark);
            render(bookmark)
        } else alert('This location \'' + loc.assembly + ':' + loc.position + '@' + loc.bases + ':' + loc.pixels + '\' is already bookmarked.');
    };

    function remove(bookmark) {
        var clean = [];
        Ext.each(AnnoJ.config.settings.bookmarks, function(item) {
            if (item != bookmark) {
                clean.push(item)
            }
        });
        delete bookmark;
        AnnoJ.config.settings.bookmarks = clean;
        save()
    };
    return function(url) {
        AnnoJ.Bookmarker.superclass.constructor.call(this, {
            title: 'Bookmarks',
            border: false,
            iconCls: 'silk_book_open',
            autoScroll: true,
            contentEl: body,
            tbar: toolbar,
        });
        server = url || '';
        this.redraw = redraw;
        this.load = load
    }
})();
Ext.extend(AnnoJ.Bookmarker, Ext.Panel);
AnnoJ.Messenger = (function() {
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_system_messages');

    function clear() {
        body.update('')
    };

    function alert(message, type, important) {
        if (!type || (type != 'error' && type != 'warning' && type != 'notice')) type = 'notice';
        body.update("<div class='AJ_system_" + type + "'>" + message + "</div>" + body.dom.innerHTML)
    };

    function error(message) {
        if (!Ext.isIE && console) console.trace();
        alert(message, 'error', true)
    };

    function warning(message) {
        if (!Ext.isIE && console) console.trace();
        alert(message, 'warning', true)
    };

    function notice(message, important) {
        alert(message, 'notice', important || false)
    };
    return function() {
        AnnoJ.Messenger.superclass.constructor.call(this, {
            title: 'Systems',
            iconCls: 'silk_terminal',
            autoScroll: true,
            border: false,
            contentEl: body
        });
        this.clear = clear;
        this.alert = alert;
        this.error = error;
        this.warning = warning;
        this.notice = notice
    }
})();
Ext.extend(AnnoJ.Messenger, Ext.Panel);
AnnoJ.InfoBox = function() {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_infobox');
    var innerHTML = {
        annoj: '',
        citation: '',
        message: ''
    };
    AnnoJ.InfoBox.superclass.constructor.call(this, {
        title: 'Information',
        iconCls: 'silk_information',
        border: false,
        contentEl: body,
        autoScroll: true
    });
    this.echo = function(msg) {
        self.expand();
        if (typeof(msg) == 'object') {
            var html = '<table>';
            for (var name in msg) {
                html += "<tr><td><b>" + name + "</b></td><td>" + msg[name] + "</td></tr>"
            }
            html += "</table>";
            body.update(html);
            return
        }
        body.update(msg)
    }
};
Ext.extend(AnnoJ.InfoBox, Ext.Panel);
AnnoJ.AboutBox = (function() {
    var info = {
        logo: " <a href='http://signal.salk.edu/aj2' target=_blank><div class='annoj'> &nbsp; </div></a>",
        version: 'X (9.4.25)',
        engineer: 'Huaming Chen (2013-2019),<br>Tao Wang (2010-2012),<br>Julian Tonti-Filippini (2007-2009)',
        contact: 'hchen@salk.edu',
        copyright: '&copy; Huaming Chen, Tao Wang, Julian Tonti-Filippini',
        website: "<a href=http://signal.salk.edu/aj2/>http://signal.salk.edu/aj2</a><br><a href=http://tabit.ucsd.edu>http://tabit.ucsd.edu</a>",
        tutorial: "<a target='new' href='http://neomorph.salk.edu/index.html'>SALK example</a>",
        license: "<a target='new' rel='license' href='http://creativecommons.org/licenses/by-nc-sa/3.0/'><img alt='Creative Commons License' style='border-width:0' src='http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png' /></a>"
    };
    var body = new Ext.Element(document.createElement('DIV'));
    var html = "<div style='padding-bottom:10px;'>" + info.logo + "<b>Anno-J Networked Genome Browser</b></div>" + "<table style='font-size:10px';>" + "<tr><td><div><b>Version: </b></td><td>" + info.version + "</div></td></tr>" + "<tr><td valign=top><div><b>Engineers: </b></td><td>" + info.engineer + "</div></td></tr>" + "<tr><td><div><b>Contact: </b></td><td>" + info.contact + "</div></td></tr>" + "<tr><td valign=top><div><b>Copyright: </b></td><td>" + info.copyright + "</div></td></tr>" + "<tr><td valign=top><div><b>Websites: </b></td><td>" + info.website + "</div></td></tr>" + "<tr><td><div><b>License: </b></td><td>" + info.license + "</div></td></tr>" + "<tr><td><div><b>Tutorial: </b></td><td>" + info.tutorial + "</div></td></tr>" + "</table><br><br>";
    body.addClass('AJ_aboutbox');
    body.update(html);

    function addCitation(c) {
        body.update(c + html)
    };
    return function() {
        AnnoJ.AboutBox.superclass.constructor.call(this, {
            title: 'Credits ',
            iconCls: 'silk_user_comment',
            border: false,
            contentEl: body,
            autoScroll: true
        });
        this.info = info;
        this.addCitation = addCitation
    }
})();
Ext.extend(AnnoJ.AboutBox, Ext.Panel);
AnnoJ.Settings = (function(config) {
    var displayItems = ['Histogram', 'Heatmap'];
    var activeItems = ['Single', 'Folder'];
    var captureItems = ['Forward', 'Reverse'];
    var captureModes = ['W', 'A'];
    var jumpItems = ['Normal', 'Bookmark', 'Animation'];
    var scaleItems = ['Individual(Screen)', 'Uniform(Screen)', 'Fixed(Genome)'];
    var Baseline = new Ext.form.TextField({
        width: 50,
        value: '',
        maskRe: /[0-9]/,
        regex: /^[0-9]+$/,
        selectOnFocus: true
    });
    Baseline.on('specialKey', function(config, event) {
        AnnoJ.config.settings.baseline = parseInt(Baseline.getValue());
        var Navigator = AnnoJ.getGUI().NavBar;
        Navigator.Controls.refreshControls();
        Navigator.fireEvent('browse', Navigator.getLocation())
    });
    var yDelta = new Ext.form.TextField({
        width: 50,
        value: '',
        maskRe: /[0-9]/,
        regex: /^[0-9]+$/,
        minValue: 0,
        maxValue: 200,
        selectOnFocus: true
    });
    yDelta.on('specialKey', function(config, event) {
        AnnoJ.config.settings.ydelta = parseInt(yDelta.getValue())
    });
    var showColor = new Ext.CycleButton({
        showText: true,
        width: 40,
        tooltip: 'Select heatmap display color',
        items: [{
            text: 'red',
            checked: true
        }, {
            text: 'blue',
            checked: false
        }, {
            text: 'black',
            checked: false
        }, {
            text: 'green',
            checked: false
        }, {
            text: 'purple',
            checked: false
        }, {
            text: 'red-blue',
            checked: false
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.displays.color = item.text;
            var Navigator = AnnoJ.getGUI().NavBar;
            Navigator.Controls.refreshControls();
            Navigator.fireEvent('browse', Navigator.getLocation())
        }
    });
    var showMode = new Ext.CycleButton({
        showText: true,
        tooltip: 'Toggle display between histogram and heatmap',
        items: [{
            text: 'Histogram',
            checked: true
        }, {
            text: 'Heatmap',
            checked: false
        }],
        changeHandler: function(btn, item) {
            if (item.text == "Heatmap") {
                AnnoJ.config.settings.display = 1;
                showColor.show()
            } else {
                AnnoJ.config.settings.display = 0;
                showColor.hide()
            }
            var Tracks = AnnoJ.getGUI().Tracks;
            if (Tracks) {
                for (var i in Tracks.tracks.tracks) {
                    var track = Tracks.tracks.tracks[i];
                    if (AnnoJ.config.settings.display == 1) {
                        if (track.Toolbar) track.Toolbar.hide()
                    } else if (AnnoJ.config.settings.display == 0) {
                        if (track.Toolbar) track.Toolbar.show()
                    }
                }
            }
            var Navigator = AnnoJ.getGUI().NavBar;
            Navigator.Controls.refreshControls();
            Navigator.fireEvent('browse', Navigator.getLocation())
        }
    });
    var scaleMode = new Ext.CycleButton({
        showText: true,
        tooltip: 'Scaling method for multiple tracks',
        items: [{
            text: 'Fixed(Genome)',
            checked: true
        }, {
            text: 'Individual(Screen)',
            checked: false
        }, {
            text: 'Uniform(Screen)',
            checked: false
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.scale = scaleItems.indexOf(item.text);
            var Navigator = AnnoJ.getGUI().NavBar;
            Navigator.Controls.refreshControls();
            Navigator.fireEvent('browse', Navigator.getLocation())
        }
    });
    var activeMode = new Ext.CycleButton({
        showText: true,
        minWidth: 80,
        tooltip: 'Activating single or multiple tracks in a folder',
        items: [{
            text: 'Single',
            checked: false
        }, {
            text: 'Folder',
            checked: true
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.activate = item.text == 'Folder';
        }
    });
    var captureMode = new Ext.CycleButton({
        showText: true,
        minWidth: 40,
        tooltip: '<nobr>Toggle between appending to or <br>overwriting last captured image.</nobr>',
        items: [{
            text: 'W',
            checked: true
        }, {
            text: 'A',
            checked: false
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.capture.mode = item.text == 'A';
        }
    });
    var captureStrand = new Ext.CycleButton({
        showText: true,
        minWidth: 70,
        tooltip: '<nobr>Toggle between capture forward direction (&rArr;) or <br>reverse compliment direction (&lArr;) canvas image.</nobr>',
        items: [{
            text: 'Forward',
            checked: true
        }, {
            text: 'Reverse',
            checked: false
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.capture.strand = item.text == 'Reverse';
        }
    });
    var captureMargin = new Ext.form.TextField({
        width: 40,
        value: 10,
        maskRe: /[0-9]/,
        regex: /^[0-9]+$/,
        minValue: 0,
        maxValue: 200,
        selectOnFocus: true
    });
    captureMargin.on('specialKey', function(config, event) {
        AnnoJ.config.settings.capture.margin = parseInt(captureMargin.getValue())
    });
    var highlightColor = new Ext.CycleButton({
        showText: true,
        minWidth: 70,
        tooltip: 'Pick up a highlight color.',
        items: [{
            text: 'red',
            checked: false
        }, {
            text: 'green',
            checked: false
        }, {
            text: 'blue',
            checked: true
        }, {
            text: 'purple',
            checked: false
        }, {
            text: 'orange',
            checked: false
        }, {
            text: 'gray',
            checked: false
        }],
        changeHandler: function(btn, item) {
            AnnoJ.config.settings.highlight.color = item.text;
        }
    });
    var highlightSize = new Ext.form.TextField({
        width: 50,
        value: '',
        maskRe: /[0-9]/,
        regex: /^[0-9]+$/,
        minValue: 1,
        maxValue: 21,
        selectOnFocus: true
    });
    highlightSize.on('specialKey', function(event) {
        AnnoJ.config.settings.highlight.size = highlightSize.getValue()
    });
    var animPace = new Ext.form.TextField({
        width: 40,
        emptyText: '5',
        value: '5',
        maskRe: /[0-9]/,
        regex: /^[0-9]+$/,
        selectOnFocus: true
    });
    animPace.on('specialKey', function(config, event) {
        AnnoJ.config.settings.jump.pace = animPace.getValue()
    });
    var animScreen = new Ext.form.TextField({
        width: 40,
        emptyText: '1',
        value: '1',
        maskRe: /[0-9.]/,
        regex: /^[0-9.]+$/,
        selectOnFocus: true
    });
    animScreen.on('specialKey', function(config, event) {
        AnnoJ.config.settings.jump.screen = animScreen.getValue()
    });
    var animPaceBar = new Ext.Toolbar({
        cls: 'x-panel',
        style: 'margin: 0px; padding: 1px; font-size: 10px; border: 0px; background: #FFFFFF;',
        items: [' Pace: &nbsp; ', animPace]
    });
    var animScreenBar = new Ext.Toolbar({
        cls: 'x-panel',
        style: 'margin: 0px; padding: 1px; font-size: 10px; border: 0px; background: #FFFFFF;',
        items: [' Screen: &nbsp; ', animScreen]
    });
    var animBar = new Ext.Toolbar({
        cls: 'x-panel',
        width: '100%',
        style: 'margin: 0px; padding: 1px; font-size: 10px; border: 0px; background: #FFFFFF;',
        items: [' &nbsp; &nbsp; &#8627;', animPaceBar, animScreenBar]
    });
    var scrollMode = new Ext.CycleButton({
        showText: true,
        minWidth: 80,
        tooltip: 'Select scroll mode for arrow &lArr; and &rArr;.',
        items: [{
            text: 'Normal',
            checked: true
        }, {
            text: 'Bookmark',
            checked: false
        }, {
            text: 'Animation',
            checked: false
        }],
        changeHandler: function(btn, item) {
            var setts = AnnoJ.config.settings;
            setts.jump.mode = jumpItems.indexOf(item.text);
            if (setts.jump.mode == 1) {
                animBar.hide();
            } else {
                animPace.setValue(AnnoJ.config.settings.jump.pace);
                animScreen.setValue(AnnoJ.config.settings.jump.screen);
                animBar.show();
                if (setts.jump.mode === 0) animPaceBar.hide();
                else animPaceBar.show()
            }
            if (setts.jump.mode !== 2) {
                if (setts.jump.animating) {
                    setts.jump.animating = false;
                    if (setts.jump.requestID)
                        window.cancelAnimationFrame(setts.jump.requestID);
                }
            }
        }
    });
    if (scrollMode.getActiveItem().text == 'Bookmark') animBar.hide();
    else if (scrollMode.getActiveItem().text == 'Normal') animPaceBar.hide();
    var toolbar = new Ext.Toolbar({
        xtype: 'container',
        layout: 'anchor',
        defaults: {
            anchor: '0'
        },
        defaultType: 'toolbar',
        style: 'margin: 0px; padding: 0px; border: 0px; background : #FFFFFF;',
        items: [{
            cls: 'x-panel',
            height: 28,
            style: 'padding-top: 8px; margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Baseline: </b> &nbsp &nbsp; ', Baseline]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Display mode: </b> &nbsp ', showMode, ' &nbsp;', showColor]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Global Scale: </b> &nbsp &nbsp; ', scaleMode]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Height </b>(<i>&Delta;y</i>)<b>: </b> &nbsp &nbsp; ', yDelta]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Active mode: </b> &nbsp &nbsp; ', activeMode]
        }, {
            cls: 'x-panel',
            height: 24,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Capture </b> &rArr; &nbsp &nbsp; strand: &nbsp; ', captureStrand]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: [' &nbsp &nbsp; &nbsp; mode: &nbsp;', captureMode, ' &nbsp; &nbsp; margin: &nbsp; ', captureMargin]
        }, {
            cls: 'x-panel',
            height: 24,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Highlight </b> &rArr; &nbsp &nbsp; color: &nbsp; ', highlightColor]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: [' &nbsp &nbsp; &nbsp; size: &nbsp; ', highlightSize]
        }, {
            cls: 'x-panel',
            height: 24,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: ['<b>Scroll mode: </b> &nbsp &nbsp; ', scrollMode]
        }, {
            cls: 'x-panel',
            height: 26,
            style: 'margin-left: 6px; border: 0px; background: #FFFFFF;',
            items: [' ', animBar]
        }]
    });

    function setSelected(option, mode) {
        option.suspendEvents();
        var num = 0;
        var max = option.items.length;
        while (option.getActiveItem().text != mode) {
            option.toggleSelected();
            if (++num > max) break
        }
        option.resumeEvents()
    };

    function manage(settings) {
        if (!settings) return;
        if (settings.baseline !== undefined)
            Baseline.setValue(settings.baseline);
        if (settings.ydelta !== undefined)
            yDelta.setValue(settings.ydelta);
        if (settings.highlight.size !== undefined)
            highlightSize.setValue(settings.highlight.size);
        if (settings.jump.pace !== undefined)
            animPace.setValue(settings.jump.pace);
        if (settings.jump.screen !== undefined)
            animScreen.setValue(settings.jump.screen);
        if (settings.capture.margin !== undefined)
            captureMargin.setValue(settings.capture.margin);
        if (settings.display !== undefined) {
            setSelected(showMode, displayItems[settings.display]);
            if (settings.display == 0) showColor.hide();
            else {
                showColor.show();
                setSelected(showColor, settings.displays.color);
            }
        }
        if (settings.highlight.color)
            setSelected(highlightColor, settings.highlight.color);
        if (settings.activate !== undefined)
            setSelected(activeMode, activeItems[settings.activate]);
        if (settings.jump.mode !== undefined)
            setSelected(scrollMode, jumpItems[settings.jump.mode]);
        if (settings.capture.strand !== undefined)
            setSelected(captureStrand, captureItems[settings.capture.strand]);
        if (settings.capture.mode !== undefined)
            setSelected(captureMode, captureModes[settings.capture.mode]);
        if (settings.scale !== undefined)
            setSelected(scaleMode, scaleItems[settings.scale]);
    }
    return function() {
        AnnoJ.Settings.superclass.constructor.call(this, {
            title: 'Settings ',
            iconCls: 'silk_cog_go',
            autoScroll: true,
            border: false,
            tbar: toolbar
        });
        this.manage = manage
    }
})();
Ext.extend(AnnoJ.Settings, Ext.Panel);
AnnoJ.SignOn = (function() {
    var server = '';
    var notes = {
        note: " <font color=#000088><i>Sign In</i> can help users keep their's bookmark sites during travels.</font>",
        email: " <font color=#000088>Please contact admin for your verification code before submission.</font>",
        code: " <font color=#000088>Please contact admin for a verification code before submitting your registration.</font>",
        empty: " <font color=#000088>Warning: password or re-type could not be empty.</font>",
        empty0: " <font color=#000088>Warning: password could not be empty.</font>",
        email0: " <font color=#000088>Warning: username could not be empty.</font>",
        equal: " <font color=#000088>Error: re-type password must be the same as first password.</font>",
        vcode: " <font color=#000088>Verification code is empty. Please contact admin for your verification code."
    };
    var body = new Ext.Element(document.createElement('TABLE'));
    body.addClass('AJ_style_selector');
    body.setStyle('padding', '5px 5px 5px 5px');
    var row_note = new Ext.Element(document.createElement('TR'));
    var td1_note = new Ext.Element(document.createElement('TD'));
    row_note.appendTo(body);
    row_note.appendChild(td1_note);
    td1_note.dom.setAttribute("colspan", "2");
    td1_note.dom.setAttribute("align", "left");
    td1_note.dom.setAttribute("padding-left", "12");
    td1_note.dom.setAttribute("padding-right", "12");
    td1_note.dom.setAttribute("height", "40");
    td1_note.update(notes.note);
    var row_user = new Ext.Element(document.createElement('TR'));
    var td1_user = new Ext.Element(document.createElement('TD'));
    var td2_user = new Ext.Element(document.createElement('TD'));
    row_user.appendTo(body);
    row_user.appendChild(td1_user);
    row_user.appendChild(td2_user);
    row_user.dom.setAttribute("height", "28px");
    td1_user.dom.setAttribute("align", "right");
    td1_user.update('Username: ');
    var email = new Ext.form.TextField({
        width: 150,
        value: '',
        selectOnFocus: true,
        renderTo: td2_user
    });
    var row_pass = new Ext.Element(document.createElement('TR'));
    var td1_pass = new Ext.Element(document.createElement('TD'));
    var td2_pass = new Ext.Element(document.createElement('TD'));
    row_pass.appendTo(body);
    row_pass.appendChild(td1_pass);
    row_pass.appendChild(td2_pass);
    row_pass.dom.setAttribute("height", "22px");
    td1_pass.dom.setAttribute("align", "right");
    td1_pass.update('Password: ');
    var password = new Ext.form.TextField({
        width: 150,
        blankText: 'Enter your password',
        selectOnFocus: true,
        renderTo: td2_pass
    });
    var row_pass2 = new Ext.Element(document.createElement('TR'));
    var td1_pass2 = new Ext.Element(document.createElement('TD'));
    var td2_pass2 = new Ext.Element(document.createElement('TD'));
    row_pass2.appendTo(body);
    row_pass2.appendChild(td1_pass2);
    row_pass2.appendChild(td2_pass2);
    row_pass2.dom.setAttribute("height", "22px");
    td1_pass2.dom.setAttribute("align", "right");
    td1_pass2.update('Re-type: ');
    var password2 = new Ext.form.TextField({
        width: 150,
        blankText: 'Re-type your password',
        selectOnFocus: true,
        renderTo: td2_pass2
    });
    var row_verify = new Ext.Element(document.createElement('TR'));
    var td1_verify = new Ext.Element(document.createElement('TD'));
    var get_verify = new Ext.Element(document.createElement('SPAN'));
    row_verify.appendTo(body);
    row_verify.appendChild(td1_verify);
    td1_verify.dom.setAttribute("colspan", "2");
    td1_verify.dom.setAttribute("align", "left");
    td1_verify.update('&nbsp;Verification code: &nbsp;');
    td1_verify.dom.setAttribute("cellpadding", "2");
    var verification = new Ext.form.TextField({
        width: 100,
        value: '',
        selectOnFocus: true,
        renderTo: td1_verify
    });
    td1_verify.appendChild(get_verify);
    var row_submit = new Ext.Element(document.createElement('TR'));
    var td1_submit = new Ext.Element(document.createElement('TD'));
    var span1_submit = new Ext.Element(document.createElement('SPAN'));
    var span0_submit = new Ext.Element(document.createElement('SPAN'));
    var span2_submit = new Ext.Element(document.createElement('SPAN'));
    row_submit.appendTo(body);
    row_submit.appendChild(td1_submit);
    td1_submit.appendChild(span1_submit);
    td1_submit.appendChild(span0_submit);
    td1_submit.appendChild(span2_submit);
    td1_submit.dom.setAttribute("align", "center");
    td1_submit.dom.setAttribute("colspan", "2");
    td1_submit.dom.setAttribute("height", "40");
    span1_submit.update("&nbsp; <font color=blue>Register</font>&nbsp; &nbsp; ");
    span2_submit.dom.style.background = '#24488F';
    span0_submit.update("&nbsp;&nbsp; &nbsp;&nbsp;");
    span2_submit.update("&nbsp; &nbsp; <b><font color=white>Sign on</font></b> &nbsp; &nbsp; ");
    span1_submit.on("click", function() {
        if (!row_verify.isVisible()) {
            row_pass2.show();
            row_verify.show();
            td1_verify.dom.height = '40px';
            td1_note.update(notes.code);
            span2_submit.hide();
        } else {
            if (!email.getValue()) {
                td1_note.update(notes.email0);
            } else if (!password.getValue() || !password2.getValue()) {
                td1_note.update(notes.empty);
            } else if (password.getValue() != password2.getValue()) {
                td1_note.update(notes.equal);
            } else if (!verification.getValue()) {
                td1_note.update(notes.vcode);
            } else {
                register('register');
            }
        }
    });
    span2_submit.on("click", function() {
        if (!email.getValue()) {
            td1_note.update(notes.email0);
        } else if (!password.getValue()) {
            td1_note.update(notes.empty0);
        } else {
            register('sign_on');
        }
    });
    var row_signoff = new Ext.Element(document.createElement('TR'));
    var td1_signoff = new Ext.Element(document.createElement('TD'));
    var span1_signoff = new Ext.Element(document.createElement('SPAN'));
    row_signoff.appendTo(body);
    row_signoff.appendChild(td1_signoff);
    td1_signoff.appendChild(span1_signoff);
    td1_signoff.dom.setAttribute("align", "center");
    td1_signoff.dom.setAttribute("colspan", "2");
    td1_signoff.dom.setAttribute("height", "40");
    span1_signoff.dom.style.background = '#2468BB';
    span1_signoff.update("&nbsp; &nbsp; <b><font color=white>Sign off</font></b> &nbsp; &nbsp; ");
    span1_signoff.on("click", function() {
        Ext.util.Cookies.set("KETP", '');
        Ext.util.Cookies.set("KTTP", '');
        manage();
    });

    function register(cmd) {
        if (!server) return;
        BaseJS.request({
            url: server.datasource,
            method: 'POST',
            requestJSON: false,
            data: {
                action: 'bookmarks_' + cmd,
                DETP: email.getValue().replace("@", "{AT}"),
                DPTP: password.getValue(),
                DRTP: password2.getValue(),
                DVTP: verification.getValue()
            },
            success: function(response) {
                if (cmd == 'register' || cmd == 'sign_on') {
                    Ext.util.Cookies.set("KETP", response.KETP);
                    Ext.util.Cookies.set("KTTP", response.KTTP);
                    AnnoJ.getGUI().Bookmarker.load();
                }
                AnnoJ.getGUI().SignOn.note.update(response.message);
                AnnoJ.getGUI().SignOn.manage()
            },
            failure: function(response) {
                AnnoJ.getGUI().SignOn.note.update(response);
            }
        })
    };
    var manage = function() {
        var m = Ext.util.Cookies.get('KETP');
        var t = Ext.util.Cookies.get('KTTP');
        if (m != null && m != '' && t != null && t != '') {
            td1_submit.dom.setAttribute("height", "10");
            row_pass.hide();
            row_pass2.hide();
            row_verify.hide();
            row_user.hide();
            row_submit.hide();
            row_signoff.show();
            td1_note.update('&nbsp; <font color=#8888FF>You are signing on.</font>');
        } else {
            td1_note.update(notes.note);
            td1_submit.dom.setAttribute("height", "40");
            row_user.show();
            row_pass.show();
            row_submit.show();
            row_pass2.hide();
            row_verify.hide();
            row_signoff.hide();
        }
    }
    return function(url) {
        AnnoJ.SignOn.superclass.constructor.call(this, {
            title: 'Sign In',
            iconCls: 'silk_user',
            autoScroll: true,
            border: false,
            contentEl: body
        });
        server = url || '';
        this.manage = manage;
        this.note = td1_note
    }
})();
Ext.extend(AnnoJ.SignOn, Ext.Panel);
AnnoJ.Tracks = function(userConfig) {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_tracks');
    var defaultConfig = {
        title: 'Tracks',
        region: 'center',
        iconCls: 'silk_bricks',
        deferredRender: true,
        contentEl: body,
        autoScroll: false,
        header: false,
        margin: '0 0 0 0'
    };
    var config = defaultConfig;
    Ext.apply(config, userConfig || {}, defaultConfig);
    AnnoJ.Tracks.superclass.constructor.call(this, config);
    self.addEvents({
        'dragStarted': true,
        'dragCancelled': true,
        'dragEnded': true,
        'dragged': true,
        'dragModeSet': true
    });
    var mouse = {
        x: 0,
        y: 0,
        down: false,
        drag: false,
        downX: 0,
        downY: 0
    };
    var dragMode = 'browse';
    body.on('scroll', function(event) {
        mouse.drag = false;
        mouse.down = false
    });
    if ('ontouchstart' in window) {
        body.on('touchstart', function(event) {
            if (event.target.tagName == 'INPUT') return;
            if (dragMode == 'browse' && event.target.tagName == 'BUTTON') return;
            event.stopEvent();
            mouse.drag = false;
            mouse.down = true;
            mouse.downX = event.getPageX() - this.getX();
            mouse.downY = event.getPageY() - this.getY();
        });
        body.on('touchmove', function(event) {
            mouse.x = event.getPageX() - this.getX();
            mouse.y = event.getPageY() - this.getY();
            if (!mouse.down) return;
            if (!mouse.drag) {
                mouse.drag = true;
                self.fireEvent('dragStarted', {
                    x: mouse.x,
                    y: mouse.y
                });
                return
            }
            self.fireEvent('dragged', {
                x1: mouse.downX,
                y1: mouse.downY,
                x2: mouse.x,
                y2: mouse.y
            })
        });
        body.on('touchend', function(event) {
            if (!mouse.down) return;
            mouse.down = false;
            if (mouse.drag) {
                mouse.drag = false;
                self.fireEvent('dragEnded', {
                    x: mouse.x,
                    y: mouse.y
                })
            } else {
                self.fireEvent('released', mouse)
            };
        });
    } else {
        body.on('mousedown', function(event) {
            if (event.button != 0 && !('ontouchstart' in window)) return;
            if (event.target.tagName == 'INPUT') return;
            event.stopEvent();
            mouse.drag = false;
            mouse.down = true;
            mouse.downX = mouse.x;
            mouse.downY = mouse.y
        });
        body.on('mousemove', function(event) {
            mouse.x = event.getPageX() - this.getX();
            mouse.y = event.getPageY() - this.getY();
            if (!mouse.down) return;
            if (!mouse.drag) {
                mouse.drag = true;
                self.fireEvent('dragStarted', {
                    x: mouse.x,
                    y: mouse.y
                });
                return
            }
            self.fireEvent('dragged', {
                x1: mouse.downX,
                y1: mouse.downY,
                x2: mouse.x,
                y2: mouse.y
            })
        });
        body.on('mouseup', function(event) {
            if (event.button != 0 && !('ontouchstart' in window)) return;
            if (!mouse.down) return;
            mouse.down = false;
            if (mouse.drag) {
                mouse.drag = false;
                self.fireEvent('dragEnded', {
                    x: mouse.x,
                    y: mouse.y
                })
            } else {
                self.fireEvent('released', mouse)
            }
        });
    };
    Ext.EventManager.addListener(window, 'keydown', function() {
        if (mouse.drag) {
            mouse.drag = false;
            mouse.down = false;
            self.fireEvent('dragCancelled')
        }
    });
    this.setDragMode = function(mode, broadcast) {
        if (mouse.drag) {
            mouse.drag = false;
            self.fireEvent('dragCancelled')
        }
        if (mode == dragMode) return;
        switch (mode) {
            case 'browse':
                dragMode = mode;
                break;
            case 'zoom':
                dragMode = mode;
                break;
            case 'scale':
                dragMode = mode;
                break;
            case 'resize':
                dragMode = mode;
                break;
            case 'highlight':
                dragMode = mode;
                break;
        }
        if (broadcast) {
            self.fireEvent('dragModeSet', dragMode)
        }
    };
    this.getDragMode = function() {
        return dragMode
    };
    Ext.EventManager.addListener(window, 'keyup', function(event) {
        if (event.getTarget().tagName == 'INPUT') return;
        switch (event.getKey()) {
            case 66:
                self.setDragMode('browse', true);
                break;
            case 72:
                self.setDragMode('highlight', true);
                break;
            case 82:
                self.setDragMode('resize', true);
                break;
            case 83:
                self.setDragMode('scale', true);
                break;
            case 90:
                self.setDragMode('zoom', true);
                break;
            default:
                self.setDragMode('browse', true);
                break;
        }
    });
    if (!('ontouchstart' in window))
        this.MouseLabel = (function() {
            var ext = Ext.get(document.createElement('DIV'));
            ext.addClass('AJ_mouse_label');
            ext.appendTo(body);
            show(ext);
            body.on('mousemove', function(event) {
                event.stopEvent();
                var offset = AnnoJ.bases2pixels(getEdges().g1);
                if (mouse.drag) {
                    if (dragMode == 'zoom') {
                        show();
                        showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset) + '</div>&darr;')
                    } else if (dragMode == 'scale') {
                        hide()
                    } else {
                        show()
                    }
                } else {
                    show();
                    var pp = cursor.offsetTop - cursor.offsetHeight / 2;
                    var max;
                    var conf = findConf(cursor.id);
                    if (AnnoJ.config.settings.scale == 0) max = AnnoJ.config.maxlist[cursor.id];
                    else if (AnnoJ.config.settings.scale == 1) max = AnnoJ.config.max;
                    else max = AnnoJ.config.settings.yaxis / conf.scale;
                    var scroll_width = (body.dom.offsetWidth - body.dom.clientWidth) / 2;
                    InfoRequest.position = AnnoJ.pixels2bases(mouse.x + offset + scroll_width);
                    if (cursor.type == 'HiCTrack') {
                        var trk = AnnoJ.getGUI().Tracks.tracks.find('id', cursor.id);
                        var data = trk.getData();
                        if (data['resolution'] && data['resolution'][2]) {
                            var bin = data['resolution'][2];
                            var x = AnnoJ.pixels2bases(offset + mouse.x + scroll_width);
                            var idx = Math.floor(x / bin);
                            if (conf.style == 1) {
                                var y = Math.floor(bin / conf.unity * (cursor.offsetHeight - cursor.offsetTop)) + trk.config.indexy * bin;
                                var idy = trk.config.indexy + Math.floor((cursor.offsetHeight - cursor.offsetTop) / conf.unity);
                                if (data[idx] && data[idx][idy]) {
                                    var val = data[idx][idy];
                                    showText('<div>' + x + ',' + y + ',' + val + '</div>&darr;')
                                } else showText('<div>' + x + ',' + y + '</div>&darr;')
                            }
                            if (conf.style == 0) {
                                var x1 = x - AnnoJ.pixels2bases(cursor.offsetHeight - cursor.offsetTop);
                                var x2 = x + AnnoJ.pixels2bases(cursor.offsetHeight - cursor.offsetTop);
                                var i1 = Math.floor(x1 / bin);
                                var i2 = Math.floor(x2 / bin);
                                if (data[i1] && data[i1][i2]) {
                                    var val = data[i1][i2];
                                    showText('<div>' + x + ',' + x1 + ',' + x2 + ',' + val + '</div>&darr;')
                                } else showText('<div>' + x + ',' + x1 + ',' + x2 + '</div>&darr;')
                            }
                        } else if (data['pair']) {
                            var x = AnnoJ.pixels2bases(offset + mouse.x + scroll_width);
                            var y, y0 = trk.config.position - AnnoJ.pixels2bases(trk.config.height / 2);
                            if (!trk.config.flipY) y = y0 + AnnoJ.pixels2bases(cursor.offsetTop);
                            else y = y0 + AnnoJ.pixels2bases(cursor.offsetHeight - cursor.offsetTop);
                            showText('<div>x:' + x + ', y:' + y + '</div>&darr;')
                        } else {
                            var x = AnnoJ.pixels2bases(offset + mouse.x + scroll_width);
                            var x1 = x - AnnoJ.pixels2bases(cursor.offsetHeight - cursor.offsetTop);
                            var x2 = x + AnnoJ.pixels2bases(cursor.offsetHeight - cursor.offsetTop);
                            showText('<div>' + x + ',' + x1 + ',' + x2 + '</div>&darr;')
                        }
                    } else {
                        if (!max || !conf.scale || AnnoJ.config.settings.display == 1) {
                            showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset + scroll_width) + '</div>&darr;');
                        } else {
                            max /= conf.scale;
                            var ppos = Math.round(-pp * max * 20 / cursor.offsetHeight) / 10;
                            showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset + scroll_width) + ',' + ppos + '</div>&darr;');
                        }
                    }
                }
                ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2));
                ext.setTop(mouse.y - ext.getHeight() - 5)
            });

            function getEdges() {
                var half = Math.round(AnnoJ.pixels2bases(body.getWidth()) / 2);
                var view = AnnoJ.getLocation();
                return {
                    g1: view.position - half,
                    g2: view.position + half
                }
            };

            function showText(text) {
                ext.update(text);
                setDisplayed(true)
            };

            function showCoord() {
                var edges = self.getEdges();
                var offset = AnnoJ.bases2pixels(edges.g1);
                showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset) + '</div>&darr;')
            };

            function show_info() {
                info_ext.setDisplayed(true);
                info_ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2) + 2);
                info_ext.setTop(mouse.y - ext.getHeight() - 5)
            }

            function hide_info() {
                info_ext.setDisplayed(false)
            }

            function show() {
                ext.setDisplayed(true);
                ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2) + 2);
                ext.setTop(mouse.y - ext.getHeight() - 5)
            }

            function hide() {
                ext.setDisplayed(false)
            }

            function setDisplayed(state) {
                state ? show() : hide()
            };
            return {
                showText: showText,
                showCoord: showCoord,
                setDisplayed: setDisplayed,
                show: show,
                hide: hide
            }
        })();
    this.Scaler = (function() {
        var container = Ext.get(document.createElement('DIV'));
        container.setStyle('position', 'absolute');
        container.appendTo(body);
        var bg = Ext.get(document.createElement('DIV'));
        var fg = Ext.get(document.createElement('DIV'));
        bg.appendTo(container);
        fg.appendTo(container);
        bg.addClass('AJ_scaler_bg');
        fg.addClass('AJ_scaler_fg');
        bg.setStyle('position', 'absolute');
        fg.setStyle('position', 'absolute');
        fg.setBottom(0);
        fg.setLeft(0);
        bg.setTop(0);
        bg.setLeft(fg.getWidth());
        var track = null;
        var scale = 0.5;
        var start = 0.5;
        container.hide();

        function showAt(x, y) {
            track = self.tracks.mouse2track(x, y);
            if (!track || !track.getScale || !track.setScale) {
                track = null;
                return
            }
            var val = track.getScale();
            setScale(val);
            start = scale;
            fg.setLeft(0);
            bg.setLeft(fg.getWidth());
            container.show(true);
            container.setX(x - fg.getWidth() - Math.round(bg.getWidth() / 2));
            container.setY(y - Math.round((1 - scale) * bg.getHeight()))
        };

        function hide() {
            container.hide(true)
        };

        function update(offset) {
            var shift = offset / bg.getHeight();
            var target = start + shift;
            if (target < 0 || target > bg.getHeight()) return;
            setScale(target)
        };

        function setScale(v) {
            if (!track) return;
            if (v > 1) v = 1;
            if (v < 0) v = 0;
            var rounded = Math.round(1000 * v) / 1000;
            if (scale == rounded) {
                return
            }
            scale = rounded || 0.001;
            track.setScale(scale);
            var trackConfig = find(AnnoJ.config.tracks, 'id', track.config.id);
            if (!trackConfig) trackConfig = AnnoJ.config.tracks[0];
            trackConfig.scale = scale;
            track.Toolbar.setScale(trackConfig.scale, true);
            var px = bg.getHeight() - Math.round(scale * bg.getHeight());
            fg.setTop(px - Math.round(fg.getHeight() / 2))
        };

        function getScale() {
            return scale
        };
        body.on('mousedown', scalerMousedown);
        body.on('touchstart', scalerMousedown);

        function scalerMousedown(event) {
            if (event.button != 0 && !('ontouchstart' in window)) return;
            if (dragMode != 'scale') return;
            if (event.getTarget().tagName == 'INPUT') return;
            showAt(event.getPageX() - Math.round(bg.getWidth() / 2) - 2, event.getPageY());
            if (self.MouseLabel) self.MouseLabel.hide();
            if (self.CrossHairs) self.CrossHairs.hide()
        };
        body.on('mouseup', scalerMouseup);
        body.on('touchend', scalerMouseup);

        function scalerMouseup() {
            if (dragMode != 'scale') return;
            hide()
        };
        self.on('dragEnded', function() {
            if (dragMode != 'scale') return;
            hide()
        });
        self.on('dragged', function() {
            if (dragMode != 'scale') return;
            update(mouse.downY - mouse.y)
        });
        return {}
    })();
    this.Resizer = (function() {
        var box = Ext.get(document.createElement('DIV'));
        box.addClass('AJ_resizer');
        box.setStyle('position', 'absolute');
        box.appendTo(body);
        box.hide();
        var height = 0;
        var track = null;

        function bind(track) {
            box.setTop(track.ext.getY() - body.getY());
            box.setLeft(0);
            box.setWidth(track.ext.getWidth());
            box.setHeight(track.ext.getHeight());
            height = box.getHeight();
            show()
        };

        function show() {
            box.show()
        };

        function hide() {
            track = null;
            box.hide(true)
        };
        body.on('mousedown', resizerMousedown);
        body.on('touchstart', resizerMousedown);

        function resizerMousedown(event) {
            if (event.button != 0 && !('ontouchstart' in window)) return;
            if (dragMode != 'resize') return;
            if (event.getTarget().tagName == 'INPUT') return;
            track = self.tracks.mouse2track(event.getPageX(), event.getPageY());
            if (!track) {
                track = null;
                return
            }
            bind(track)
        };
        body.on('mouseup', resizerMouseup);
        body.on('touchend', resizerMouseup);

        function resizerMouseup(event) {
            if (dragMode != 'resize') return;
            if (event.getTarget().tagName == 'INPUT') return;
            if (!track) track = self.tracks.mouse2track(event.getPageX(), event.getPageY());
            track.setHeight(box.getHeight());
            hide()
        };
        self.on('dragged', function() {
            if (dragMode != 'resize' || !track) return;
            var h = height + mouse.y - mouse.downY;
            if (h < track.getMinHeight()) return;
            if (h > track.getMaxHeight()) return;
            box.setHeight(height + mouse.y - mouse.downY);
            track.setHeight(box.getHeight())
        });
        self.on('dragEnded', function() {
            if (dragMode != 'resize' || !track) return;
            track.setHeight(box.getHeight());
            hide()
        });
        self.on('dragCancelled', function() {
            if (dragMode != 'resize' || !track) return;
            hide()
        });
        return {
            show: show,
            hide: hide
        }
    })();
    if (!('ontouchstart' in window))
        this.CrossHairs = (function() {
            var gap = 5;
            var showNS = true;
            var showEW = false;
            var north = Ext.get(document.createElement('DIV'));
            var south = Ext.get(document.createElement('DIV'));
            var east = Ext.get(document.createElement('DIV'));
            var west = Ext.get(document.createElement('DIV'));
            north.addClass('AJ_crosshair');
            south.addClass('AJ_crosshair');
            east.addClass('AJ_crosshair');
            west.addClass('AJ_crosshair');
            north.setStyle({
                position: 'absolute',
                top: 0,
                width: 0,
                height: 0,
                borderLeft: 'dotted red 1px'
            });
            south.setStyle({
                position: 'absolute',
                top: 0,
                width: 0,
                height: '100%',
                borderLeft: 'dotted red 1px'
            });
            east.setStyle({
                position: 'absolute',
                left: 0,
                width: '100%',
                height: 0,
                borderTop: 'dotted red 1px'
            });
            west.setStyle({
                position: 'absolute',
                left: 0,
                width: 0,
                height: 0,
                borderTop: 'dotted red 1px'
            });
            north.appendTo(body);
            south.appendTo(body);
            east.appendTo(body);
            west.appendTo(body);
            toggleNS(showNS);
            toggleEW(showEW);

            function setGap(n) {
                gap = Math.max(parseInt(n) || 0, 0)
            };

            function toggleNS(state) {
                showNS = state ? true : false;
                north.setDisplayed(showNS);
                south.setDisplayed(showNS)
            };

            function toggleEW(state) {
                showEW = state ? true : false;
                east.setDisplayed(showEW);
                west.setDisplayed(showEW)
            };

            function setXY(x, y) {
                var x = Math.max(parseInt(x) || 0, 0);
                var y = Math.max(parseInt(y) || 0, 0);
                if (showNS) {
                    north.setLeft(x - 1);
                    south.setLeft(x - 1);
                    north.setHeight(y - gap);
                    south.setTop(y + gap)
                }
                if (showEW) {
                    east.setTop(y - 1);
                    west.setTop(y - 1);
                    east.setLeft(x + gap);
                    west.setWidth(x - gap)
                }
            };

            function show() {
                toggleNS(true);
                toggleEW(false)
            };

            function hide() {
                toggleNS(false);
                toggleEW(false)
            };
            body.on('mousemove', function(event) {
                if (mouse.drag) {
                    if (dragMode == 'zoom' || dragMode == 'scale') {
                        toggleNS(false);
                        toggleEW(false);
                        return
                    }
                }
                toggleNS(true);
                setXY(mouse.x, mouse.y)
            });
            return {
                setGap: setGap,
                toggleNS: toggleNS,
                toggleEW: toggleEW,
                setXY: setXY,
                show: show,
                hide: hide
            }
        })();
    this.Region = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_region_indicator');
        ext.appendTo(body);
        ext.setDisplayed(false);

        function show() {
            ext.setDisplayed(true)
        };

        function hide() {
            ext.setDisplayed(false)
        };

        function setBox(box) {
            ext.setLeft(box.x1);
            ext.setTop(box.y1);
            ext.setWidth(box.x2 - box.x1);
            ext.setHeight(box.y2 - box.y1)
        };

        function getBox() {
            return {
                x1: ext.getLeft(true),
                x2: ext.getLeft(true) + ext.getWidth(),
                y1: ext.getTop(true),
                y2: ext.getTop(true) + ext.getHeight()
            }
        };

        function mouse2box() {
            var x1 = mouse.downX;
            var x2 = mouse.x;
            var y1 = mouse.downY;
            var y2 = mouse.y;
            if (x1 > x2) {
                var temp = x1;
                x1 = x2;
                x2 = temp
            }
            if (y1 > y2) {
                var temp = y1;
                y1 = y2;
                y2 = temp
            }
            return {
                x1: x1,
                x2: x2,
                y1: y1,
                y2: y2
            }
        };
        self.on('dragStarted', function() {
            if (dragMode != 'zoom') return;
            setBox({
                x1: mouse.downX,
                y1: mouse.downY,
                x2: mouse.downX,
                y2: mouse.downY,
            });
            show()
        });
        self.on('dragEnded', function() {
            var box = getBox();
            if (dragMode == 'browse') {
                hide();
                var loc = AnnoJ.getLocation();
                loc.position -= AnnoJ.pixels2bases(mouse.x - mouse.downX);
                loc = AnnoJ.setLocation(loc);
                var mov = mouse.y - mouse.downY;
                body.dom.scrollTop -= mov;
                self.tracks.each(function(track) {
                    track.moveCanvas(0, 0);
                    track.setLocation(loc)
                })
            }
            if (dragMode == 'zoom') {
                var left = ext.getLeft(true);
                var width = ext.getWidth();
                var loc = AnnoJ.getLocation();
                hide();
                if (width < 10) {
                    var height = mouse.y - mouse.downY;
                    if (height < 10 && height > -10) return;
                    var z = Math.floor(height / 10);
                    var closer = (z > 0) ? 1 : 0;
                    var x = [loc.bases, loc.pixels, closer];
                    for (var i = 0; i < Math.abs(z); i += 2) {
                        x = Math.step(x);
                    }
                    loc.bases = x[0];
                    loc.pixels = x[1];
                    loc = AnnoJ.setLocation(loc);
                    self.tracks.setLocation(loc);
                } else {
                    loc.position = AnnoJ.xpos2gpos(Math.round((box.x1 + box.x2) / 2));
                    loc.bases = AnnoJ.pixels2bases(box.x2 - box.x1);
                    loc.pixels = body.getWidth();
                    loc = AnnoJ.setLocation(loc);
                    self.tracks.setLocation(loc)
                }
            }
        });
        self.on('dragged', function() {
            var box = mouse2box();
            if (dragMode == 'browse') {
                ext.setLeft(mouse.x - mouse.downX);
                ext.setTop(mouse.y - mouse.downY);
                self.tracks.each(function(track) {
                    track.moveCanvas(mouse.x - mouse.downX, mouse.y - mouse.downY)
                });
                return
            }
            if (dragMode == 'zoom') {
                box.y1 = 0;
                box.y2 = body.getHeight();
                setBox(box);
                return
            }
        });
        self.on('dragCancelled', function() {
            hide()
        });
        return {
            hide: hide,
            show: show,
            setBox: setBox,
            getBox: getBox
        }
    })();
    this.Highlights = (function() {
        var size = AnnoJ.config.settings.highlight.size || 11;
        var maxSize = 21,
            color = 0,
            exts = [],
            colors = [];
        for (var i = 0; i < maxSize; i++) {
            colors[i] = AnnoJ.config.settings.highlight.color;
            exts[i] = highlight(i);
        };

        function highlight(i) {
            var exti = Ext.get(document.createElement('DIV'));
            exti.addClass('AJ_region_highlight_' + colors[i].toString());
            exti.appendTo(body);
            exti.setDisplayed(false);
            return exti
        }

        function show() {
            var newSize = AnnoJ.config.settings.highlight.size;
            if (size > newSize) {
                for (var i = size - 1; i >= newSize; i--) {
                    exts[i].setDisplayed(false);
                }
            }
            size = newSize;
            if (color >= size) color = size - 1;
            exts[color].removeClass('AJ_region_highlight_' + colors[color]);
            exts[color].addClass('AJ_region_highlight_' + AnnoJ.config.settings.highlight.color);
            exts[color].setDisplayed(true);
        };

        function hide() {
            for (var i = 0; i < size; i++) {
                if (exts[i]) exts[i].setDisplayed(false);
            }
        };

        function setBox(box) {
            exts[color].setLeft(box.x1);
            exts[color].setTop(box.y1);
            exts[color].setWidth(box.x2 - box.x1);
            exts[color].setHeight(box.y2 - box.y1)
        };

        function getBox() {
            return {
                x1: exts[color].getLeft(true),
                x2: exts[color].getLeft(true) + exts[color].getWidth(),
                y1: exts[color].getTop(true),
                y2: exts[color].getTop(true) + exts[color].getHeight()
            }
        };

        function mouse2box() {
            var x1 = mouse.downX;
            var x2 = mouse.x;
            var y1 = mouse.downY;
            var y2 = mouse.y;
            return {
                x1: x1,
                x2: x2,
                y1: y1,
                y2: y2
            }
        };
        self.on('dragStarted', function() {
            if (dragMode != 'highlight') {
                hide();
                return
            };
            setBox({
                x1: mouse.downX,
                y1: mouse.downY,
                x2: mouse.downX,
                y2: mouse.downY,
            });
            show();
        });
        self.on('dragEnded', function() {
            var removeHighlight = 0,
                box = getBox();
            if (dragMode != 'highlight') {
                return
            }
            if (mouse.x < mouse.downX) {
                for (var i = 0, j; i < size; i++) {
                    j = (color + size - 1 - i) % size;
                    if (mouse.downX > exts[j].getLeft(true) && mouse.x < exts[j].getLeft(true) + exts[j].getWidth()) {
                        exts[j].removeClass('AJ_region_highlight_' + colors[j]);
                        colors[j] = AnnoJ.config.settings.highlight.color;
                        exts[j].setDisplayed(false);
                        removeHighlight++;
                    }
                }
                if (removeHighlight) return;
            } else {
                colors[color] = AnnoJ.config.settings.highlight.color;
            }
            var nBar = AnnoJ.getGUI().NavBar;
            var hBox = {
                assembly: nBar.getLocation().assembly,
                position: nBar.getLocation().position,
                regions: [],
                xpos: []
            }
            for (var i = 0, k = 0, left, right, xpos; i < size; i++) {
                if (exts[i] && exts[i].isVisible()) {
                    xpos = exts[i].getLeft() - AnnoJ.getGUI().Tracks.getBox().x;
                    left = nBar.xpos2gpos(xpos);
                    right = left + nBar.pixels2bases(exts[i].getWidth());
                    hBox.regions[k] = left + '-' + right;
                    hBox.xpos[k] = [xpos, exts[i].getWidth()];
                    k++;
                }
            }
            Ext.apply(AnnoJ.config.settings.highlight, hBox);
            for (var i = 0; i < size; i++) {
                if (!exts[i].isVisible()) {
                    color = i;
                    break;
                }
            }
        });
        self.on('dragged', function() {
            if (mouse.x < mouse.downX) return;
            var box = mouse2box();
            if (dragMode == 'highlight') {
                box.y1 = 0;
                box.y2 = body.getHeight();
                setBox(box);
                return
            }
        });
        self.on('dragCancelled', function() {
            hide()
        });
        return {
            hide: hide,
            show: show
        }
    })();
    this.tracks = (function() {
        var active = [];
        var tracks = [];
        var enabled = [];
        var disabled = [];
        var timer = null;
        var focused = null;
        Ext.EventManager.addListener(body.dom, 'scroll', function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        Ext.EventManager.addListener(window, 'scroll', function() {
            event.preventDefault();
        });
        Ext.EventManager.addListener(window, 'resize', function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });

        function refresh() {
            clearTimeout(timer);
            var view = getLocation();
            disabled = [];
            enabled = [];
            Ext.each(active, function(track) {
                if (onscreen(track)) {
                    if (track.Syndicator.isSyndicated()) track.unmask();
                    enabled.push(track)
                } else {
                    track.mask('Track temporarily disabled');
                    disabled.push(track)
                }
                track.setLocation(view)
            })
        };

        function onscreen(track) {
            if (body.getTop() > track.ext.getBottom()) return false;
            if (body.getBottom() < track.ext.getTop()) return false;
            return true
        };

        function manage(track) {
            if (!track instanceof AnnoJ.BaseTrack && !track.is.Proxy) return;
            if (AnnoJ.isReady && isManaged(track)) return;
            tracks.push(track);
            if (track instanceof AnnoJ.BaseTrack) {
                track.on('generic', propagate);
                track.on('close', close);
                track.on('browse', setLocation);
                track.on('error', error);
            }
        };

        function unmanage(track) {
            if (!track instanceof AnnoJ.Tracks) return;
            close(track);
            tracks.remove(track);
            track.un('generic', propagate);
            track.un('close', close);
            track.un('browse', setLocation);
            track.un('error', error)
        };

        function isManaged(track) {
            return tracks.search(track) != -1
        };

        function mouse2track(x, y) {
            var track = null;
            Ext.each(active, function(item) {
                var x1 = item.ext.getX();
                var x2 = x1 + item.ext.getWidth();
                var y1 = item.ext.getY();
                var y2 = y1 + item.ext.getHeight();
                if (x >= x1 && x <= x2 && y >= y1 && y <= y2) {
                    track = item;
                    return false
                }
            });
            return track
        };

        function isActive(track) {
            return active.search(track) != -1
        };

        function open(track, existing) {
            if (track.isProxy) {
                var index = tracks.search(track);
                var itrack = new AnnoJ[track.config.type](track.config);
                var node = track.node;
                node.track = itrack;
                itrack.node = node;
                track = itrack;
                tracks[index] = track;
                track.setLocation(AnnoJ.getLocation());
                track.on('close', myclose);
                track.on('generic', propagate);
                track.on('browse', setLocation);
                track.on('error', error);
                track.on('describe', function(syndication) {
                    AnnoJ.getGUI().InfoBox.echo(BaseJS.toHTML(syndication));
                });
            }
            if (!track.ext.isVisible()) {
                track.ext.setHeight(track.config.height);
                track.ext.setVisible(true)
            }
            if (!isManaged(track)) return;
            if (isActive(track)) return;
            active.push(track);
            if (existing) {
                track.insertBefore(existing.ext)
            } else {
                track.appendTo(body.dom)
            }
            if (!track.Syndicator.isSyndicated()) {
                track.Syndicator.syndicate({
                    success: function() {
                        track.setLocation(AnnoJ.getLocation())
                    },
                    failure: function() {
                        track.mask('Error: track failed to syndicate');
                        AnnoJ.error("Track '" + track.getId() + "' failed to syndicate");
                        close(track)
                    }
                })
            } else {
                track.unmask();
                track.setLocation(AnnoJ.getLocation())
            }
            refresh()
        };

        function myclose(track) {
            if (!isActive(track)) return;
            active.remove(track);
            enabled.remove(track);
            disabled.remove(track);
            var node = track.node;
            node.originalParent.appendChild(node);
            track.ext.setVisibilityMode(Ext.Element.DISPLAY);
            track.ext.setVisible(false);
            if (AnnoJ.config.trks) {
                var exists = AnnoJ.config.trks.indexOf(track.config.id);
                if (exists >= 0) AnnoJ.config.trks.splice(exists, 1);
            }
            refresh()
        };

        function close(track) {
            if (!isActive(track)) return;
            active.remove(track);
            enabled.remove(track);
            disabled.remove(track);
            track.ext.setVisibilityMode(Ext.Element.DISPLAY);
            track.ext.setVisible(false);
            AnnoJ.config.active.remove(track.config.id);
            if (AnnoJ.config.trks) {
                var exists = AnnoJ.config.trks.indexOf(track.config.id);
                if (exists >= 0) AnnoJ.config.trks.splice(exists, 1);
            }
            if (track.config.id.indexOf('new-') >= 0 || track.config.action2) {
                var trk = window.find(AnnoJ.config.tracks, 'id', track.config.id);
                AnnoJ.config.tracks.remove(trk);
                tracks.remove(track);
                track.un('generic', propagate);
                track.un('close', close);
                track.un('browse', setLocation);
                track.un('error', error)
            }
            refresh()
        };

        function reorder(track, existing) {
            if (existing) {
                track.insertBefore(existing.ext);
            } else {
                track.appendTo(body.dom)
            }
            AnnoJ.config.active.remove(track.config.id);
            var index = AnnoJ.config.active.length;
            if (existing) index = AnnoJ.config.active.search(existing.config.id);
            AnnoJ.config.active.insert(index, track.config.id);
            refresh()
        };

        function insert(track, existing) {
            if (!isManaged(track)) return;
            if (isActive(track)) return;
            active.push(track);
            if (existing) {
                track.insertBefore(existing.ext)
            } else {
                track.appendTo(body.dom)
            }
        };

        function closeAll() {
            Ext.each(active, close)
        };

        function error(track, message) {
            AnnoJ.error('An error was generated by track: ' + track.name + '.<br />The track has been removed from the display.<br />Error: ' + message);
            close(track)
        };

        function propagate(type, data) {
            Ext.each(enabled, function(item) {
                item.receive(type, data)
            })
        };

        function setLocation(view) {
            var view = AnnoJ.setLocation(view);
            Ext.each(enabled, function(track) {
                track.setLocation(view)
            })
        };

        function getLocation() {
            return AnnoJ.getLocation()
        };

        function clear() {
            while (tracks.length) unmanage(tracks[0])
        };

        function find(param, value) {
            var hit = null;
            Ext.each(tracks, function(track) {
                if (track.config[param] && track.config[param] == value) {
                    hit = track;
                    return false
                }
            });
            return hit
        };

        function getConfigs() {
            var list = [];
            Ext.each(tracks, function(track) {
                list.push(track.config)
            });
            return list
        };

        function each(func) {
            Ext.each(active, func)
        };
        return {
            manage: manage,
            unmanage: unmanage,
            refresh: refresh,
            clear: clear,
            setLocation: setLocation,
            getLocation: getLocation,
            open: open,
            close: close,
            myclose: myclose,
            reorder: reorder,
            insert: insert,
            body: body,
            isActive: isActive,
            find: find,
            tracks: tracks,
            getConfigs: getConfigs,
            closeAll: closeAll,
            each: each,
            mouse2track: mouse2track
        }
    })()
};
Ext.extend(AnnoJ.Tracks, Ext.Panel);
AnnoJ.TracksInfo = function(userConfig) {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_tracks');
    var defaultConfig = {
        title: 'Additional Information',
        region: 'south',
        iconCls: 'silk_bricks',
        deferredRender: true,
        contentEl: body,
        autoScroll: false,
        margin: '0 0 0 0'
    };
    var config = defaultConfig;
    Ext.apply(config, userConfig || {}, defaultConfig);
    AnnoJ.TracksInfo.superclass.constructor.call(this, config);
    this.tracks = (function() {
        var active = [];
        var tracks = [];
        var enabled = [];
        var disabled = [];
        var timer = null;
        var focused = null;
        Ext.EventManager.addListener(body.dom, 'scroll', function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        Ext.EventManager.addListener(window, 'resize', function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });

        function refresh() {
            clearTimeout(timer);
            var view = getLocation();
            disabled = [];
            enabled = [];
            Ext.each(tracks, function(track) {
                if (onscreen(track)) {
                    if (track.Syndicator.isSyndicated()) track.unmask();
                    enabled.push(track)
                } else {
                    track.mask('Track temporarily disabled');
                    disabled.push(track)
                }
                track.setLocation(view)
            })
        };

        function onscreen(track) {
            if (body.getTop() > track.ext.getBottom()) return false;
            if (body.getBottom() < track.ext.getTop()) return false;
            return true
        };

        function manage(track) {
            if (!track instanceof AnnoJ.BaseTrack && !track.isProxy) return;
            if (isManaged(track)) return;
            tracks.push(track);
            if (!track.isProxy) {
                track.on('generic', propagate);
                track.on('close', close);
                track.on('browse', setLocation);
                track.on('error', error)
            }
        };

        function unmanage(track) {
            if (!track instanceof AnnoJ.Tracks) return;
            close(track);
            tracks.remove(track);
            track.un('generic', propagate);
            track.un('close', close);
            track.un('browse', setLocation);
            track.un('error', error)
        };

        function isManaged(track) {
            return tracks.search(track) != -1
        };

        function open(track) {
            track.appendTo(body.dom)
        };

        function genid() {
            var id = 'trackxxxx-' + tracks.length;
            return id
        };

        function setLocation(view) {
            var view = AnnoJ.setLocation(view);
            Ext.each(enabled, function(track) {
                track.setLocation(view)
            })
        };

        function getLocation() {
            return AnnoJ.getLocation()
        };
        return {
            manage: manage,
            unmanage: unmanage,
            setLocation: setLocation,
            getLocation: getLocation,
            open: open,
            genid: genid,
            body: body,
            tracks: tracks
        }
    })()
};
Ext.extend(AnnoJ.TracksInfo, Ext.Panel);
AnnoJ.Bugs = (function() {
    var body = Ext.get(document.createElement('DIV'));
    body.addClass('AJ_bugs');
    var buglist = Ext.get(document.createElement('DIV'));
    var report = new Ext.form.TextArea();
    buglist.appendTo(body);
    return function() {
        AnnoJ.Bugs.superclass.constructor.call(this, {
            title: 'Bugs',
            iconCls: 'silk_bug',
            border: false,
            contentEl: body,
            autoScroll: true
        })
    }
})();
Ext.extend(AnnoJ.Bugs, Ext.Panel);
AnnoJ.Helpers.List = function() {
    var self = this;
    this.first = null;
    this.last = null;
    this.count = 0;
    var Node = function(item) {
        this.next = null;
        this.prev = null;
        this.value = item
    };
    this.insertFirst = function(item) {
        var node = new Node(item);
        if (!self.first) {
            self.first = node;
            self.last = node
        } else {
            node.next = self.first;
            node.prev = null;
            node.next.prev = node;
            self.first = node
        }
        self.count++
    };
    this.insertLast = function(item) {
        var node = new Node(item);
        if (!self.last) {
            self.first = node;
            self.last = node
        } else {
            node.next = null;
            node.prev = self.last;
            node.prev.next = node;
            self.last = node
        }
        self.count++
    };
    this.insertBefore = function(existing, item) {
        if (existing == null) {
            self.insertLast(item);
            return
        }
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.first) {
            self.insertFirst(item);
            return
        }
        var node = new Node(item);
        node.next = existing;
        node.prev = existing.prev;
        node.next.prev = node;
        node.prev.next = node;
        self.count++
    };
    this.insertAfter = function(existing, item) {
        if (existing == null) {
            self.insertFirst(item);
            return
        }
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.last) {
            self.insertLast(item);
            return
        }
        var node = new Node(item);
        node.prev = existing;
        node.next = existing.next;
        node.next.prev = node;
        node.prev.next = node;
        self.count++
    };
    this.remove = function(existing) {
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.first && existing == self.last) {
            self.first = null;
            self.last = null
        } else if (existing == self.first) {
            self.first = existing.next;
            self.first.prev = null
        } else if (existing == self.last) {
            self.last = existing.prev;
            self.last.next = null
        } else {
            existing.next.prev = existing.prev;
            existing.prev.next = existing.next
        }
        existing.prev = null;
        existing.next = null;
        temp = existing.value;
        delete existing;
        self.count--;
        return temp
    };
    this.clear = function() {
        var vals = [];
        while (self.first) {
            vals.push(remove(self.first))
        }
        self.count = 0;
        return vals
    };
    this.apply = function(func) {
        if (func == undefined || !(func instanceof Function)) {
            return
        }
        for (var node = self.first; node; node = node.next) {
            if (!func(node.value)) {
                break
            }
        }
    };
    this.find = function(value) {
        for (var node = self.first; node; node = node.next) {
            if (node.value == value) {
                return node
            }
        }
        return null
    }
};
var PointList = function() {
    var index = {};
    var count = 0;
    var self = this;
    var first = null;
    var last = null;
    var viewL = null;
    var viewR = null;
    var PointNode = function(id, x, item) {
        this.id = id || '';
        this.x = parseInt(x) || 0;
        this.value = item || null;
        this.next = null;
        this.prev = null
    };
    this.getCount = function() {
        return count
    };
    this.getFirst = function() {
        return first
    };
    this.getLast = function() {
        return last
    };
    this.getIndex = function() {
        return index
    };
    this.createNode = function(id, x, item) {
        return new PointNode(id, x, item)
    };
    this.clear = function() {
        while (first) {
            self.remove(first)
        }
    };
    this.prune = function(x1, x2) {
        while (first && first.x < x1) {
            self.remove(first)
        }
        while (last && last.x > x2) {
            self.remove(last)
        }
    };
    this.parse = function(data) {};
    this.subset = function(x1, x2) {
        var data = [];
        var x1 = parseInt(x1) || 0;
        var x2 = parseInt(x2) || 0;
        if (x1 > x2) return data;
        for (var node = first; node; node = node.next) {
            if (node.x < x1) continue;
            if (node.x > x2) break;
            data.push(node.value)
        }
        return data
    };
    this.apply = function(func, x1, x2) {
        if (!(func instanceof Function)) return;
        var x1 = parseInt(x1) || first.x;
        var x2 = parseInt(x2) || last.x;
        if (x1 > x2) return;
        for (var node = first; node; node = node.next) {
            if (node.x < x1) continue;
            while (node) {
                if (node.x > x2) break;
                func(node);
                node = node.next
            }
            break
        }
    };
    this.insert = function(node) {
        if (!(node instanceof PointNode)) return;
        if (index[node.id]) {
            index[node.id].value = node.value;
            return
        }
        index[node.id] = node;
        if (count == 0) {
            first = node;
            last = node;
            count = 1;
            return
        }
        if (node.x <= first.x) {
            node.next = first;
            first.prev = node;
            first = node;
        } else if (node.x >= last.x) {
            if (node.x == last.x + last.value.w && node.value.y == last.value.y) {
                last.value.w += node.value.w;
                delete index[node.id];
                delete node;
                return
            }
            node.next = null;
            node.prev = last;
            node.prev.next = node;
            last = node
        } else {
            if (Math.abs(node.x - first.x) < Math.abs(node.x - last.x)) {
                for (var existing = first; existing; existing = existing.next) {
                    if (node.x <= existing.x) {
                        node.next = existing;
                        node.prev = existing.prev;
                        node.next.prev = node;
                        node.prev.next = node;
                        break
                    }
                }
            } else {
                for (var existing = last; existing; existing = existing.prev) {
                    if (node.x >= existing.x) {
                        node.next = existing.next;
                        node.prev = existing;
                        node.next.prev = node;
                        node.prev.next = node;
                        break
                    }
                }
            }
        }
        count++
    };
    this.insertPoints = function(array) {
        var len = array.length;
        if (len == 0) return;
        if (count > 0 && Math.abs(array[0].x - first.x) < Math.abs(array[0].x - last.x - last.value.w)) {
            for (var i = len - 1; i >= 0; i--) {
                self.insert(array[i])
            }
        } else {
            for (var i = 0; i < len; i++) {
                self.insert(array[i])
            }
        }
    };
    this.remove = function(node) {
        if (!(node instanceof PointNode)) return;
        if (!index[node.id]) return;
        if (count == 0) return;
        if (count == 1) {
            first = null;
            last = null;
            viewL = null;
            viewR = null
        } else {
            if (node == viewL) {
                viewL = node.next
            }
            if (node == viewR) {
                viewR = node.prev
            }
            if (node == first) {
                first = node.next;
                first.prev = null
            } else if (node == last) {
                last = node.prev;
                last.next = null
            } else {
                node.prev.next = node.next;
                node.next.prev = node.prev
            }
        }
        node.prev = null;
        node.next = null;
        delete index[node.id];
        delete node;
        count--
    };
    this.viewport = (function() {
        function get() {
            return {
                left: viewL,
                right: viewR
            }
        };

        function set(x1, x2) {
            if (count == 0) {
                clear();
                return
            }
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            for (var node = first; node; node = node.next) {
                if (node.x + node.value.w < x1) continue;
                viewL = node;
                while (node) {
                    if (node.x > x2) break;
                    viewR = node;
                    node = node.next
                }
                break
            }
        };

        function update(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            if (!viewL || !viewR) {
                set(x1, x2);
                return
            }
            while (viewL && viewL.x < x1) {
                viewL = viewL.next
            }
            while (viewR && viewR.x > x2) {
                viewR = viewR.prev
            }
            while (viewL && viewL.prev && viewL.prev.x + viewL.prev.value.w >= x1) {
                viewL = viewL.prev
            }
            while (viewR && viewR.next && viewR.next.x <= x2) {
                viewR = viewR.next
            }
        };

        function clear() {
            viewL = null;
            viewR = null
        };

        function apply(func) {
            if (!(func instanceof Function)) return;
            for (var node = viewL; node; node = node.next) {
                func(node.value);
                if (node == viewR) break
            }
        };
        return {
            get: get,
            set: set,
            clear: clear,
            update: update,
            apply: apply
        }
    })()
};
var RangeList = function() {
    var index = {};
    var count = 0;
    var self = this;
    var firstL = null;
    var firstR = null;
    var lastL = null;
    var lastR = null;
    var viewL = null;
    var viewR = null;
    var RangeNode = function(id, x1, x2, item) {
        this.id = id || '';
        this.x1 = parseInt(x1) || 0;
        this.x2 = parseInt(x2) || 0;
        this.value = item || null;
        this.level = -1;
        this.nextL = null;
        this.nextR = null;
        this.prevL = null;
        this.prevR = null
    };
    this.getCount = function() {
        return count
    };
    this.getFirstL = function() {
        return firstL
    };
    this.getFirstR = function() {
        return firstR
    };
    this.getLastL = function() {
        return lastL
    };
    this.getLastR = function() {
        return lastR
    };
    this.getIndex = function() {
        return index
    };
    this.createNode = function(id, x1, x2, item) {
        return new RangeNode(id, x1, x2, item)
    };
    this.exists = function(id) {
        if (!id) return false;
        return index[id] ? true : false
    };
    this.getNode = function(id) {
        if (!id) return null;
        return index[id] || null
    };
    this.getValue = function(id) {
        if (!id) return null;
        if (!index[id]) return null;
        return index[id].value
    };
    this.clear = function() {
        while (firstL) {
            self.remove(firstL)
        }
    };
    this.prune = function(x1, x2) {
        while (firstR && firstR.x2 < x1) {
            self.remove(firstR)
        }
        while (lastL && lastL.x1 > x2) {
            self.remove(lastL)
        }
    };
    this.parse = function(data) {};
    this.apply = function(func) {
        if (!(func instanceof Function)) return;
        for (var node = firstL; node; node = node.nextL) {
            func(node)
        }
    };
    this.insert = function(node) {
        if (!(node instanceof RangeNode)) return;
        if (index[node.id]) {
            index[node.id].value = node.value;
            return
        }
        index[node.id] = node;
        if (count == 0) {
            firstL = node;
            firstR = node;
            lastL = node;
            lastR = node;
            count = 1;
            return
        }
        if (node.x1 < firstL.x1 || (node.x1 == firstL.x1 && node.x2 <= firstL.x2)) {
            node.nextL = firstL;
            firstL.prevL = node;
            firstL = node
        } else if (node.x1 > lastL.x1 || (node.x1 == lastL.x1 && node.x2 >= lastL.x2)) {
            node.nextL = null;
            node.prevL = lastL;
            node.prevL.nextL = node;
            lastL = node
        } else {
            if (Math.abs(node.x1 - firstL.x1) < Math.abs(node.x1 - lastL.x1)) {
                for (var existing = firstL; existing; existing = existing.nextL) {
                    if (node.x1 < existing.x1 || (node.x1 == existing.x1 && node.x2 <= existing.x2)) {
                        node.nextL = existing;
                        node.prevL = existing.prevL;
                        node.nextL.prevL = node;
                        node.prevL.nextL = node;
                        break
                    }
                }
            } else {
                for (var existing = lastL; existing; existing = existing.prevL) {
                    if (node.x1 > existing.x1 || (node.x1 == existing.x1 && node.x2 >= existing.x2)) {
                        node.nextL = existing.nextL;
                        node.prevL = existing;
                        node.nextL.prevL = node;
                        node.prevL.nextL = node;
                        break
                    }
                }
            }
        }
        if (node.x2 < firstR.x2 || (node.x2 == firstR.x2 && node.x1 <= firstR.x1)) {
            node.nextR = firstR;
            firstR.prevR = node;
            firstR = node
        } else if (node.x2 > lastR.x2 || (node.x2 == lastR.x2 && node.x1 >= lastR.x1)) {
            node.nextR = null;
            node.prevR = lastR;
            node.prevR.nextR = node;
            lastR = node
        } else {
            if (Math.abs(node.x2 - firstR.x2) < Math.abs(node.x2 - lastR.x2)) {
                for (var existing = firstR; existing; existing = existing.nextR) {
                    if (node.x2 < existing.x2 || (node.x2 == existing.x2 && node.x1 <= existing.x1)) {
                        node.nextR = existing;
                        node.prevR = existing.prevR;
                        node.nextR.prevR = node;
                        node.prevR.nextR = node;
                        break
                    }
                }
            } else {
                for (var existing = lastR; existing; existing = existing.prevR) {
                    if (node.x2 > existing.x2 || (node.x2 == existing.x2 && node.x1 >= existing.x1)) {
                        node.nextR = existing.nextR;
                        node.prevR = existing;
                        node.nextR.prevR = node;
                        node.prevR.nextR = node;
                        break
                    }
                }
            }
        }
        count++
    };
    this.remove = function(node) {
        if (!(node instanceof RangeNode)) return;
        if (!index[node.id]) return;
        if (count == 0) return;
        if (node == viewL) viewL = node.nextR;
        if (node == viewR) viewR = node.prevL;
        if (count == 1) {
            firstL = null;
            firstR = null;
            lastL = null;
            lastR = null
        } else {
            if (node == firstL) {
                firstL = node.nextL;
                firstL.prevL = null
            } else if (node == lastL) {
                lastL = node.prevL;
                lastL.nextL = null
            } else {
                node.prevL.nextL = node.nextL;
                node.nextL.prevL = node.prevL
            }
            if (node == firstR) {
                firstR = node.nextR;
                firstR.prevR = null
            } else if (node == lastR) {
                lastR = node.prevR;
                lastR.nextR = null
            } else {
                node.prevR.nextR = node.nextR;
                node.nextR.prevR = node.prevR
            }
        }
        node.prevL = null;
        node.nextL = null;
        node.prevR = null;
        node.nextR = null;
        delete index[node.id];
        delete node;
        count--
    };
    this.levelize = function(func) {
        var max = 0;
        var added = false;
        var inplay = new lightweight_list();
        for (var rangeNode = firstL; rangeNode; rangeNode = rangeNode.nextL) {
            if (func && !func(rangeNode.value)) continue;
            added = false;
            rangeNode.level = 0;
            for (var node = inplay.first; node; node = node.next) {
                if (node.value.x2 <= rangeNode.x1) {
                    inplay.remove(node)
                }
            }
            for (var node = inplay.first; node; node = node.next) {
                if (rangeNode.level < node.value.level) {
                    inplay.insertAfter(node.prev, rangeNode);
                    added = true;
                    break
                }
                rangeNode.level++;
                max = Math.max(max, rangeNode.level)
            }
            if (!added) inplay.insertLast(rangeNode)
        };
        return max
    };
    this.viewport = (function() {
        function get() {
            return {
                viewL: viewL,
                viewR: viewR
            }
        };

        function set(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            viewL = null;
            viewR = null;
            for (var node = firstL; node; node = node.nextL) {
                if (node.x2 < x1) continue;
                if (node.x1 > x2) break;
                viewL = node;
                viewR = node;
                while (node) {
                    if (node.x1 > x2) break;
                    if (node.x2 < x1) {
                        node = node.nextL;
                        continue;
                    }
                    viewR = node;
                    node = node.nextL
                }
                break
            }
        };

        function update(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            if (!viewL || !viewR) {
                set(x1, x2);
                return
            }
            while (viewL && viewL.x2 < x1) {
                viewL = viewL.nextR
            }
            while (viewR && viewR.x1 > x2) {
                viewR = viewR.prevL
            }
            while (viewL && viewL.prevR && viewL.prevR.x2 >= x1) {
                viewL = viewL.prevR
            }
            while (viewL && viewL.prevL && viewL.prevL.x2 >= x1) {
                viewL = viewL.prevL
            }
            while (viewR && viewR.nextL && viewR.nextL.x1 <= x2) {
                viewR = viewR.nextL
            }
        };

        function clear() {
            viewL = null;
            viewR = null
        };

        function apply(func) {
            if (!(func instanceof Function)) return false;
            if (!viewL || !viewR) return;
            for (var node = viewL; node; node = node.nextL) {
                func(node);
                if (node == viewR) break
            }
            return true
        };
        return {
            get: get,
            set: set,
            clear: clear,
            update: update,
            apply: apply
        }
    })()
};
var HistogramData = function() {
    this.series = {};
    this.clear = function() {
        for (var s in this.series) {
            this.series[s].clear()
        }
    };
    this.prune = function(x1, x2) {
        for (var s in this.series) {
            this.series[s].prune(x1, x2)
        }
    };
    this.parse = function(data, above) {
        if (!data) return;
        for (var name in data) {
            if (this.series[name] == undefined) {
                this.series[name] = new HistogramList()
            }
            this.series[name].parse(data[name], above)
        }
    };
    this.getCount = function(data) {
        if (!data) return;
        var count = 0;
        for (var name in data) {
            if (this.series[name]) count += this.series[name].getCnt();
        }
        return count
    };
    this.subset2canvas = function(left, right, bases, pixels) {
        var result = {};
        for (var s in this.series) {
            result[s] = this.series[s].subset2canvas(left, right, bases, pixels)
        }
        return result
    }
};
var HistogramList = function() {
    HistogramList.superclass.constructor.call(this);
    var self = this;
    var count = 0;
    this.parse = function(data, above) {
        var points = [];
        Ext.each(data, function(datum) {
            if (!datum) return;
            if (datum.length == 3) {
                var item = {
                    x: parseInt(datum[0]),
                    w: AnnoJ.pixels2bases(1),
                    y: parseFloat(datum[above ? 1 : 2]) || 0
                }
            } else {
                var item = {
                    x: parseInt(datum[0]),
                    w: parseInt(datum[1]) || 0,
                    y: parseFloat(datum[above ? 2 : 3]) || 0
                }
            }
            item.id = item.x;
            if (!item.w || !item.y) return;
            points.push(self.createNode(item.id, item.x, item))
        });
        self.insertPoints(points);
        points = null;
        count = self.getCount();
    };
    this.getCnt = function() {
        return count
    }
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var active = null;
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(item) {
            var x = Math.round((item.x - x1) * pixels / bases);
            var y = item.y;
            var w = Math.round(item.w * pixels / bases) || 1;
            if (active == null) {
                active = {
                    x: x,
                    y: y,
                    w: w
                }
            } else if (x == active.x) {
                active.y = Math.max(active.y, y);
                active.w = Math.max(active.w, w)
            } else {
                subset.push(active);
                active = {
                    x: x,
                    y: y,
                    w: w
                }
            }
        });
        if (active) subset.push(active);
        return subset
    }
};
Ext.extend(HistogramList, PointList);
var ReadsList = function() {
    ReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson' : 'crick'], function(datum) {
                if (datum.length != 6) return;
                var read = {
                    cls: name,
                    strand: above ? '+' : '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    places: parseInt(datum[3]) || 0,
                    copies: parseInt(datum[4]) || 0,
                    sequence: datum[5] || ''
                };
                read.sequence += "," + read.copies;
                if (read.id && read.x && read.w && read.places && read.copies) {
                    if (read.places > 1) read.cls += ' multi_mapper';
                    if (read.copies > 1) read.cls += ' multi_copies';
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            subset.push({
                x: Math.round((node.x1 - x1) * pixels / bases),
                w: Math.round((node.value.w) * pixels / bases) || 1,
                cls: node.value.cls,
                sequence: node.value.sequence
            });
            return true
        });
        return subset
    }
};
Ext.extend(ReadsList, RangeList);
var SmallReadsList = function() {
    SmallReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson' : 'crick'], function(datum) {
                if (datum.length != 6) return;
                var read = {
                    cls: name,
                    strand: above ? '+' : '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    places: parseInt(datum[3]) || 0,
                    copies: parseInt(datum[4]) || 0,
                    sequence: datum[5] || ''
                };
                read.sequence += "," + read.copies;
                if (read.id && read.x && read.w && read.places && read.copies) {
                    if (read.places > 1) read.cls += ' multi_mapper';
                    if (read.copies > 1) read.cls += ' multi_copies';
                    switch (read.w) {
                        case 20:
                            read.cls += ' sm21mers';
                            break;
                        case 21:
                            read.cls += ' sm22mers';
                            break;
                        case 22:
                            read.cls += ' sm23mers';
                            break;
                        case 23:
                            read.cls += ' sm24mers';
                            break;
                        default:
                            read.cls += ' Others';
                            break
                    }
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    }
};
Ext.extend(SmallReadsList, ReadsList);
var ModelsList = function(m) {
    ModelsList.superclass.constructor.call(this);
    var self = this;
    var boxWidthMin = 0;
    if (m) boxWidthMin = parseInt(m);
    this.parse = function(data, above) {
        if (!data || !(data instanceof Array)) return;
        Ext.each(data, function(datum) {
            var strand = datum[2];
            if (above && strand != '+') return;
            if (!above && strand != '-') return;
            var item = {
                parent: datum[0],
                id: datum[1],
                strand: datum[2],
                cls: datum[3],
                x: parseInt(datum[4]),
                w: parseInt(datum[5])
            };
            if (!item.parent || item.parent.indexOf(':') == 0) {
                if (!self.exists(item.id)) {
                    var node = self.createNode(item.id, item.x, item.x + item.w, item);
                    self.insert(node)
                }
            } else {
                var parent = self.getValue(item.parent);
                if (parent) {
                    if (!parent.children) {
                        parent.children = {}
                    }
                    parent.children[item.id] = item
                }
            }
        })
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        AnnoJ.config.genome_x1 = x1;
        AnnoJ.config.genome_x2 = x2;
        AnnoJ.config.genome_ratio = bases / pixels;
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            var item = {
                id: node.id,
                cls: node.value.cls,
                x: Math.round((parseInt(node.value.x) - x1) * pixels / bases),
                w: Math.round(parseInt(node.value.w) * pixels / bases) || boxWidthMin || 1,
                children: []
            };
            if (node.value.parent != null && node.value.parent != '') item.label = node.value.parent.substring(1);
            if (node.value.children) {
                for (var id in node.value.children) {
                    var child = node.value.children[id];
                    var cw = Math.round(child.w * pixels / bases) || 0;
                    if (cw < boxWidthMin) cw = boxWidthMin;
                    if (cw) {
                        item.children.push({
                            id: child.id,
                            cls: child.cls,
                            x: Math.round((parseInt(child.x) - x1) * pixels / bases),
                            w: cw
                        })
                    }
                }
            }
            subset.push(item);
            return true
        });
        return subset
    }
};
Ext.extend(ModelsList, RangeList);
var PairedReadsList = function() {
    PairedReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var self = this;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson' : 'crick'], function(datum) {
                if (datum.length != 7) return;
                var read = {
                    cls: name,
                    strand: above ? '+' : '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    lenA: parseInt(datum[3]) || 0,
                    lenB: parseInt(datum[4]) || 0,
                    seqA: datum[5],
                    seqB: datum[6]
                };
                if (read.id && read.x && read.w) {
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        var self = this;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            subset.push({
                x: Math.round((node.x1 - x1) * pixels / bases),
                w: Math.round((node.value.w) * pixels / bases) || 1,
                e1: Math.round((node.value.lenA) * pixels / bases),
                e2: Math.round((node.value.lenB) * pixels / bases),
                cls: node.value.cls,
                seqA: node.value.seqA,
                seqB: node.value.seqB
            });
            return true
        });
        return subset
    };
};
Ext.extend(PairedReadsList, RangeList);
var BaseCanvas = function(userConfig) {
    var self = this;
    self.config = {};
    var defaultConfig = {};
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    var container = document.createElement('DIV');
    var canvas = document.createElement("CANVAS");
    if (!canvas.getContext) {
        G_vmlCanvasManager.initElement(canvas);
    }
    var brush = canvas.getContext('2d');
    var width = 0;
    var height = 0;
    container.style.position = 'relative';
    canvas.style.position = 'relative';
    this.getContainer = function() {
        return container
    };
    this.getCanvas = function() {
        return canvas
    };
    this.getBrush = function() {
        return brush
    };
    this.getWidth = function() {
        return width
    };
    this.getHeight = function() {
        return height
    };
    this.getRegion = function() {
        if (!container || !container.parentNode) return null;
        var pr = Ext.get(container.parentNode).getRegion();
        var cr = Ext.get(container).getRegion();
        var ir = cr.intersect(pr);
        if (!ir) return null;
        if (ir.top < 0) {
            var diff = Math.abs(ir.top);
            ir.top += diff;
            ir.bottom += diff;
            cr.top += diff;
            cr.bottom += diff
        }
        if (ir.left < 0) {
            var diff = Math.abs(ir.left);
            ir.left += diff;
            ir.right += diff;
            cr.left += diff;
            cr.right += diff
        }
        var region = {};
        region.x1 = ir.left - cr.left;
        region.y1 = ir.top - cr.top;
        region.x2 = region.x1 + ir.right - ir.left;
        region.y2 = region.y1 + ir.bottom - ir.top;
        return region
    };
    this.setContainer = function(dom) {
        if (!dom || !dom.appendChild) return;
        if (dom.style.position != 'absolute' && dom.style.position != 'relative') {
            dom.style.position = 'relative'
        }
        if (canvas.parentNode) {
            canvas.parentNode.removeChild(canvas)
        }
        dom.appendChild(canvas);
        container = dom;
        self.clear()
    };
    this.setSize = function(width, height) {
        container.style.width = parseInt(width) || container.offsetWidth;
        container.style.height = parseInt(height) || container.offsetHeight;
        self.refresh()
    };
    this.paint = function() {};
    this.refresh = function() {
        self.clear();
        self.paint()
    };
    this.clear = function() {
        container.removeChild(canvas);
        container.innerHTML = '';
        if (canvas.innerHTML == "" && Ext.isIE) {
            canvas = document.createElement("canvas");
            if (!canvas.getContext) G_vmlCanvasManager.initElement(canvas);
        }
        container.appendChild(canvas);
        width = container.offsetWidth;
        height = container.offsetHeight;
        canvas.width = width;
        if ((Ext.isIE8 || Ext.isIE7 || Ext.isOpera)) {
            var track_id = container.parentNode.parentNode.parentNode.id;
            var conf = findConf(track_id);
            canvas.height = conf.height / 2;
            if (conf.single) canvas.height *= 2;
            var track = AnnoJ.getGUI().Tracks.tracks.find('id', track_id);
            if (track && track.config.type == 'HiCTrack') canvas.height *= 2
        } else canvas.height = height;
        brush = canvas.getContext('2d');
        brush.clearRect(0, 0, width, height)
    };
    this.paintLine = function(color, x1, y1, x2, y2) {
        if ((x1 < 0 && x2 < 0) || (y1 < 0 && y2 < 0)) return;
        if ((x1 >= width && x2 >= width) || (y1 >= height && y2 >= height)) return;
        brush.beginPath();
        brush.moveTo(x1, y1);
        brush.lineTo(x2, y2);
        brush.strokeStyle = color;
        brush.lineWidth = 1;
        brush.closePath();
        brush.stroke();
    }
    this.paintBox = function(cls, x, y, w, h) {
        if (!check(x, y, w, h)) return;
        var s = self.styles.get(cls);
        if (!s) return;
        var oldTrans = brush.globalAlpha;
        brush.globalAlpha = s.opacity;
        if (s.border.top.width > 0) {
            fillBox(s.border.top.color, x, y, w, s.border.top.width);
            y += s.border.top.width;
            h -= s.border.top.width
        }
        if (s.border.bottom.width > 0) {
            fillBox(s.border.bottom.color, x, y + h - s.border.bottom.width, w, s.border.bottom.width);
            h -= s.border.bottom.width
        }
        if (s.border.left.width > 0) {
            fillBox(s.border.left.color, x, y, s.border.left.width, h);
            x += s.border.left.width;
            w -= s.border.left.width
        }
        if (s.border.right.width > 0) {
            fillBox(s.border.right.color, x + w - s.border.right.width, y, s.border.right.width, h);
            w -= s.border.right.width
        }
        if (s.padding.top) {
            x += s.padding.top;
            h -= s.padding.top
        }
        if (s.padding.bottom) {
            h -= s.padding.bottom
        }
        if (s.padding.left) {
            y += s.padding.left;
            w -= s.padding.left
        }
        if (s.padding.right) {
            w -= s.padding.right
        }
        if (s.fill) {
            fillBox(s.fill, x, y, w, h)
        }
        if (s.image) {
            fillImage(s.image, s.background.repeat, x, y, w, h)
        }
        brush.globalAlpha = oldTrans
    };

    function fillBox(fill, x, y, w, h) {
        var box = check(x, y, w, h);
        if (!box) return;
        if (!isColor(fill)) return;
        brush.fillStyle = fill;
        brush.fillRect(box.x, box.y, box.w, box.h)
    };

    function fillImage(img, repeat, x, y, w, h) {
        if (!img) return;
        if (!img.complete) {
            Ext.EventManager.addListener(img, 'load', function() {
                fillImage(img, repeat, x, y, w, h)
            });
            return
        }
        var box = check(x, y, w, h);
        if (!box) return;
        var imgW = img.width;
        var imgH = img.height;
        if (repeat == 'repeat-x') {
            var numx = Math.floor(box.w / imgW);
            var diffx = box.w - (numx * imgW);
            for (var i = 0; i < numx; i++) {
                brush.drawImage(img, box.x + (i * imgW), box.y, imgW, box.h)
            }
            if (diffx > 0 && imgH > 0) brush.drawImage(img, 0, 0, diffx, imgH, box.x + (i * imgW), box.y, diffx, box.h)
        } else if (repeat == 'repeat-y') {
            var numy = Math.floor(box.h / imgH);
            var diffy = box.h - (numh * imgH);
            for (var i = 0; i < numy; i++) {
                brush.drawImage(img, box.x, box.y + (i * imgH), box.w, imgH)
            }
            if (imgW > 0 && diffy > 0) brush.drawImage(img, 0, 0, imgW, diffy, box.x, box.y + (i * imgH), box.w, diffy)
        } else {
            brush.drawImage(img, box.x, box.y, box.w, box.h)
        }
    };

    function check(x, y, w, h) {
        var x1 = x;
        var y1 = y;
        var x2 = x + w;
        var y2 = y + h;
        if (x1 < 0) x1 = 0;
        if (y1 < 0) y1 = 0;
        if (x2 >= width) x2 = width - 1;
        if (y2 >= height) y2 = height - 1;
        if (x1 >= x2 || x2 <= 0 || x1 >= width) return null;
        if (y1 >= y2 || y2 <= 0 || y1 >= height) return null;
        return {
            x: x1,
            y: y1,
            w: x2 - x1,
            h: y2 - y1
        }
    };
    this.styles = (function() {
        var styles = {};
        var imgCache = {};

        function set(cls, override) {
            if (!cls || typeof(cls) != 'string') return;
            if (styles[cls] && !override) return;
            styles[cls] = build(cls)
        };

        function get(cls) {
            if (!cls || typeof(cls) != 'string') return null;
            if (!styles[cls]) set(cls);
            return styles[cls] || null
        };

        function remove(cls) {
            if (!cls || typeof(cls) != 'string') return;
            if (styles[cls]) delete styles[cls]
        };

        function clear() {
            delete styles;
            styles = {}
        };

        function build(cls) {
            if (!cls || typeof(cls) != 'string') return;
            if (!container || !container.appendChild) container = document.body;
            var div = Ext.get(document.createElement('DIV'));
            div.addClass(cls);
            div.appendTo(container);
            var css = {
                opacity: div.getStyle('opacity'),
                image: null,
                fill: div.getColor('background-color') || 'white',
                line: div.getColor('color') || 'black',
                w: div.getWidth(),
                h: div.getHeight(),
                x: div.getTop(),
                y: div.getLeft(),
                background: {
                    image: div.getStyle('background-image'),
                    color: div.getColor('background-color'),
                    repeat: div.getStyle('background-repeat'),
                    position: div.getStyle('background-position')
                },
                margin: {
                    top: div.getMargins('t'),
                    bottom: div.getMargins('b'),
                    left: div.getMargins('l'),
                    right: div.getMargins('r')
                },
                border: {
                    top: {
                        width: div.getBorderWidth('t'),
                        color: div.getColor('border-top-color')
                    },
                    bottom: {
                        width: div.getBorderWidth('b'),
                        color: div.getColor('border-bottom-color')
                    },
                    left: {
                        width: div.getBorderWidth('l'),
                        color: div.getColor('border-left-color')
                    },
                    right: {
                        width: div.getBorderWidth('r'),
                        color: div.getColor('border-right-color')
                    }
                },
                padding: {
                    top: div.getPadding('t'),
                    bottom: div.getPadding('b'),
                    left: div.getPadding('l'),
                    right: div.getPadding('r')
                }
            };
            if (css.background.image.substr(0, 4) == 'url(') {
                var start = css.background.image.indexOf("http");
                var end = css.background.image.indexOf(".gif");
                var src = css.background.image.substr(start, end - start + 4);
                var img = new Image();
                img.src = src;
                css.image = img
            } else {
                css.image = null
            }
            div.remove();
            return css
        };
        return {
            set: set,
            get: get,
            remove: remove,
            clear: clear,
            build: build
        }
    })()
};
Ext.extend(BaseCanvas, Ext.util.Observable);
var DataCanvas = function(userConfig) {
    var self = this;
    DataCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        scaler: 1.0,
        flippedX: false,
        flippedY: false
    };
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    this.setScaler = function(value, update) {
        self.config.scaler = parseFloat(value) || 0;
        if (self.config.scaler < 0) self.config.scaler = 0;
        if (update) self.paint()
    };
    this.getScaler = function() {
        var brush = this.getBrush();
        if (brush) {
            var id = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
            var trackConfig = find(AnnoJ.config.tracks, 'id', id);
            if (trackConfig) return trackConfig.scale
            else return self.config.scaler
        }
        return self.config.scaler
    };
    this.flipX = function(update) {
        self.config.flippedX = !self.config.flippedX;
        if (update) this.paint()
    };
    this.flipY = function(update) {
        self.config.flippedY = !self.config.flippedY;
        if (update) this.paint()
    };
    this.isFlippedX = function() {
        return self.config.flippedX
    };
    this.isFlippedY = function() {
        return self.config.flippedY
    };
    this.groups = (function() {
        var list = {};

        function exists(name) {
            return !(list[name] == undefined)
        };

        function add(name) {
            if (exists(name)) return;
            list[name] = true;
            self.styles.set(name)
        };

        function remove(name) {
            if (!exists(name)) return;
            delete list[name];
            self.styles.remove(name)
        };

        function clear() {
            list = {};
            self.styles.clear()
        };

        function getList() {
            return list
        };

        function toggle(name, state) {
            list[name] = state ? true : false
        };

        function active(name) {
            return list[name] ? true : false
        };
        return {
            exists: exists,
            add: add,
            remove: remove,
            clear: clear,
            getList: getList,
            toggle: toggle,
            active: active
        }
    })()
};
Ext.extend(DataCanvas, BaseCanvas);
var HistogramCanvas = function() {
    HistogramCanvas.superclass.constructor.call(this);
    var self = this;
    var data = {};
    var max = 0;
    this.setData = function(series) {
        max = 0;
        for (var name in series) {
            self.groups.add(name);
            Ext.each(series[name], function(datum) {
                max = Math.max(max, datum.y)
            })
        }
        data = series
    };
    this.getMax = function() {
        return max
    };
    this.normalize = function(max) {
        for (var name in data) {
            Ext.each(data[name], function(datum) {
                datum.y /= max
            })
        }
    };
    this.paint = function() {
        this.clear();
        var brush = this.getBrush();
        var width = this.getWidth();
        var height = this.getHeight();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        if (Ext.isIE8 || Ext.isIE7) brush.canvas.style.height = height;
        brush.fillStyle = 'rgb(25,25,0)';
        if (AnnoJ.config.settings.display == 0) {
            brush.fillRect(1, 0, 1, height);
            brush.fillRect(1, 0, 4, 1);
        }
        var msg, lane, id;
        id = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        brush.font = "7pt times";
        brush.fillStyle = 'rgb(25,25,0)';
        lane = brush.canvas.parentNode.className;
        var max = 0;
        for (var i in AnnoJ.config.maxlist) {
            if (AnnoJ.config.maxlist[i] && max < AnnoJ.config.maxlist[i])
                max = AnnoJ.config.maxlist[i];
        }
        if (AnnoJ.config.settings.scale == 0) max = AnnoJ.config.maxlist[id];
        else if (AnnoJ.config.settings.scale == 1) AnnoJ.config.max = max;
        else if (AnnoJ.config.settings.scale == 2) {
            var conf = findConf(id);
            max = AnnoJ.config.settings.yaxis / conf.scale;
        }
        if (max == null) msg = "";
        else if (id == 'trackyyyy-0') msg = 1;
        else msg = Math.round(max);
        if (AnnoJ.config.settings.display == 0) {
            if (lane.indexOf("AJ_above") != -1) brush.fillText(msg, 2, 10);
            if (lane.indexOf("AJ_below") != -1) {
                brush.fillText("0", 2, 10);
                brush.fillText(msg, 2, height - 2);
            }
        }
        var clist = new Array('#FF0000', '#00FF00', '#0000FF', '#DC143C', '#FF1493', '#800080', '#9932CC', '#483D8B', '#00008B', '#149614', '#1E90FF', '#5F9EA0', '#2F4F4F', '#00FF7F', '#006400', '#7CFC00', '#FFD700', '#8B0000');
        if (id == 'trackyyyy-0') {
            if (InfoRequest.corr.length == 0) return;
            var h = AnnoJ.config.infoTrack.height / 2;
            var w = Math.ceil(200 / InfoRequest.corr.length);
            for (var i = 0; i < InfoRequest.corr.length; i++) {
                var val = InfoRequest.corr[i];
                var label = val.toString();
                var pos = label.indexOf(".");
                if (pos >= 0) label = label.substr(0, pos + 3);
                if (lane.indexOf("AJ_above") != -1 && val > 0) {
                    brush.fillStyle = clist[0];
                    brush.fillRect(10 + i * w + 1, h * (1 - val), w - 2, h * val);
                    brush.fillStyle = clist[2];
                    brush.fillText(label, 10 + i * w, h * (1 - val) - 2);
                }
                if (lane.indexOf("AJ_below") != -1 && val < 0) {
                    brush.fillStyle = clist[2];
                    brush.fillRect(10 + i * w + 1, 0, w - 2, -h * val);
                    brush.fillStyle = clist[0];
                    brush.fillText(label, 10 + i * w, -h * val + 10);
                }
            }
            return
        };
        var c = AnnoJ.config.settings.displays.color;
        for (var cls in data) {
            if (!self.groups.active(cls)) continue;
            if (cls == 'coverage') {
                brush.strokeStyle = "rgb(0,33,33)";
            } else brush.fillStyle = self.styles.get(cls).fill;
            var track = findConf(id);
            if (id.substring(0, 4) == 'new-') {
                var nc = nc || 0;
                if (!track.color) track.color = {};
                if (brush.fillStyle == '#ffffff') track.color[cls] = clist[7 + (nc % 9)];
                else track.color[cls] = brush.fillStyle;
                track.color['rColor'] = 1;
                nc++;
            };
            if (!track.color) track.color = {};
            else if (typeof(track.color) == 'string')
                track.color = Ext.decode(track.color);
            if (track.color[cls]) {
                if (track.color[cls].indexOf('#') === 0)
                    brush.fillStyle = track.color[cls];
                else if (track.color[cls].indexOf('rgb') === 0)
                    brush.fillStyle = track.color[cls];
                else brush.fillStyle = '#' + track.color[cls];
                var cc = rColor(brush.fillStyle);
                if (track.color['rColor'] && track.color['rColor'] != '0')
                    cc = brush.fillStyle;
                if (lane.indexOf("AJ_below") != -1) brush.fillStyle = cc
            }
            var pw, qw;
            var px = -1000,
                py = -1000,
                qx = -1000,
                qy = -1000;
            Ext.each(data[cls], function(datum) {
                var w = datum.w || 1;
                var h = datum.y;
                h /= max;
                h = Math.round(h * height);
                h -= AnnoJ.config.settings.baseline;
                if (h < 0) h = 0;
                var x = flippedX ? width - datum.x - w : datum.x;
                var y = flippedY ? 0 : height - h - 1;
                var dw = 20 * AnnoJ.getLocation().pixels + 3;
                if (x + w < 0 || x > width) return;
                if (h >= height) {
                    y = 0;
                    h = height - 1
                }
                if (AnnoJ.config.settings.display == 0 || isInfo(id)) {
                    if (h <= 0) return;
                    if (cls == 'coverage') {
                        if (lane.indexOf("AJ_below") != -1) {
                            pw = Math.ceil(w / 2);
                            if (x - px > dw) {
                                px = x;
                                py = y + h;
                                drawQuadraticCurve(brush, px, py, x, y + h, pw);
                                px = x + pw;
                            } else {
                                drawQuadraticCurve(brush, px, py, x, y + h, pw);
                                px = x + pw;
                                py = y + h;
                            }
                        } else {
                            qw = Math.ceil(w / 2);
                            if (x - qx > dw) {
                                qx = x;
                                qy = y;
                                drawQuadraticCurve(brush, qx, qy, x, y, qw);
                                qx = x + qw;
                            } else {
                                drawQuadraticCurve(brush, qx, qy, x, y, qw);
                                qx = x + qw;
                                qy = y;
                            }
                        }
                    } else brush.fillRect(x, y, w, h);
                } else {
                    var h = datum.y / max * 255;
                    h -= AnnoJ.config.settings.baseline;
                    if (h < 0) h = 0;
                    if (h > 255) h = 255;
                    brush.fillStyle = rgbColor(c, h);
                    brush.fillRect(x, 0, w, height);
                }
            });
        }
        clist = null;
    };

    function drawQuadraticCurve(brush, x, y, xz, yz, iw) {
        brush.beginPath();
        if (x == xz) {
            x0 = xz - iw;
            y0 = y;
            x1 = xz;
            y1 = yz;
            x2 = xz + iw;
            y2 = yz;
            brush.moveTo(x0, y0);
        } else {
            x0 = x;
            y0 = y;
            x1 = (x + xz - iw) / 2;
            y1 = (y + yz) / 2;
            x2 = xz - iw;
            y2 = yz;
            if (x2 < x0) {
                x2 = x0;
                x1 = x0;
            }
            brush.moveTo(x0, y0);
            brush.quadraticCurveTo(x1, y1, x2, y2);
            x0 = x2;
            y0 = y2;
            x1 = xz;
            y1 = yz;
            x2 = xz + iw;
        }
        brush.quadraticCurveTo(x1, y1, x2, y2);
        brush.stroke();
    };
};
Ext.extend(HistogramCanvas, DataCanvas);
var HiCInteraction = function() {
    HiCInteraction.superclass.constructor.call(this);
    var self = this;
    var data = {};
    var cidx, sidx;
    var max = 0;
    var resolution = 10000;
    var map_type = false;
    this.setData = function(series) {
        max = 0;
        if (series['resolution'] && series['resolution'][2]) {
            resolution = series['resolution'][2];
            data = series
        } else if (series['pair']) {
            map_type = true;
            data = series['pair']
        } else {
            data = null
        };
        if (series['cidx']) cidx = series['cidx'];
        if (series['sidx']) sidx = series['sidx'];
    };
    this.getMax = function() {
        return max
    };
    this.normalize = function(max) {
        for (var name in data) {
            Ext.each(data[name], function(datum) {
                datum.y /= max
            })
        }
    };
    this.paint = function() {
        this.clear();
        if (!data) return;
        var brush = this.getBrush();
        var width = this.getWidth();
        var height = this.getHeight();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var id = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        var track = AnnoJ.getGUI().Tracks.tracks.find('id', id);
        var conf = track.config;
        var ratio = AnnoJ.pixels2bases(1);
        var h = conf.unity;
        var half = Math.round(AnnoJ.pixels2bases(width) / 2);
        var view = AnnoJ.getLocation();
        var pos = view.position - half;
        var startx = 0;
        var drawShape = function(x, y, h, at) {
            if (!at) at = [
                [0, -1],
                [-1, 0],
                [1, 0]
            ];
            brush.moveTo(x + at[0][0] * h, y + at[0][1] * h);
            for (var i = 1; i < at.length; i++) {
                brush.lineTo(x + at[i][0] * h, y + at[i][1] * h);
            }
        };
        if (pos < 0) {
            startx = Math.round(-pos / (2 * half) * width);
            pos = 0;
        }
        if (map_type) {
            var c = AnnoJ.config.settings.displays.color || 'red';
            var y0 = conf.position - AnnoJ.pixels2bases(height / 2);
            var yh = conf.position + AnnoJ.pixels2bases(height / 2);
            for (var j = 0; j < data.length; j++) {
                var x = parseInt(data[j][0]) || 0;
                var y = parseInt(data[j][1]) || 0;
                if (x < view.position - half || x > view.position + half) continue;
                if (!conf.dimension) {
                    if (y < y0 || y > yh) continue;
                    var xpos = (x - view.position + half) / AnnoJ.pixels2bases(1);
                    var ypos = (y - y0) / AnnoJ.pixels2bases(1);
                } else {
                    if (y < conf.position - half || y > conf.position + half) continue;
                    var w = y - x + conf.positionDiff;
                    var xpos = (x + w / 2 - view.position + half) / AnnoJ.pixels2bases(1);
                    var ypos = w / 2 / AnnoJ.pixels2bases(1);
                }
                var color = data[j][2];
                var shape = data[j][3] || 0;
                if (cidx && cidx[color] !== false)
                    brush.fillStyle = cidx[color];
                else if (typeof color == "string" && color.indexOf("rgb") != -1)
                    brush.fillStyle = color;
                else {
                    var f = parseFloat(data[j][2]) || 0;
                    var color = scaler * Math.abs(f) - AnnoJ.config.settings.baseline;
                    color = Math.round(255 * color / AnnoJ.config.settings.yaxis);
                    if (color < 0) color = 0;
                    if (color > 255) color = 255;
                    brush.fillStyle = rgbColor(c, color);
                }
                var s;
                if (sidx && shape !== false && sidx[shape]) s = sidx[shape];
                brush.beginPath();
                if (conf.flipY) drawShape(xpos, height - ypos, h, s);
                else drawShape(xpos, ypos, h, s);
                brush.fill()
            }
            return;
        }
        var w = Math.round(resolution / ratio);
        var idx = Math.floor(pos / resolution);
        var first_w = resolution - pos % resolution;
        first_w = Math.round(first_w / resolution * w);
        var posy = conf.position;
        var idy = idx;
        if (posy > 0) idy = Math.floor(posy / resolution);
        track.config.indexy = idy;
        var rboundry = Math.ceil((view.position + half) / resolution);
        if (conf.style) {
            for (var i in data) {
                if (i == 'resolution') continue;
                var y = parseInt(i) || 0;
                if (y < idy || (y - idy) * h > height) continue;
                for (var j in data[i]) {
                    var x = parseInt(j) || 0;
                    if (x < idx || x > rboundry) continue;
                    var val = data[i][j] || 0;
                    if (val <= 0) continue;
                    var color = Math.round(255 - scaler * val);
                    if (color < 0) color = 0;
                    brush.fillStyle = 'rgb(255,' + color + ',' + color + ')';
                    brush.fillRect(startx + first_w + (x - idx - 1) * w, height - (y - idy + 1) * h, w, h)
                }
            }
        } else {
            for (var i in data) {
                if (i == 'resolution') continue;
                var y = parseInt(i) || 0;
                if (y < 2 * idx - rboundry || y > 2 * rboundry - idx) continue;
                for (var j in data[i]) {
                    var x = parseInt(j) || 0;
                    if (x < 2 * idx - rboundry || x > 2 * rboundry - idx) continue;
                    var val = data[i][j] || 0;
                    if (val <= 0) continue;
                    var color = Math.round(255 - scaler * val);
                    if (color < 0) color = 0;
                    brush.fillStyle = 'rgb(255,' + color + ',' + color + ')';
                    var xx = Math.round(((x + y) / 2 - idx - 1) * w);
                    var yy = Math.round(Math.abs(y - x) * w / 2);
                    var half = Math.round(w / 2);
                    brush.beginPath();
                    brush.moveTo(startx + first_w + xx + half, height - yy - h);
                    brush.lineTo(startx + first_w + xx + half - h, height - yy);
                    brush.lineTo(startx + first_w + xx + half + h, height - yy);
                    brush.fill()
                }
            }
        }
    }
};
Ext.extend(HiCInteraction, DataCanvas);
var BoxesCanvas = function(userConfig) {
    var self = this;
    BoxesCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxSpace: 1
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.paint = function() {
        this.clear();
        var width = this.getWidth();
        var height = this.getHeight();
        var region = this.getRegion();
        var brush = this.getBrush();
        var series = this.series.getAll();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var h = self.config.boxHeight * this.getScaler();
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        for (var name in series) {
            var boxes = series[name];
            this.levelize(boxes);
            Ext.each(boxes, function(box) {
                if (!self.groups.active(box.cls)) return true;
                var w = box.w;
                var x = flippedX ? width - box.x - w : box.x;
                var y = box.level * (h + self.config.boxSpace);
                y = flippedY ? y : height - y - h;
                if (x + w < region.x1 || x > region.x2) return;
                if (y + h < region.y1 || y > region.y2) return;
                self.paintBox(box.cls, x, y, w, h)
            })
        }
    };
    this.levelize = function(boxes) {
        if (!boxes || !(boxes instanceof Array)) return;
        var inplay = new AnnoJ.Helpers.List();
        var max = 0;
        Ext.each(boxes, function(box) {
            self.groups.add(box.cls);
            if (!self.groups.active(box.cls)) return true;
            box.level = 0;
            if (box.x1 == undefined) box.x1 = box.x;
            if (box.x2 == undefined) box.x2 = box.x + box.w;
            var added = false;
            for (var node = inplay.first; node; node = node.next) {
                if (node.value.x2 <= box.x1) {
                    inplay.remove(node)
                }
            }
            for (var node = inplay.first; node; node = node.next) {
                if (box.level < node.value.level) {
                    inplay.insertAfter(node.prev, box);
                    added = true;
                    break
                }
                box.level++;
                max = Math.max(max, box.level)
            }
            if (!added) inplay.insertLast(box)
        });
        return max
    }
};
Ext.extend(BoxesCanvas, DataCanvas);
var MaskCanvas = function(userConfig) {
    var self = this;
    var data = [];
    MaskCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {};
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    this.setData = function(models) {
        if (!(models instanceof Array)) return;
        data = [];
        Ext.each(models, function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w
        });
        data = models
    };
    this.paint = function() {
        this.clear();
        if (!data || data.length == 0) return;
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var flippedX = this.isFlippedX();
        if (region == null) return;
        var y = 0;
        var h = height;
        Ext.each(data, function(model) {
            self.groups.add(model.cls);
            if (!self.groups.active(model.cls)) return;
            var w = model.w;
            var x = model.x;
            if (flippedX) x = width - x - w;
            if (x + w < region.x1 || x > region.x2) return;
            self.paintBox(model.cls, x, y, w, h)
        })
    }
};
Ext.extend(MaskCanvas, BoxesCanvas);
var AlignsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    AlignsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {};
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    this.setData = function(models) {
        if (!(models instanceof Array)) return;
        data = [];
        Ext.each(models, function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w
        });
        data = models
    };
    this.paint = function() {
        this.clear();
        if (!data || data.length == 0) return;
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var flippedX = this.isFlippedX();
        if (region == null) return;
        var x0 = 0;
        var y = 0;
        var xp = AnnoJ.bases2pixels(AnnoJ.config.alignX[self.config.alignControl]);
        var nl = 0;
        var nr = 0;
        var xl = 0;
        var xr = 0;
        var h = height;
        var ih = self.config.boxHeight * this.getScaler();
        Ext.each(data, function(model) {
            self.groups.add(model.cls);
            if (!self.groups.active(model.cls)) return;
            var w = model.w;
            var x = model.x;
            if (flippedX) x = width - x - w;
            if (x + w < region.x1 || x > region.x2) return;
            if (x + w >= x0) color = 'rgb(25,205,85)';
            else color = 'rgb(205,105,205)';
            x0 = x + w;
            y1 = y + ih;
            y2 = y + h - ih;
            if (self.config.reverse) {
                self.paintBox(model.cls, x + w, y1 - ih, 1, ih);
                self.paintLine(color, x + w, y1 + 1, x + xp, y2 - 1);
                self.paintBox(model.cls, x + xp, y2, 1, ih);
            } else {
                self.paintBox(model.cls, x + xp, y1 - ih, 1, ih);
                self.paintLine(color, x + xp, y1 + 1, x + w, y2 - 1);
                self.paintBox(model.cls, x + w, y2, 1, ih);
            }
            if (w < -2) {
                nl++;
                xl += w;
            }
            if (w > 2) {
                nr++;
                xr += w;
            }
        });
        if (nl > nr) AnnoJ.config.meanX[self.config.alignControl] = AnnoJ.pixels2bases(Math.round(xl / nl));
        else AnnoJ.config.meanX[self.config.alignControl] = AnnoJ.pixels2bases(Math.round(xr / nr));
    }
};
Ext.extend(AlignsCanvas, BoxesCanvas);
var ModelsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    ModelsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5,
        boxSpace: 1,
        alignControl: 0,
        labels: true,
        arrows: true,
        strand: '+'
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    self.addEvents({
        'modelSelected': true
    });
    if (self.config.strand == '+' && !self.config.flippedY) self.flipY();
    if (self.config.strand == '-' && self.config.flippedY) self.flipY();
    this.setBoxHeight = function(h) {
        var h = parseInt(h) || 0;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        self.config.boxHeight = h
    };
    this.setBoxSpace = function(s) {
        var s = parseInt(s) || 0;
        self.config.boxSpace = s < 0 ? 0 : s
    };
    this.setLabels = function(state) {
        self.config.labels = state ? true : false
    };
    this.setArrows = function(state) {
        self.config.arrows = state ? true : false
    };
    this.setStrand = function(s) {
        self.config.strand = (s == '+') ? '+' : '-'
    };
    this.setData = function(models) {
        if (!(models instanceof Array)) return;
        data = [];
        Ext.each(models, function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w;
            self.groups.add(model.cls)
        });
        data = models
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler() || 1.0;
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var h = scaler * self.config.boxHeight;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
        var html = '';
        brush.fillStyle = 'rgb(25,25,0)';
        var id = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        var lane = brush.canvas.parentNode.className;
        var name = findConf(id).name;
        var sequence = AnnoJ.config.sequence;
        var ratio = AnnoJ.config.genome_ratio;
        if (data && data.length != 0) {
            Ext.each(data, function(model) {
                if (!self.groups.active(model.cls)) return;
                if (model.level > maxLevel) return;
                var w = model.w;
                var x = model.x;
                var y = model.level * (h + self.config.boxSpace);
                var sx = AnnoJ.bases2pixels(AnnoJ.config.alignX[self.config.alignControl]);
                var px = x;
                var qx = x;
                if (flippedX) x = width - x - w;
                if (flippedY) y = height - y - h - self.config.boxSpace;
                if (x + w < region.x1 || x > region.x2) return;
                if (y + h < region.y1 || y > region.y2) return;
                if (self.config.alignControl > 0) px = x + sx;
                self.paintBox(model.cls, px, y, w, h);
                Ext.each(model.children, function(child) {
                    qx = child.x;
                    if (self.config.alignControl > 0) qx = child.x + sx;
                    self.paintBox(child.cls, qx, y, child.w, h)
                });
                if (h >= self.config.boxBlingLimit) {
                    if (self.config.arrows) {
                        if (self.config.strand == '+') {
                            self.paintBox('arrowRight', px + w, y, h, h)
                        } else {
                            self.paintBox('arrowLeft', px - h, y, h, h)
                        }
                    }
                    if (self.config.labels) {
                        var label = model.id;
                        if (model.label) label = model.label;
                        html += "<div class='label' style='";
                        html += "position:absolute;";
                        html += "top:" + y + "px;";
                        html += "height:" + h + "px;";
                        html += "font-size:" + h + "px;";
                        html += "cursor:pointer;z-index:1000;";
                        if (self.config.strand == '+') {
                            html += "right:" + (width - x - sx) + "px;"
                        } else {
                            html += "left:" + (x + w + sx) + "px;"
                        }
                        html += "'>" + label + "</div>"
                    }
                }
            });
        }
        if (lane.indexOf("AJ_above") != -1 && sequence && ratio <= 0.2) {
            var length = sequence.length;
            if (ratio < 0.1) brush.font = "16px Times New Roman";
            else if (ratio <= 0.125) brush.font = "14px Times New Roman";
            else brush.font = "12px Times New Roman";
            for (var i = 0; i < length; i++) {
                var letter = sequence.charAt(i + 1);
                var letterX = Math.round((i + AnnoJ.config.genome_x - AnnoJ.config.genome_x1 + 1) / ratio);
                if (letter == 'a' || letter == 'A') brush.fillStyle = 'rgb(70,225,70)';
                if (letter == 'c' || letter == 'C') brush.fillStyle = 'rgb(70,70,225)';
                if (letter == 'g' || letter == 'G') brush.fillStyle = 'rgb(70,70,70)';
                if (letter == 't' || letter == 'T') brush.fillStyle = 'rgb(225,70,70)';
                if (letterX > 0) brush.fillText(letter, letterX, 34);
            }
        }
        if (!data || data.length == 0) return;
        var id = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        var div = document.createElement('DIV');
        div.innerHTML = html;
        div.id = 'models_' + id;
        container.appendChild(div);
        var c = Ext.get(container);
        c.removeListener('mouseup', clickModel);
        c.addListener('mouseup', clickModel);
        c.removeListener('touchend', clickModel);
        c.addListener('touchend', clickModel);
    };

    function clickModel(event, srcEl, obj) {
        var el = Ext.get(srcEl);
        if (el.hasClass('label')) {
            self.fireEvent('modelSelected', el.dom.innerHTML)
        }
    };
    var superLevelize = this.levelize;
    this.levelize = function(data) {
        var container = this.getContainer();
        var h = self.config.boxHeight * this.getScaler();
        var temp = Ext.get(document.createElement('DIV'));
        temp.addClass('label');
        temp.setStyle('position', 'absolute');
        temp.setStyle('visibility', 'hidden');
        temp.setStyle('font-size', h);
        temp.update('0123456789');
        temp.appendTo(container);
        var letterW = temp.getWidth(true) / 10;
        temp.remove();
        Ext.each(data, function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w;
            if (h >= self.config.boxBlingLimit) {
                if (self.config.arrows) {
                    if (self.config.strand == '+') {
                        model.x2 += h
                    } else {
                        model.x1 -= h
                    }
                }
                if (self.config.labels) {
                    var w = Math.round(letterW * model.id.length);
                    if (self.config.strand == '+') {
                        model.x1 -= w
                    } else {
                        model.x2 += w
                    }
                }
            }
        });
        return superLevelize(data)
    }
};
Ext.extend(ModelsCanvas, BoxesCanvas);
var ReadsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    ReadsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 8,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5,
        boxSpace: 1
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.setData = function(reads) {
        if (!(reads instanceof Array)) return;
        Ext.each(reads, function(read) {
            self.groups.add(read.cls)
        });
        data = reads
    };
    this.toggleSpecial = function(targetCls, state) {
        var list = self.groups.getList();
        for (var cls in list) {
            if (cls.indexOf(targetCls) != -1) {
                self.groups.toggle(cls, state)
            }
        }
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var x = 0;
        var y = 0;
        var w = 0;
        var h = Math.round(self.config.boxHeight * scaler);
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
        var level = 0;
        Ext.each(data, function(read) {
            self.groups.add(read.cls);
            if (!self.groups.active(read.cls)) return;
            if (read.level > maxLevel) return;
            if (read.multi && !self.config.showMultis) return;
            var pos = read.sequence.indexOf(",");
            var sequence = read.sequence.substr(0, pos);
            var copies = Math.round(read.sequence.substr(pos + 1));
            if (read.level == 0) level = 0;
            w = read.w;
            x = flippedX ? width - read.x - read.w : read.x;
            y = (read.level + level) * (h + self.config.boxSpace);
            y = flippedY ? y : height - 1 - y - h;
            if (x + w < region.x1 || x > region.x2) return;
            if (y + h < region.y1 || y > region.y2) return;
            var lane = brush.canvas.parentNode.className;
            var letterW = AnnoJ.bases2pixels(1);
            for (var i = 0; i < copies; i++) {
                if (letterW < 5 || sequence == "") {
                    if (lane.indexOf("AJ_below") != -1)
                        self.paintBox(read.cls, x, y + i * (h + self.config.boxSpace), w, h);
                    else self.paintBox(read.cls, x, y - i * (h + self.config.boxSpace), w, h);
                }
                if (sequence) {
                    if (lane.indexOf("AJ_below") != -1) {
                        var seq = "";
                        var length = sequence.length;
                        for (var j = length - 1; j >= 0; j--) {
                            var letter = sequence.charAt(j);
                            seq += letter;
                        }
                        letterize(brush, seq, x, y + i * (h + self.config.boxSpace), w, h, container)
                    } else
                        letterize(brush, sequence, x, y - i * (h + self.config.boxSpace), w, h, container);
                }
            }
            level += copies - 1;
        })
    };

    function letterize(brush, sequence, x, y, w, h, container) {
        var clean = "";
        var length = sequence.length;
        var letterW = AnnoJ.bases2pixels(1);
        for (var i = 0; i < length; i++) {
            var letter = sequence.charAt(i);
            switch (letter) {
                case 'A':
                    break;
                case 'T':
                    break;
                case 'C':
                    break;
                case 'G':
                    break;
                case 'N':
                    break;
                case 'a':
                    letter = 'A';
                    break;
                case 't':
                    letter = 'T';
                    break;
                case 'c':
                    letter = 'C';
                    break;
                case 'g':
                    letter = 'G';
                    break;
                default:
                    letter = 'N'
            }
            clean += letter;
            var letterX = x + (i * letterW);
            if (letterW < 5) {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillRect(letterX, y, letterW, h)
            } else {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillText(letter, letterX, y + h)
            }
        }
    }
};
Ext.extend(ReadsCanvas, BoxesCanvas);
var PairedReadsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    PairedReadsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 8,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.setData = function(reads) {
        if (!(reads instanceof Array)) return;
        Ext.each(reads, function(read) {
            self.groups.add(read.cls)
        });
        data = reads
    };
    this.toggleSpecial = function(targetCls, state) {
        var list = self.groups.getList();
        for (var cls in list) {
            if (cls.indexOf(targetCls) != -1) {
                self.groups.toggle(cls, state)
            }
        }
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var x = 0;
        var y = 0;
        var w = 0;
        var e1 = 0;
        var e2 = 0;
        var h = Math.round(self.config.boxHeight * scaler);
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
        Ext.each(data, function(read) {
            self.groups.add(read.cls);
            if (!self.groups.active(read.cls)) return;
            if (read.level > maxLevel) return;
            if (read.multi && !self.config.showMultis) return;
            w = read.w;
            e1 = read.e1;
            e2 = read.e2;
            x = flippedX ? width - read.x - read.w : read.x;
            y = read.level * (h + self.config.boxSpace);
            y = flippedY ? y : height - 1 - y - h;
            if (x + w < region.x1 || x > region.x2) return;
            if (y + h < region.y1 || y > region.y2) return;
            self.paintBox(read.cls, x, y, e1, h);
            self.paintBox(read.cls + '_spacer', x + e1, y, w - (e1 + e2), h);
            self.paintBox(read.cls, x + w - e2, y, e2, h);
            if (read.seqA) {
                letterize(brush, read.seqA, x, y, e1, h, container);
                letterize(brush, read.seqB, x + w - e2, y, e2, h, container)
            }
        })
    };

    function letterize(brush, sequence, x, y, w, h, container) {
        var clean = "";
        var length = sequence.length;
        var letterW = AnnoJ.bases2pixels(1);
        var readLength = length * letterW;
        for (var i = 0; i < length; i++) {
            var letter = sequence.charAt(i);
            switch (letter) {
                case 'A':
                    break;
                case 'T':
                    break;
                case 'C':
                    break;
                case 'G':
                    break;
                case 'N':
                    break;
                case 'a':
                    letter = 'A';
                    break;
                case 't':
                    letter = 'T';
                    break;
                case 'c':
                    letter = 'C';
                    break;
                case 'g':
                    letter = 'G';
                    break;
                default:
                    letter = 'N'
            }
            clean += letter;
            var letterX = x + (i * letterW);
            if (letterW < 5 || h < self.config.boxBlingLimit) {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillRect(letterX, y, letterW, h)
            } else {
                self.paintBox(letter, letterX, y, letterW, h)
            }
        }
    }
};
Ext.extend(PairedReadsCanvas, BoxesCanvas);
var SNPsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    SNPsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 16,
        boxHeightMax: 40,
        boxHeightMin: 8,
        boxBlingLimit: 5,
        boxSpace: 1
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.setData = function(reads) {
        if (!(reads instanceof Array)) return;
        Ext.each(reads, function(read) {
            self.groups.add(read.cls)
        });
        data = reads
    };
    this.toggleSpecial = function(targetCls, state) {
        var list = self.groups.getList();
        for (var cls in list) {
            if (cls.indexOf(targetCls) != -1) {
                self.groups.toggle(cls, state)
            }
        }
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        if (container.style.display == 'none') return;
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var x = 0;
        var y = 0;
        var w = 0;
        var h = Math.round(self.config.boxHeight * scaler);
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var level = 0;
        Ext.each(data, function(read) {
            self.groups.add(read.cls);
            if (!self.groups.active(read.cls)) return;
            var pos = read.sequence.indexOf(",");
            var sequence = read.sequence.substr(0, pos);
            w = read.w;
            if (w < self.config.boxWidthMin) w = self.config.boxWidthMin;
            x = flippedX ? width - read.x - read.w : read.x;
            y = 0;
            y = flippedY ? y : height - 1 - y - h;
            if (x + w < region.x1 || x > region.x2) return;
            if (y + h < region.y1 || y > region.y2) return;
            var lane = brush.canvas.parentNode.className;
            var letterW = 6;
            if (read.cls == 'Ins' || read.cls == 'Trans')
                self.paintBox(read.cls, x - 3, y, 9, h);
            else self.paintBox(read.cls, x, y, w, h);
            if (sequence) {
                var x2 = x - 2;
                if (AnnoJ.bases2pixels(1) > 6) x2 = x;
                if (lane.indexOf("AJ_below") != -1) {
                    var seq = "";
                    var length = sequence.length;
                    for (var j = length - 1; j >= 0; j--) {
                        var letter = sequence.charAt(j);
                        seq += letter;
                    }
                    letterize(brush, seq, x2, y + h + 1, w, h, container)
                } else
                    letterize(brush, sequence, x2, y - h - 1, w, h, container);
            }
        })
    };

    function letterize(brush, sequence, x, y, w, h, container) {
        var clean = "";
        var length = sequence.length;
        var letterW = (AnnoJ.bases2pixels(1) > 6) ? AnnoJ.bases2pixels(1) : 6;
        for (var i = 0; i < length; i++) {
            var letter = sequence.charAt(i);
            var fillStyle = 'rgb(25,25,25)';
            switch (letter) {
                case 'A':
                    break;
                case 'T':
                    break;
                case 'C':
                    break;
                case 'G':
                    break;
                case 'N':
                    break;
                case 'a':
                    letter = 'A';
                    break;
                case 't':
                    letter = 'T';
                    break;
                case 'c':
                    letter = 'C';
                    break;
                case 'g':
                    letter = 'G';
                    break;
                case 'n':
                    letter = 'N';
                    break;
                default:
                    break;
            }
            clean += letter;
            var letterX = x + (i * letterW);
            if (letter == 'N' || letter == 'A' || letter == 'T' || letter == 'G' || letter == 'C')
                brush.fillStyle = self.styles.get(letter).fill;
            else brush.fillStyle = 'rgb(80,100,100)';
            brush.fillText(letter, letterX, y + h)
        }
    }
};
Ext.extend(SNPsCanvas, BoxesCanvas);
AnnoJ.BaseTrack = function(userConfig) {
    var self = this;
    var defaultConfig = {
        id: Ext.id(),
        name: 'Track',
        path: '',
        datasource: '',
        minHeight: 10,
        maxHeight: 4000,
        height: 40,
        resizable: true,
        showControls: false,
        cls: 'AJ_track',
        iconCls: 'silk_bricks',
        enableMenu: true
    };
    this.config = defaultConfig;
    Ext.apply(this.config, userConfig || {}, defaultConfig);
    this.addEvents({
        'generic': true,
        'close': true,
        'error': true,
        'resize': true
    });
    this.Frame = (function() {
        var d = document.createElement('DIV');
        d.id = self.config.id;
        var ext = Ext.get(d);
        ext.addClass(self.config.cls);
        ext.on('mousemove', function(event) {
            cursor.id = self.config.id;
            cursor.type = self.config.type;
            var offsetTop = ext.dom.offsetTop;
            var offsetY = (ext.dom.offsetParent) ? ext.dom.offsetParent.offsetTop : 0;
            var scrollTop = AnnoJ.getGUI().Tracks.tracks.body.dom.scrollTop;
            cursor.offsetTop = event.getPageY() - offsetY + scrollTop - offsetTop;
            cursor.offsetHeight = ext.dom.offsetHeight;
            return false
        });
        ext.on('dblclick', function(event) {
            event.stopEvent();
            if (self.config.id == 'trackxxxx-0' || self.config.id == 'trackyyyy-0' || self.config.id.indexOf('new-') != -1) return true;
            InfoRequest.ready = true;
            var loc = AnnoJ.getLocation();
            var old_track = AnnoJ.getGUI().TracksInfo.tracks.tracks[0];
            if (old_track) {
                old_track.close();
                old_track = null;
                AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = null;
            }
            AnnoJ.config.infoTrack.data = self.config.data;
            AnnoJ.config.infoTrack.name = self.config.name;
            AnnoJ.config.infoTrack.type = self.config.type;
            AnnoJ.config.infoTrack.path = 'Analyses';
            AnnoJ.config.infoTrack.action = '';
            AnnoJ.config.infoTrack.color = {};
            AnnoJ.config.infoTrack.scale = 1;
            AnnoJ.config.infoTrack.showControls = true;
            AnnoJ.config.infoTrack.id = 'trackxxxx-0';
            AnnoJ.config.infoTrack.height = 160;
            AnnoJ.config.infoTrack.source = self.config.type;
            AnnoJ.config.infoTrack.assembly = AnnoJ.config.location.assembly;
            try {
                var track = new AnnoJ[AnnoJ.config.infoTrack.type](AnnoJ.config.infoTrack);
            } catch (e) {
                WebApp.error(e);
                if (!Ext.isIE) console.log(e);
                return false
            };
            AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = track;
            AnnoJ.getGUI().TracksInfo.tracks.open(track);
            if (track) track.setLocation(loc);
            return false
        });
        ext.on('contextmenu', function(event) {
            event.stopEvent();
            if (self.config.enableMenu) {
                self.ContextMenu.ext.showAt([event.getPageX(), event.getPageY()])
            }
            return false
        });
        ext.setHeight(self.config.height);
        return {
            ext: ext
        }
    })();
    this.Overflow = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_overflow');
        return {
            ext: ext
        }
    })();
    this.Canvas = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_canvas');
        return {
            ext: ext
        }
    })();
    this.ContextMenu = (function() {
        var ext = new Ext.menu.Menu();

        function enable() {
            self.config.enableMenu = true
        };

        function disable() {
            self.config.enableMenu = false
        };

        function addItems(items) {
            Ext.each(items, function(item) {
                ext.add(item)
            })
        };
        return {
            ext: ext,
            enable: enable,
            disable: disable,
            addItems: addItems
        }
    })();
    this.Toolbar = (function() {
        var ext = new Ext.Element(document.createElement('DIV'));
        ext.addClass('AJ_toolbar');
        ext.appendTo(document.body);
        var visible = self.config.showControls;
        var toolbar = new Ext.Toolbar({
            height: 50,
            renderTo: ext
        });
        var closeButton = new Ext.Button({
            iconCls: 'silk_delete',
            tooltip: 'Remove the track',
            permanent: true,
            moverBegin: 0,
            heatmap_permanent: true,
            handler: function() {
                self.fireEvent('close', self)
            }
        });
        var trackConfig = find(AnnoJ.config.tracks, 'id', self.config.id);
        if (!trackConfig) trackConfig = AnnoJ.config.tracks[0];
        var selected = new Ext.form.Checkbox({
            checked: false
        });
        selected.on('check', function(me, checked) {
            if (checked) {
                if (!AnnoJ.config.trks) AnnoJ.config.trks = new Array();
                var exists = AnnoJ.config.trks.indexOf(self.config.id);
                if (exists < 0) AnnoJ.config.trks.push(self.config.id);
            } else {
                var exists = AnnoJ.config.trks.indexOf(self.config.id);
                if (exists >= 0) AnnoJ.config.trks.splice(exists, 1);
            }
        });
        if (self.config.type == 'HiCTrack') {
            var assembly = new Ext.form.ComboBox({
                typeAhead: true,
                triggerAction: 'all',
                width: 40,
                grow: true,
                growMin: 40,
                growMax: 160,
                forceSelection: true,
                mode: 'local',
                displayField: 'id'
            });
            assembly.on('select', function(e) {
                self.config.assembly = e.getValue()
            });

            function bindAssemblies(options, selected) {
                if (!options || options.length == 0) return;
                var temp = [];
                Ext.each(options, function(item) {
                    temp.push([item.id])
                });
                if (!selected) selected = temp[0];
                var store = new Ext.data.SimpleStore({
                    fields: ['id'],
                    data: temp
                });
                assembly.bindStore(store);
                assembly.setValue(selected);
            };
            bindAssemblies(AnnoJ.config.assemblies, self.config.assembly);
            var HicFlipY = new Ext.Button({
                iconCls: 'silk_shape_flipY',
                tooltip: '<nobr>Flip display in Y axis.',
                handler: function(btn, item) {
                    self.config.flipY = (!self.config.flipY) ? 1 : 0;
                    var Navigator = AnnoJ.getGUI().NavBar;
                    Navigator.Controls.refreshControls();
                    Navigator.fireEvent('browse', Navigator.getLocation())
                }
            });
            var HicFlipXY = new Ext.Button({
                iconCls: 'silk_shape_handles',
                tooltip: '<nobr>Set X axis to Y axis and vice versa.</nobr>',
                handler: function(btn, item) {
                    var location = AnnoJ.getLocation();
                    var yLocation = {
                        assembly: assembly.getValue(),
                        position: HicPosition.getValue(),
                        pixels: location.pixels,
                        bases: location.bases
                    };
                    self.config.assembly = location.assembly;
                    self.config.position = location.position;
                    assembly.setValue(location.assembly);
                    HicPosition.setValue(location.position);
                    AnnoJ.setLocation(yLocation);
                    self.config.positionDiff = location.position - parseInt(HicPosition.getValue());
                    var Navigator = AnnoJ.getGUI().NavBar;
                    Navigator.Controls.refreshControls();
                    Navigator.fireEvent('browse', Navigator.getLocation())
                }
            });
            var HicSynchronized = new Ext.form.Checkbox({
                checked: self.config.synchronized || false
            });
            HicSynchronized.on('check', function(me, checked) {
                if (checked) {
                    var c = AnnoJ.getLocation();
                    self.config.positionDiff = c.position - parseInt(HicPosition.getValue());
                    self.config.synchronized = true
                } else {
                    self.config.synchronized = false
                }
            });
            var displayMode = new Ext.CycleButton({
                showText: true,
                prependText: '',
                tooltip: 'Display shape.',
                items: [{
                    text: 'Triangle',
                    checked: !self.config.style
                }, {
                    text: 'Rectangle',
                    checked: self.config.style
                }],
                changeHandler: function(btn, item) {
                    self.config.style = item.text == "Rectangle"
                }
            });
            var HicDimension = new Ext.CycleButton({
                showText: true,
                prependText: '',
                tooltip: 'Display in 1D or 2D dimensions.',
                items: [{
                    text: '1D',
                    checked: self.config.dimension
                }, {
                    text: '2D',
                    checked: !self.config.dimension
                }],
                changeHandler: function(btn, item) {
                    self.config.dimension = item.text == "1D";
                    if (self.config.dimension) HicSynchronized.setValue(true)
                }
            });
            var HicHeight = new Ext.form.TextField({
                width: 36,
                value: self.config.unity,
                maskRe: /[0-9]/,
                regex: /^[0-9]+$/,
                selectOnFocus: true
            });
            HicHeight.on('specialKey', function(config, event) {
                var val = parseInt(HicHeight.getValue());
                if (val < 0) HicHeight.setValue(1);
                if (val > 100) HicHeight.setValue(100);
                self.config.unity = parseInt(HicHeight.getValue()) || 4
            });
            var HicPosition = new Ext.form.TextField({
                width: 60,
                value: self.config.position || 0,
                maskRe: /[0-9]/,
                regex: /^[0-9]+$/,
                selectOnFocus: true
            });
            HicPosition.on('specialKey', function(config, event) {
                var val = parseInt(HicPosition.getValue());
                if (val < 0) HicPosition.setValue(1);
                self.config.position = parseInt(HicPosition.getValue());
            });
            var ylocs = new Ext.Toolbar.TextItem('Y-location');
            var ysync = new Ext.Toolbar.TextItem('sync');
            var ysize = new Ext.Toolbar.TextItem('size');
        };
        var title = new Ext.Toolbar.TextItem(self.config.name);
        title.permanent = true;
        title.heatmap_permanent = true;
        title.addClass('AJ_track_title');
        var trackmenu;
        if (self.config.type != "ModelsTrack") {
            trackmenu = new Ext.menu.Menu({
                items: ['series']
            })
        }

        function addItem(item) {
            if (item == 'remove' || item == 'insert' || item == 'search') return;
            if (self.config.type == "ModelsTrack") return;
            if (trackmenu) {
                for (var i = 0; i < trackmenu.items.items.length; i++) {
                    if (trackmenu.items.items[i].text == item) return;
                }
            }
            trackmenu.add([{
                text: item,
                menu: new Ext.menu.ColorMenu({
                    id: item,
                    handler: function(cm, color) {
                        if (typeof color != 'string') return;
                        var conf = find(AnnoJ.config.tracks, 'id', self.config.id);
                        if (conf) conf.color[this.id] = color;
                        self.refreshCanvas();
                    }
                })
            }])
        };
        var walkRequest = function(dir) {
            if (dir == 'o') {
                self.config.markname = '';
                return;
            }
            var c = AnnoJ.getLocation();
            BaseJS.request({
                url: self.config.data,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'walk',
                    id: self.config.markname || '',
                    dir: dir,
                    assembly: c.assembly,
                    position: c.position,
                    table: self.config.name,
                },
                success: function(response) {
                    if (response.data == null) {
                        alert("Warning: No response.data. The walks.txt file does not exist.");
                    } else {
                        var loc = AnnoJ.getLocation();
                        if (response.data.assembly && response.data.start) {
                            loc.assembly = response.data.assembly;
                            loc.position = parseInt(response.data.start);
                            AnnoJ.setLocation(loc);
                            self.fireEvent('browse', loc);
                            self.config.markname = response.data.id;
                        } else alert("Warning: Empty response.data. The walks.txt might not exist.");
                    }
                },
                failure: function(message) {
                    AnnoJ.error(message)
                }
            })
        };
        var prevWalk = new Ext.Button({
            iconCls: 'silk_previous',
            tooltip: '<nobr>Walk to Previous mark</nobr>',
            handler: function() {
                var dir = 'R';
                walkRequest(dir);
            }
        });
        var setWalk = new Ext.Button({
            iconCls: 'silk_location',
            tooltip: '<nobr>Set mark from this screen</nobr>',
            handler: function() {
                var dir = 'o';
                walkRequest(dir);
            }
        });
        var nextWalk = new Ext.Button({
            iconCls: 'silk_next',
            tooltip: '<nobr>Walk to next mark</nobr>',
            handler: function() {
                var dir = 'F';
                walkRequest(dir);
            },
        });
        var val = new Ext.Toolbar.TextItem('scale ');
        val.id = 'AJ_val_' + self.config.id;
        var scale_box = new Ext.form.TextField({
            width: 30,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        scale_box.id = 'AJ_scale_box_' + self.config.id;
        scale_box.setValue(trackConfig.scale);

        function setScale(val, notMul) {
            var scaler = parseFloat(scale_box.getValue());
            if (notMul) scaler = 1;
            var value = parseFloat(val) || 1;
            var f = value * scaler;
            scale_box.setValue(f.toString());
            trackConfig.scale = value * scaler;
            self.l_rescale(trackConfig.scale)
        };
        scale_box.on('specialKey', function(config, event) {
            if (event.getKey() == event.ENTER) {
                var trackConfig = find(AnnoJ.config.tracks, 'id', self.config.id);
                if (!trackConfig) trackConfig = AnnoJ.config.tracks[0];
                var scaler = scale_box.getValue();
                if (scaler == "") scale_box.setValue(1);
                trackConfig.scale = scale_box.getValue();
                self.l_rescale(trackConfig.scale)
            }
        });
        if (self.config.type == "AlignsTrack") {
            var alignbutton = new Ext.Button({
                iconCls: 'silk_cursor_hand',
                tooltip: '<nobr>Click and then Go to re-align</nobr>',
                handler: function() {
                    if (AnnoJ.config.alignX[self.config.alignControl] == 0)
                        AnnoJ.config.alignX[self.config.alignControl] = AnnoJ.config.meanX[self.config.alignControl];
                    else AnnoJ.config.alignX[self.config.alignControl] = 0;
                    AnnoJ.getGUI().Tracks.tracks.refresh();
                }
            });
            alignbutton.id = 'AJ_alignbutton_' + self.config.id;
        };
        var filler = new Ext.Toolbar.Fill();
        filler.permanent = true;
        var toggleButton = new Ext.Button({
            iconCls: 'silk_application',
            tooltip: 'Toggles toolbar visibility',
            handler: toggle,
            permanent: true
        });
        var head_space = new Ext.Toolbar.Spacer();
        head_space.permanent = true;
        head_space.heatmap_permanent = true;
        var spacer = new Ext.Toolbar.Spacer();
        spacer.permanent = true;
        var colorbutton = new Ext.Toolbar.Button({
            text: 'Color',
            menu: trackmenu
        });
        var items;
        if (isInfo(self.config.id) || self.config.id == 'trackyyyy-0') {
            items = [title, val, ' ', scale_box, spacer];
        } else if (self.config.type == "ModelsTrack") {
            if (self.config.showWalks)
                items = [closeButton, title, filler, prevWalk, setWalk, nextWalk, ' ', val, spacer, scale_box, toggleButton, spacer];
            else items = [closeButton, title, filler, val, ' ', scale_box, toggleButton, spacer];
        } else if (self.config.id.indexOf('new-') != -1) {
            items = [head_space, closeButton, title, filler, val, ' ', scale_box, colorbutton, toggleButton, spacer];
        } else if (self.config.type == "HiCTrack") {
            items = [closeButton, title, ' &nbsp; &nbsp; ', filler, HicFlipY, HicFlipXY, ' ', ylocs, ' ', assembly, HicPosition, '&nbsp;', ysync, ' ', HicSynchronized, HicDimension, displayMode, ysize, ' ', HicHeight, ' &nbsp; &nbsp; ', val, ' ', scale_box, colorbutton, toggleButton, spacer];
        } else if (self.config.type == "AlignsTrack") {
            items = [closeButton, title, filler, val, ' ', scale_box, alignbutton, toggleButton, spacer];
        } else if (self.config.data.indexOf("tabit.ucsd.edu") < 0) {
            if (self.config.showWalks)
                items = [closeButton, ' ', selected, title, filler, prevWalk, setWalk, nextWalk, val, ' ', scale_box, colorbutton, toggleButton, spacer];
            else items = [closeButton, ' ', selected, title, filler, val, ' ', scale_box, colorbutton, toggleButton, spacer];
        } else {
            items = [closeButton, selected, title, filler, val, ' ', scale_box, colorbutton, toggleButton, spacer];
        }
        Ext.each(items, function(item) {
            toolbar.add(item);
        });
        toolbar.doLayout();
        toggle(self.config.showControls);

        function setTitle(text) {
            Ext.get(title.getEl()).update(text)
        };

        function getTitle(text) {
            return title.getEl().innerHTML
        };

        function toggle() {
            visible ? hide() : show()
        };

        function show() {
            visible = true;
            Ext.each(items, function(item) {
                if (item.show) item.show()
            })
        };

        function hide() {
            visible = false;
            Ext.each(items, function(item) {
                if (item.hide && !item.permanent && AnnoJ.config.settings.display == 0) item.hide();
                if (item.hide && !item.heatmap_permanent && AnnoJ.config.settings.display == 1) item.hide()
            })
        };

        function insert(index, item) {
            if (!item.show || !item.hide) return;
            var oldParent = ext.dom.parentNode;
            ext.appendTo(document.body);
            items.insert(index, item);
            toolbar.insert(index, item);
            toggle(visible);
            toolbar.doLayout();
            ext.appendTo(oldParent)
        };

        function isVisible() {
            return visible
        };
        return {
            ext: ext,
            toolbar: toolbar,
            addItem: addItem,
            setScale: setScale,
            setTitle: setTitle,
            getTitle: getTitle,
            toggle: toggle,
            show: show,
            hide: hide,
            insert: insert,
            isVisible: isVisible
        }
    })();
    this.appendTo = function(d) {
        self.Frame.ext.appendTo(d)
    };
    this.remove = function() {
        self.Frame.ext.remove()
    };
    this.insertBefore = function(d) {
        self.Frame.ext.insertBefore(d)
    };
    this.insertAfter = function(d) {
        self.Frame.ext.insertAfter(d)
    };
    this.mask = function(m) {
        self.Frame.ext.mask(m)
    };
    this.unmask = function() {
        self.Frame.ext.unmask()
    };
    this.isMasked = function() {
        return self.Frame.ext.isMasked()
    };
    this.getTitle = function() {
        return self.Toolbar.getTitle()
    };
    this.setTitle = function(title) {
        self.Toolbar.setTitle(title)
    };
    this.getWidth = function() {
        return self.Frame.ext.getWidth(w)
    };
    this.setWidth = function(w) {
        self.Frame.ext.setWidth(w)
    };
    this.getHeight = function() {
        return self.Frame.ext.getHeight()
    };
    this.setHeight = function(h) {
        self.Frame.ext.setHeight(h)
    };
    this.getMinHeight = function() {
        return self.config.minHeight
    };
    this.getMaxHeight = function() {
        return self.config.maxHeight
    };
    this.broadcast = function(type, data) {
        this.fireEvent('generic', type, data)
    };
    this.receive = function(type, data) {};
    this.Frame.ext.appendChild(this.Overflow.ext);
    this.Frame.ext.appendChild(this.Toolbar.ext);
    this.Overflow.ext.appendChild(this.Canvas.ext);
    this.ext = this.Frame.ext
};
Ext.extend(AnnoJ.BaseTrack, Ext.util.Observable);
AnnoJ.DataTrack = function(userConfig) {
    AnnoJ.DataTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        datasource: ''
    };
    Ext.applyIf(this.config, defaultConfig);
    this.addEvents({
        'describe': true
    });
    var infoButton = new Ext.Button({
        iconCls: 'silk_information',
        tooltip: 'Show information about the track',
        permanent: true,
        handler: function() {
            self.fireEvent('describe', self.Syndicator.getSyndication())
        }
    });
    this.Toolbar.insert(1, infoButton);
    this.Communicator = (function() {
        var busy = false;

        function isBusy() {
            return busy
        };

        function get(data, options) {
            var options = Ext.apply(options || {}, {
                method: 'GET'
            });
            return request(data, options)
        };

        function post(data, options) {
            var options = Ext.apply(options || {}, {
                method: 'POST'
            });
            return request(data, options)
        };

        function request(data, options) {
            if (busy) return false;
            self.setTitle('<span class="waiting">Communicating with server...</span>');
            var options = Ext.apply(options || {}, {}, {
                url: self.config.datasource,
                method: 'POST',
                data: data || null,
                success: function() {},
                failure: function() {}
            });
            options.success = function(response) {
                busy = false;
                self.setTitle(self.config.name);
                options.success(response)
            };
            options.failure = function(response) {
                busy = false;
                self.setTitle(self.config.name);
                options.failure(response)
            };
            BaseJS.request(options);
            return true
        };
        return {
            isBusy: isBusy,
            get: get,
            post: post,
            request: request
        }
    })();
    this.Syndicator = (function() {
        var syndication = null;
        var syndicated = false;
        var busy = false;

        function check() {
            return syndicated
        };

        function get() {
            return syndication
        };

        function syndicate(options) {
            if (busy) return;
            busy = true;
            self.setTitle('Syndicating...');
            self.mask("<span class='waiting'>Syndicating datasource...</span>");
            var options = Ext.applyIf(options || {}, {
                url: self.config.data,
                table: self.config.name,
                success: function() {},
                failure: function() {}
            });
            var tempS = options.success;
            var tempF = options.failure;
            options.success = function(response) {
                syndicated = true;
                syndication = response;
                busy = false;
                if (self.config.name == 'Track') {
                    self.config.name = syndication.service.title
                }
                self.setTitle(self.config.name);
                self.unmask();
                tempS(response)
            };
            options.failure = function(string) {
                syndication = {};
                syndicated = false;
                busy = false;
                self.setTitle('Error: syndication failed');
                self.unmask();
                tempF(string)
            };
            BaseJS.syndicate(options)
        };
        return {
            isSyndicated: check,
            getSyndication: get,
            syndicate: syndicate
        }
    })()
};
Ext.extend(AnnoJ.DataTrack, AnnoJ.BaseTrack);
AnnoJ.BrowserTrack = function(userConfig) {
    AnnoJ.BrowserTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        autoResize: false,
        autoScroll: true,
        minCache: 100,
        cache: 3 * screen.width,
        maxCache: 20 * screen.width,
        scaler: 1.0,
        dragMode: 'browse'
    };
    Ext.applyIf(self.config, defaultConfig);
    self.config.originalHeight = self.config.height;
    if (!isInfo(self.config.id)) {
        this.ContextMenu.addItems([self.config.name, '-', new Ext.menu.Item({
            text: 'Close track',
            iconCls: 'silk_delete',
            handler: function() {
                self.fireEvent('close', self)
            }
        }), new Ext.menu.Item({
            text: 'Track Information',
            iconCls: 'silk_information',
            handler: function() {
                self.fireEvent('describe', self.Syndicator.getSyndication())
            }
        }), new Ext.menu.Item({
            text: 'Toggle Toolbar',
            iconCls: 'silk_application',
            handler: function() {
                self.Toolbar.toggle()
            }
        }), '-', new Ext.menu.Item({
            text: 'Minimize',
            iconCls: 'silk_arrow_down',
            handler: function() {
                self.setHeight(self.config.minHeight);
                self.fireEvent('resize', self.config.minHeight)
            }
        }), new Ext.menu.Item({
            text: 'Maximize',
            iconCls: 'silk_arrow_up',
            handler: function() {
                self.setHeight(self.config.maxHeight);
                self.fireEvent('resize', self.config.maxHeight)
            }
        }), new Ext.menu.Item({
            text: 'Original Size',
            iconCls: 'silk_arrow_undo',
            handler: function() {
                self.setHeight(self.config.originalHeight);
                self.fireEvent('resize', self.config.originalHeight)
            }
        })])
    };
    var Scaler = (function() {
        var value = 0.5;

        function get() {
            return value
        };

        function set(v) {
            var v = parseFloat(v);
            if (v < 0) v = 0;
            if (v > 1) v = 1;
            value = v;
            self.rescale(v);
            return v
        };
        return {
            get: get,
            set: set
        }
    })();
    var DataManager = (function() {
        var defaultView = {
            assembly: '',
            position: 0,
            bases: 10,
            pixels: 1
        };
        var views = {
            loading: defaultView,
            requested: defaultView
        };
        var state = {
            busy: false,
            empty: true,
            ready: false,
            assembly: '',
            frameL: 0,
            frameR: 0
        };
        var policy = {
            frame: 10000,
            bases: 10,
            pixels: 1,
            index: 0
        };

        function getPolicy(view) {
            var p = self.getPolicy(view);
            if (!p) return null;
            if (p.bases == undefined) return null;
            if (p.pixels == undefined) return null;
            if (p.cache == undefined) return null;
            if (p.index == undefined) return null;
            p.bases = parseInt(p.bases) || 0;
            p.pixels = parseInt(p.pixels) || 0;
            p.cache = parseInt(p.cache) || 0;
            p.index = parseInt(p.index) || 0;
            if (p.pixels < 1 || p.bases < 1 || p.cache < 100 * p.bases / p.pixels) {
                return null
            }
            return p
        };

        function pos2frame(pos) {
            if (pos < 0) pos = 0;
            return Math.floor(Math.abs(pos) / policy.cache)
        };

        function frame2pos(frame) {
            return {
                left: Math.abs(frame) * policy.cache,
                right: (Math.abs(frame) + 1) * policy.cache - 1
            }
        };

        function getEdges() {
            var half = Math.round(AnnoJ.pixels2bases(self.Frame.ext.getWidth()) / 2);
            var view = AnnoJ.getLocation();
            var pos = view.position;
            if (isInfo(self.config.id)) {
                pos = InfoRequest.position;
                half = Math.round(InfoRequest.bases / InfoRequest.pixels * (self.Frame.ext.getWidth()) / 2);
            }
            return {
                g1: pos - half,
                g2: pos + half
            }
        };

        function convertX(x) {
            return Math.round(x * views.current.bases / views.current.pixels)
        };

        function convertG(g) {
            return Math.round(g * views.current.pixels / views.current.bases)
        };

        function clear() {
            state.empty = true;
            self.clearData()
        };

        function prune(frameLeft, frameRight) {
            if (state.empty) return;
            if (frameLeft > state.right || frameRight < state.left) {
                clear();
                return
            }
            if (frameLeft > state.left) {
                state.left = frameLeft
            }
            if (frameRight < state.right) {
                state.right = frameRight
            }
            self.pruneData(frame2pos(frameLeft).left, frame2pos(frameRight).right)
        };

        function parse(data, frame) {
            if (state.empty || frame < state.left) {
                state.left = frame
            }
            if (state.empty || frame > state.right) {
                state.right = frame
            }
            state.empty = false;
            if (!data) return;
            var pos = frame2pos(frame);
            self.parseData(data, pos.left, pos.right)
        };

        function getLocation() {
            return views.current
        };

        function setLocation(requested) {
            Ext.apply(views.requested || {}, requested || views.requested || {}, defaultView);
            if (isInfo(self.config.id) && !InfoRequest.ready) return;
            if (isInfo(self.config.id)) {
                views.requested.position = InfoRequest.position;
                views.requested.bases = InfoRequest.bases;
                views.requested.pixels = InfoRequest.pixels;
            }
            var hic_y, old_y;
            if (self.config.type == 'HiCTrack') {
                var track = AnnoJ.getGUI().Tracks.tracks.find('id', self.config.id);
                old_y = track.config.hic_y;
                hic_y = findConf(self.config.id).assembly;
                views.requested.assembly = AnnoJ.config.location.assembly;
            }
            if (self.config.type == 'ModelsTrack') {
                var loc = AnnoJ.getLocation();
                AnnoJ.config.markname = 'chr' + loc.assembly + '-' + loc.position;
            }
            var newPolicy = getPolicy(views.requested);
            if (!newPolicy) {
                self.clearCanvas();
                self.mask('Out of zooming level');
                return
            }
            self.unmask();
            if (views.requested.assembly != state.assembly || policy.index != newPolicy.index || (isInfo(self.config.id) && !state.ready) || (self.config.type == 'HiCTrack' && hic_y != old_y)) {
                clear();
                self.clearCanvas();
                policy = newPolicy
            }
            var bases = self.config.cache * policy.bases / policy.pixels;
            if (isInfo(self.config.id)) bases /= 3;
            if (self.config.type == 'HiCTrack') {
                var half = 0;
            }
            var frameL = pos2frame(views.requested.position - bases);
            var frameR = pos2frame(views.requested.position + bases);
            prune(frameL, frameR);
            if (state.empty) {
                loadFrame(frameL)
            } else if (frameL < state.left) {
                loadFrame(state.left - 1)
            } else if (frameR > state.right) {
                loadFrame(state.right + 1)
            }
            if (state.busy == false) {
                var edges = getEdges();
                self.paintCanvas(edges.g1, edges.g2, views.requested.bases, views.requested.pixels);
                state.ready = false;
                InfoRequest.ready = false
            }
            var ratio = AnnoJ.bases2pixels(1);
            if (self.config.type == "ModelsTrack" && ratio >= 5 && !isInfo(self.config.id)) {
                var left = Math.round(views.requested.position - screen.width / (ratio * 2));
                var right = Math.round(views.requested.position + screen.width / (ratio * 2));
                if ((!AnnoJ.config.genome_left || !AnnoJ.config.genome_right || AnnoJ.config.genome_left != left || AnnoJ.config.genome_right != right || views.requested.assembly != state.assembly)) {
                    AnnoJ.config.genome_left = left;
                    AnnoJ.config.genome_right = right;
                    self.config.showDNA = self.config.showDNA || (self.config.name.indexOf('Gene Models') != -1);
                    if (self.config.showDNA) loadGenome(left, right, self.config.name);
                }
            }
        };

        function loadGenome(gleft, gright, name) {
            var gurl;
            gurl = AnnoJ.config.genome;
            state.busy = true;
            self.setTitle('<span class="waiting">Updating genome...</span>');
            views.loading = views.requested;
            BaseJS.request({
                url: gurl,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'range',
                    table: 'genome',
                    assembly: views.loading.assembly,
                    left: gleft,
                    right: gright,
                    bases: policy.bases,
                    pixels: policy.pixels
                },
                success: function(response) {
                    views.loading = null;
                    state.busy = false;
                    if (response.data[0] == "GENOME") {
                        AnnoJ.config.sequence = response.data[2];
                        AnnoJ.config.genome_x = response.data[1];
                    }
                    self.setTitle(self.config.name);
                    var edges = getEdges();
                    self.paintCanvas(edges.g1, edges.g2, views.requested.bases, views.requested.pixels)
                },
                failure: function(message) {
                    AnnoJ.error('Failed to load genome ' + self.config.name + ' (' + message + ')');
                    views.loading = null;
                    state.busy = false;
                    self.setTitle(self.config.name);
                }
            })
        };

        function loadFrame(frame) {
            if (self.config.id.indexOf("new-") != -1) {
                if (policy.bases / policy.pixels < 5) return;
            }
            if (state.busy) return;
            state.busy = true;
            self.setTitle('<span class="waiting">Updating...</span>');
            views.loading = views.requested;
            var request_url = self.config.data;
            var title = self.config.name;
            var pos = frame2pos(frame);
            if (isInfo(self.config.id)) {
                request_url = AnnoJ.config.infoTrack.data;
                title = AnnoJ.config.infoTrack.name;
                var width = self.Frame.ext.getWidth() / 2;
                width = Math.ceil(width / 100 + 0.05) * 100;
                var ratio = width * InfoRequest.bases / InfoRequest.pixels;
                if (ratio < 1) ratio = 1;
                pos.left = InfoRequest.position - width * ratio;
                if (pos.left < 0) pos.left = 0;
                pos.right = InfoRequest.position + width * ratio;
                views.loading.assembly = AnnoJ.config.infoTrack.assembly
            }
            var sources = '';
            var actions = '';
            if (self.config.id == 'trackyyyy-0') {
                sources = AnnoJ.config.infoTrack.urls;
                actions = AnnoJ.config.infoTrack.action
            }
            var tables = '';
            if (self.config.id != 'trackyyyy-0' && self.config.id != 'trackxxxx-0') {
                var track = findConf(self.config.id);
                sources = self.config.urls;
                if (self.config.type == 'HiCTrack') {
                    if (self.config.synchronized) {
                        track.assembly = AnnoJ.config.location.assembly;
                        track.position = AnnoJ.config.location.position;
                    }
                    sources = AnnoJ.config.location.assembly + ',' + self.config.assembly + ':';
                    if (self.config.synchronized) sources += 'sync:' + self.config.positionDiff;
                    else sources += 'async:' + self.config.position;
                    sources += ':' + track.height;
                };
                actions = self.config.action;
                if (!sources) sources = '';
                if (!actions) actions = '';
                tables = self.config.name;
            }
            if (self.config.type == 'HiCTrack') {
                var whole = pos.right - pos.left + 1;
                pos.left -= whole;
                pos.right += whole;
                if (pos.left < 0) pos.left = 0
            }
            var cgi = request_url.indexOf('.cgi?');
            if (cgi >= 0) {
                tables = '';
                var p = request_url.substring(cgi + 5);
                var t = p.indexOf('table=');
                if (t >= 0) {
                    var tt = p.indexOf('&');
                    if (tt >= 0) tables = p.substring(t + 6, tt);
                    else tables = p.substring(t + 6);
                }
                request_url = request_url.substring(0, cgi + 4);
            }
            BaseJS.request({
                url: request_url,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'range',
                    assembly: views.loading.assembly,
                    left: pos.left,
                    right: pos.right,
                    bases: policy.bases,
                    pixels: policy.pixels,
                    action2: actions,
                    urls: sources,
                    tracktype: self.config.tracktype || self.config.type,
                    table: tables
                },
                success: function(response) {
                    if (views.loading && views.loading.assembly != state.assembly) {
                        state.assembly = views.loading.assembly;
                        clear()
                    }
                    if (self.config.id == 'trackyyyy-0') {
                        InfoRequest.corr = response.data;
                        state.empty = false;
                    } else if (self.config.type == 'HiCTrack') {
                        var track = AnnoJ.getGUI().Tracks.tracks.find('id', self.config.id);
                        track.config.hic_y = findConf(self.config.id).assembly;
                        parse(response.data, frame);
                        state.empty = false
                    } else parse(response.data, frame);
                    views.loading = null;
                    state.busy = false;
                    state.ready = true;
                    self.setTitle(title);
                    setLocation(views.requested)
                },
                failure: function(message) {
                    views.loading = null;
                    state.busy = false;
                    state.ready = true;
                    if (self.config.id.indexOf('Peakcall-') >= 0)
                        self.setTitle(self.config.name + "<font color=#FF0000>(in progress...)</font>")
                }
            })
        };
        return {
            getLocation: getLocation,
            setLocation: setLocation,
            getEdges: getEdges,
            convertX: convertX,
            convertG: convertG,
            clear: clear
        }
    })();
    this.close = function() {
        if (self.config.action2) {
            self.mask('Track Closed');
            DataManager.clear();
            self.remove()
        } else self.hide()
    };
    this.hide = function() {
        self.mask('Track Closed');
        DataManager.clear();
        self.Frame.ext.setHeight(0);
        self.Frame.ext.setVisibilityMode(Ext.Element.DISPLAY);
        self.Frame.ext.setVisible(false)
    };
    this.moveCanvas = function(x, y) {
        self.Canvas.ext.setLeft(x);
        self.Canvas.ext.setTop(y);
    };
    this.setHeight = function(h) {
        if (h < self.config.minHeight) h = self.config.minHeight;
        if (h > self.config.maxHeight) h = self.config.maxHeight;
        var trackConfig = find(AnnoJ.config.tracks, 'id', self.config.id);
        if (trackConfig) trackConfig.height = h;
        self.Frame.ext.setHeight(h);
        self.refreshCanvas()
    };
    this.setHeatmapHeight = function(h) {
        if (h < 10) h = 10;
        self.Frame.ext.setHeight(h);
        self.refreshCanvas()
    };
    this.getLocation = DataManager.getLocation;
    this.setLocation = DataManager.setLocation;
    this.getEdges = DataManager.getEdges;
    this.convertX = DataManager.convertX;
    this.convertG = DataManager.convertG;
    this.setScale = Scaler.set;
    this.getScale = Scaler.get;
    this.clearCanvas = function() {};
    this.paintCanvas = function(x1, x2, bases, pixels) {};
    this.refreshCanvas = function() {};
    this.clearData = function() {};
    this.pruneData = function(x1, x2) {};
    this.parseData = function(data, x1, x2) {};
    this.getPolicy = function(view) {
        return null
    };
    this.rescale = function(value) {};
    this.setScale(self.config.scaler)
};
Ext.extend(AnnoJ.BrowserTrack, AnnoJ.DataTrack);
AnnoJ.MaskTrack = function(userConfig) {
    AnnoJ.MaskTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        single: false,
        boxWidthMin: 0,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Models = (function() {
        var dataA = new ModelsList(self.config.boxWidthMin);
        var dataB = new ModelsList(self.config.boxWidthMin);

        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new MaskCanvas();
        var canvasB = new MaskCanvas();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint();
            var list = canvasA.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            list = canvasB.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Models;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function() {};
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MaskTrack, AnnoJ.BrowserTrack);
AnnoJ.ModelsTrack = function(userConfig) {
    AnnoJ.ModelsTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        single: false,
        searchBox: true,
        searchURL: self.config.data,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        showLabels: true,
        showArrows: true,
        showWalks: true,
        labelPos: 'left',
        alignControl: 0,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxWidthMin: 0,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    if (self.config.searchBox) {
        var ds = new Ext.data.Store({
            url: self.config.searchURL,
            baseParams: {
                action: 'lookup',
                table: self.config.name
            },
            reader: new Ext.data.JsonReader({
                root: 'rows',
                totalProperty: 'count',
                id: 'id'
            }, [{
                name: 'id',
                mapping: 'id'
            }, {
                name: 'assembly',
                mapping: 'assembly'
            }, {
                name: 'start',
                mapping: 'start'
            }, {
                name: 'end',
                mapping: 'end'
            }, {
                name: 'description',
                mapping: 'description'
            }])
        });
        var resultTpl = new Ext.XTemplate('<tpl for="."><div class="gi">', '<b>{id}: </b><span>{description}</span>', '</div></tpl>');
        var search = new Ext.form.ComboBox({
            store: ds,
            displayField: 'id',
            typeAhead: false,
            cls: 'promote',
            loadingText: 'Searching...',
            width: 150,
            pageSize: 8,
            hideTrigger: false,
            tpl: resultTpl,
            minChars: 3,
            minListWidth: 400,
            itemSelector: 'div.gi',
            emptyText: 'Search...',
            onSelect: function(record) {
                var loc = AnnoJ.getLocation();
                loc.assembly = record.data.assembly;
                loc.position = parseInt(record.data.start);
                AnnoJ.setLocation(loc);
                self.fireEvent('browse', loc);
                this.collapse()
            }
        });
        search.on('expand', function() {
            if (!self.Toolbar.isVisible()) {
                this.collapse()
            }
        });
        if (!isInfo(self.config.id)) {
            self.Toolbar.insert(4, new Ext.Toolbar.Spacer());
            self.Toolbar.insert(4, search);
        };
        if (self.config.showControls) self.Toolbar.show()
    }
    var Models = (function() {
        var dataA = new ModelsList(self.config.boxWidthMin);
        var dataB = new ModelsList(self.config.boxWidthMin);

        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ModelsCanvas({
            strand: '+',
            labels: self.config.showLabels,
            arrows: self.config.showArrows,
            scaler: self.config.slider * 2,
            alignControl: self.config.alignControl,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ModelsCanvas({
            strand: '-',
            labels: self.config.showLabels,
            arrows: self.config.showArrows,
            scaler: self.config.slider * 2,
            alignControl: self.config.alignControl,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);
        canvasA.on('modelSelected', lookupModel);
        canvasB.on('modelSelected', lookupModel);

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            if (subsetA.length > 0) {
                var i = Math.floor(subsetA.length / 2);
                AnnoJ.config.markname = subsetA[i].id;
                if (!self.config.markname)
                    self.config.markname = subsetA[i].id;
            } else if (subsetB.length > 0) {
                var i = Math.floor(subsetB.length / 2);
                AnnoJ.config.markname = subsetB[i].id;
                if (!self.config.markname)
                    self.config.markname = subsetB[i].id;
            }
            var id = 'models_' + self.config.id;
            while (1) {
                var o = document.getElementById(id);
                if (o == null) break;
                o.parentNode.removeChild(o);
            }
            canvasA.paint();
            canvasB.paint();
            var list = canvasA.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            list = canvasB.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Models;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };

    function lookupModel(id) {
        AnnoJ.getGUI().InfoBox.echo("<div class='waiting'>Loading...</div>");
        BaseJS.request({
            url: self.config.data,
            method: 'GET',
            requestJSON: false,
            data: {
                action: 'describe',
                id: id,
                table: self.config.name
            },
            success: function(response) {
                if (response.success) {
                    var d = response.data;
                    var html = "<table>";
                    html += "<tr><td width=60><b>Gene ID: </b></td><td align=left>" + d.id + "</td></tr>";
                    html += "<tr><td><b>Assembly: </b></td><td align=left>" + d.assembly + "</td></tr>";
                    html += "<tr><td><b>Position: </b></td><td -align=left>" + d.start + '..' + d.end + "</td></tr>";
                    html += "<tr><td colspan='2'><hr>" + d.description + "</td></tr>";
                    html += "</table>";
                    AnnoJ.getGUI().InfoBox.echo(html);
                    AnnoJ.config.markname = d.id;
                    self.config.markname = d.id;
                } else {
                    AnnoJ.getGUI().InfoBox.echo("Error: failed to retrieve gene information. Server says: " + response.message)
                }
            },
            failure: function(message) {
                AnnoJ.getGUI().InfoBox.echo("Error: failed to retrieve gene information from the server")
            }
        })
    }
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = parseFloat(f * 2) || 0;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.ModelsTrack, AnnoJ.BrowserTrack);
AnnoJ.MicroarrayTrack = function(userConfig) {
    var self = this;
    AnnoJ.MicroarrayTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        reverse: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler = Histogram;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = Math.pow(f * 2, 4);
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MicroarrayTrack, AnnoJ.BrowserTrack);
AnnoJ.MethTrack = function(userConfig) {
    var self = this;
    AnnoJ.MethTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        class: 'CG CHG CHH',
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var menuItems = new Array();
    var toolbarItems = new Array();
    var classItems = ['silk_bullet_orange', 'silk_bullet_blue', 'silk_bullet_pink', 'silk_bullet_green', 'silk_bullet_red', 'silk_bullet_black'];
    var classColorArr = [toColorArr('#ff9933'), toColorArr('#0066ff'), toColorArr('#cc66ff'), toColorArr('#00cc00'), toColorArr('#ff5050'), toColorArr('#000000')];
    var classItemIdx = [-1, -1, -1, -1, -1, -1];
    var classes = self.config.class.split(' ');
    var hideClasses = [];
    for (var i = 0; i < classes.length; i++) {
        if (classes[i][0] == '-') {
            classes[i] = classes[i].substring(1);
            hideClasses.push(classes[i]);
        }
    }
    classes = classes.filter(Boolean).unique();
    for (var i = 0; i < classes.length; i++) {
        var m = new Ext.menu.CheckItem({
            text: classes[i],
            handler: toggleMeth
        });
        var idx = i % classItems.length;
        if (self.config.color && self.config.color[classes[i]]) {
            idx = toColorSimIndex(toColorArr(self.config.color[classes[i]]), classColorArr);
            if (idx == -1) idx = i % classItems.length;
            else classItemIdx[i] = idx;
        }
        var t = new Ext.Toolbar.Button({
            text: classes[i],
            iconCls: classItems[idx],
            tooltip: 'Show/hide ' + classes[i] + ' methyletion',
            handler: toggleMeth
        });
        menuItems.push(m);
        toolbarItems.push(t);
        m.setChecked(true);
    }
    self.ContextMenu.addItems(menuItems);
    for (var i = 1; i <= toolbarItems.length; i++)
        self.Toolbar.insert(5, toolbarItems[toolbarItems.length - i]);

    function toggleMeth(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        var idx = classes.indexOf(item.text);
        var vdx = idx;
        if (classItemIdx[idx] != -1) vdx = classItemIdx[idx];
        toolbarItems[idx].setIconClass(show ? classItems[vdx % classItems.length] : 'silk_bullet_white');
        if (item.iconCls) menuItems[idx].setChecked(show);
        handler.canvasA.groups.toggle(item.text, show);
        handler.canvasB.groups.toggle(item.text, show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            subsetA = sortObj(subsetA, false);
            subsetB = sortObj(subsetB, false);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    for (var i = 0; i < hideClasses.length; i++) {
        var idx = classes.indexOf(hideClasses[i]);
        toolbarItems[idx].setIconClass('silk_bullet_white');
        menuItems[idx].setChecked(false);
        handler.canvasA.groups.toggle(hideClasses[i], false);
        handler.canvasB.groups.toggle(hideClasses[i], false);
    }
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler = Histogram;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = Math.pow(f * 2, 4);
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MethTrack, AnnoJ.BrowserTrack);
AnnoJ.ReadsTrack = function(userConfig) {
    var self = this;
    AnnoJ.ReadsTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        reverse: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false);
            if (Ext.isGecko) {
                var cnt = dataA.getCount(data);
                if (cnt <= 0) dataA.parse(data, true);
                cnt = dataB.getCount(data);
                if (cnt <= 0) dataB.parse(data, false)
            }
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new ReadsList();
        var dataB = new ReadsList();

        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    }, {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads : Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.ReadsTrack, AnnoJ.BrowserTrack);
AnnoJ.PairedEndTrack = function(userConfig) {
    var self = this;
    AnnoJ.PairedEndTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        reverse: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series);
            }
            dataA.parse(data, true);
            dataB.parse(data, false);
            if (Ext.isGecko) {
                var cnt = dataA.getCount(data);
                if (cnt <= 0) dataA.parse(data, true);
                cnt = dataB.getCount(data);
                if (cnt <= 0) dataB.parse(data, false)
            }
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new PairedReadsList();
        var dataB = new PairedReadsList();

        function parse(data) {
            for (var series in data) {
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new PairedReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new PairedReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    }, {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads : Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        if (handler.dataA.clear) {
            handler.dataA.clear();
            handler.dataB.clear()
        }
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.PairedEndTrack, AnnoJ.BrowserTrack);
AnnoJ.SmallReadsTrack = function(userConfig) {
    var self = this;
    AnnoJ.SmallReadsTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        reverse: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var menuMultis = new Ext.menu.CheckItem({
        text: 'Show Multi-Mappers',
        handler: toggleMultis
    });
    var menu21mers = new Ext.menu.CheckItem({
        text: '21mers',
        handler: toggleClass
    });
    var menu22mers = new Ext.menu.CheckItem({
        text: '22mers',
        handler: toggleClass
    });
    var menu23mers = new Ext.menu.CheckItem({
        text: '23mers',
        handler: toggleClass
    });
    var menu24mers = new Ext.menu.CheckItem({
        text: '24mers',
        handler: toggleClass
    });
    var menuOthers = new Ext.menu.CheckItem({
        text: 'Others',
        handler: toggleClass
    });
    menuMultis.setChecked(true);
    menu21mers.setChecked(true);
    menu22mers.setChecked(true);
    menu23mers.setChecked(true);
    menu24mers.setChecked(true);
    menuOthers.setChecked(true);
    self.ContextMenu.addItems([menuMultis, menu21mers, menu22mers, menu23mers, menu24mers, menuOthers]);
    var toolbarMultis = new Ext.Toolbar.Button({
        text: 'Multis',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide read that map to multiple locations',
        handler: toggleMultis
    });
    var toolbar21mers = new Ext.Toolbar.Button({
        text: '21mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 21mers',
        handler: toggleClass
    });
    var toolbar22mers = new Ext.Toolbar.Button({
        text: '22mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 22mers',
        handler: toggleClass
    });
    var toolbar23mers = new Ext.Toolbar.Button({
        text: '23mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 23mers',
        handler: toggleClass
    });
    var toolbar24mers = new Ext.Toolbar.Button({
        text: '24mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 24mers',
        handler: toggleClass
    });
    var toolbarOthers = new Ext.Toolbar.Button({
        text: 'Others',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide reads other than 21mers or 24mers',
        handler: toggleClass
    });
    self.Toolbar.insert(5, toolbarMultis);
    self.Toolbar.insert(5, toolbarOthers);
    self.Toolbar.insert(5, toolbar24mers);
    self.Toolbar.insert(5, toolbar23mers);
    self.Toolbar.insert(5, toolbar22mers);
    self.Toolbar.insert(5, toolbar21mers);

    function toggleMultis(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        toolbarMultis.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
        if (item.iconCls) menuMultis.setChecked(show);
        handler.canvasA.toggleSpecial('multi_mapper', show);
        handler.canvasB.toggleSpecial('multi_mapper', show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }

    function toggleClass(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        if (item.text == '21mers') {
            toolbar21mers.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
            if (item.iconCls) menu21mers.setChecked(show)
        } else if (item.text == '22mers') {
            toolbar22mers.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
            if (item.iconCls) menu22mers.setChecked(show)
        } else if (item.text == '23mers') {
            toolbar23mers.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
            if (item.iconCls) menu23mers.setChecked(show)
        } else if (item.text == '24mers') {
            toolbar24mers.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
            if (item.iconCls) menu24mers.setChecked(show)
        } else if (item.text == 'Others') {
            toolbarOthers.setIconClass(show ? 'silk_bullet_orange' : 'silk_bullet_white');
            if (item.iconCls) menuOthers.setChecked(show)
        } else {
            return
        }
        handler.canvasA.toggleSpecial(item.text, show);
        handler.canvasB.toggleSpecial(item.text, show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new SmallReadsList();
        var dataB = new SmallReadsList();

        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    }, {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads : Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.SmallReadsTrack, AnnoJ.BrowserTrack);
AnnoJ.HiCTrack = function(userConfig) {
    var self = this;
    AnnoJ.HiCTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: true,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        hic_y: '0',
        indexy: 0,
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    var subsetA = {};
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerA.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerA.setStyle('height', '100%');
    containerA.appendTo(self.Canvas.ext);
    var Intensity = (function() {
        var canvasA = new HiCInteraction();

        function paint(left, right, bases, pixels) {
            canvasA.setData(subsetA);
            canvasA.paint()
        };
        return {
            canvasA: canvasA,
            paint: paint
        }
    })();
    var handler = Intensity;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    }, {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler = Intensity;
        handler.canvasA.setContainer(containerA.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Intensity) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasA.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasA.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true)
    };
    this.clearData = function() {};
    this.pruneData = function(a, b) {};
    this.getData = function() {
        return subsetA
    };
    this.parseData = function(data) {
        subsetA = data
    }
};
Ext.extend(AnnoJ.HiCTrack, AnnoJ.BrowserTrack);
AnnoJ.IntensityTrack = function(userConfig) {
    var self = this;
    AnnoJ.IntensityTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        reverse: false,
        class: '',
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var labels = {};
    var hideClasses = new Array();
    var classes = new Array();
    var toolbarItems = new Array();
    var menuItems = new Array();
    var classItems = ['silk_bullet_orange', 'silk_bullet_blue', 'silk_bullet_pink', 'silk_bullet_green', 'silk_bullet_red', 'silk_bullet_black'];
    if (self.config.class != '') {
        classes = self.config.class.split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (classes[i][0] == '-') {
                classes[i] = classes[i].substring(1);
                hideClasses.push(classes[i]);
            }
        }
        classes = classes.filter(Boolean).unique();
        for (var i = 0; i < classes.length; i++) {
            var m = new Ext.menu.CheckItem({
                text: classes[i],
                handler: toggleIntensity
            });
            var t = new Ext.Toolbar.Button({
                text: classes[i],
                iconCls: classItems[i % classItems.length],
                tooltip: 'Show/hide ' + classes[i] + ' Histogram',
                handler: toggleIntensity
            });
            menuItems.push(m);
            toolbarItems.push(t);
            if (hideClasses.indexOf(m.text) != -1) {
                m.setChecked(false);
                t.setIconClass('silk_bullet_white');
            } else m.setChecked(true);
            labels[classes[i]] = true;
        }
        self.ContextMenu.addItems(['-', 'Series']);
        self.ContextMenu.addItems(menuItems);
        for (var i = toolbarItems.length - 1; i >= 0; i--)
            self.Toolbar.insert(5, toolbarItems[i]);
    }

    function toggleIntensity(item) {
        var show = true;
        if (item.iconCls) show = item.iconCls == 'silk_bullet_white';
        else show = !item.checked;
        var idx = classes.indexOf(item.text);
        if (item.iconCls) menuItems[idx].setChecked(show);
        toolbarItems[idx].setIconClass(show ? classItems[idx % classItems.length] : 'silk_bullet_white');
        handler.canvasA.groups.toggle(item.text, show);
        handler.canvasB.groups.toggle(item.text, show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();

        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false);
            if (Ext.isGecko) {
                var cnt = dataA.getCount(data);
                if (cnt <= 0) dataA.parse(data, true);
                cnt = dataB.getCount(data);
                if (cnt <= 0) dataB.parse(data, false)
            }
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            if (self.config.reverse) {
                canvasA.setData(subsetB);
                canvasB.setData(subsetA);
            } else {
                canvasA.setData(subsetA);
                canvasB.setData(subsetB);
            }
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    for (var i = 0; i < hideClasses.length; i++) {
        var idx = classes.indexOf(hideClasses[i]);
        toolbarItems[idx].setIconClass('silk_bullet_white');
        menuItems[idx].setChecked(false);
        handler.canvasA.groups.toggle(hideClasses[i], false);
        handler.canvasB.groups.toggle(hideClasses[i], false);
    }
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.IntensityTrack, AnnoJ.BrowserTrack);
AnnoJ.AlignsTrack = function(userConfig) {
    AnnoJ.AlignsTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        showLabels: false,
        showArrows: false,
        alignControl: 0,
        reverse: 0,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.apply(self.config, userConfig || {}, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Edges = (function() {
        var dataA = new ModelsList();
        var dataB = new ModelsList();

        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new AlignsCanvas({
            boxHeight: self.config.boxHeight,
            alignControl: self.config.alignControl,
            reverse: self.config.reverse
        });
        var canvasB = new AlignsCanvas({
            boxHeight: self.config.boxHeight,
            alignControl: self.config.alignControl,
            reverse: self.config.reverse
        });
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint();
            var list = canvasA.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            list = canvasB.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Edges;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = parseFloat(f * 2) || 0;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.AlignsTrack, AnnoJ.BrowserTrack);
AnnoJ.SNPsTrack = function(userConfig) {
    var self = this;
    AnnoJ.SNPsTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: true,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        height: 30,
        boxHeight: 16,
        boxHeightMax: 40,
        boxHeightMin: 8,
        boxWidthMin: 0,
        boxBlingLimit: 6,
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted grey 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Reads = (function() {
        var dataA = new ReadsList();
        var dataB = new ReadsList();

        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new SNPsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxWidthMin: self.config.boxWidthMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new SNPsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxWidthMin: self.config.boxWidthMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();

        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Reads;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    }, {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    }, {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }, {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }, {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    if (self.config.policy != undefined) policies = setPolicy(policies, self.config.policy);
    var labels = null;

    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.SNPsTrack, AnnoJ.BrowserTrack);
AnnoJ.ProxyTrack = function(userConfig) {
    this.config = userConfig;
    this.isProxy = true;
    if (!this.config.iconCls) this.config.iconCls = 'silk_bricks';
}