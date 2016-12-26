// 6Harmonics Qige
// Microsoft Bing Maps API v7
// 2016.12.23: + jQuery, add jQuery functions, add "#sidebar"

var _appVersion = 'NMS (Microsoft Bing Maps) v4.0.261216';
var _appLat = 40.0492, _appLng = 116.2902;
var _page = 'bing.html', _file = '', _type = 1;
var _bingMap = null, _mapConfig = null;

(function($) {
	$.url = {
		get: function(key) {
			var reg = new RegExp("(^|&)" + key + "=([^&]*)(&|$)");
			var r = window.location.search.substr(1).match(reg);
			if (r != null) return unescape(r[2]); return null;
		},
		goto: function(url) {
			$(window.location).attr('href', url);
		}
	};
}) (jQuery);

(function($) {
	$.app = {
		init: function() {
			$('#dev-status').removeClass('bad error primary warning').addClass('bad');
			$('#dev-name').val('-');
			$('#dev-peer').val('-');
			$('#dev-radio').val('-');
			$('#dev-link-rx').val('-');
			$('#dev-link-tx').val('-');
			$('#dev-uptime').val('-');

			$('#btn-user').click(function() {
				$('#user-profile').fadeToggle();
			});
			$('#btn-dev-find').click(function() {
				console.log('- search dev by kw');
				var kw = $('#dev-find-kw').val();
				if (kw) {
					$('#dev-find-list').show();
				} else {
					$('#dev-find-list').hide();
				}
			});
			$('#btn-dev-find-close').click(function() {
				$('#dev-find-list').hide();
				$('#dev-find-kw').val('');
			});
			$('#dev-profile-every5s').click(function() {
				console.log('- start update every 5 secods');
			});
		},
		sync: function(resp) { //console.dir(resp);
			if (typeof(resp) != 'undefined' && resp 
					&& typeof(resp.dev) != 'undefined'
						&& typeof(resp.data) != 'undefined') 
			{
				$('#dev').val(resp.dev.mac);
				$('#peer').val(resp.dev.peer);
				
				$('#ptotal').val(resp.data.pstat.total);
				$('#pstrong').val(resp.data.pstat.strong);
				$('#pnormal').val(resp.data.pstat.normal);
				$('#pweak').val(resp.data.pstat.weak);
				$('#pbad').val(resp.data.pstat.bad);
			}
		},
		error: function(msg) {
			$('#dev').val(msg);
			$('#peer').val(msg);
		}
	};
}) (jQuery);

// TODO: add Pushpin at every point (with icon), add Infobox when clicked.
(function($) {
	$.MicrosoftMap = {
		map: null,
		init: function(obj, center, dbg) {
			//console.log('$.MicrosoftMap.init()');
			return new Microsoft.Maps.Map(obj, {
				center: _mapConfig.center, zoom: _mapConfig.zoomLevel,
				credentials: dbg ? 'AtjQW16t92aMGheD0QGfaUij1m8XLFZyG0neqbXh5ZUsWmvc-BXLQ2LxoJ65BCrs'
					: 'Amo7B47SLOX00WnHbTBXc4HDnPDm4RP6TDtAe6PhLnMKGRSwTPxa4wPQW7oLuDx8', 
				showMapTypeSelector: false, showBreadcrumb: true, enableClickableLogo: false,
				enableSearchLogo: false, mapTypeId: Microsoft.Maps.MapTypeId.aerial
			});
		},
		sync: function(bMap, data) {
			//console.log('$.MicrosoftMap.sync()');
			//console.dir(data);
			if ($.isArray(data)) {
				//console.log('$.MicrosoftMap.icons(): update');
				this.map = bMap;
				bMap.entities.clear();
				
				var idx = 0;
				for(idx in data) {
					var obj = data[idx];
					var msg1 = idx + ' | ' + obj.signal + '/' + obj.noise + '/' + (obj.signal - obj.noise) + ' (unit: dBm)';
					var rx = obj.rx + ' Mbps (' + obj.rxmcs + ')';
					var tx = obj.tx + ' Mbps (' + obj.txmcs + ')';
					var ext = obj.ts + ' | ' + obj.speed + ' Km/h';
					var msg = msg1 + ' | ' + rx + ' | ' + tx + ' | ' + ext;
					var pos = this.pos(obj.lat, obj.lng);

					var pin = this.pushpin(pos, 'res/icon-' + obj.level + '.png');

					pin.idx = idx;
					pin.msg = msg;

					Microsoft.Maps.Events.addHandler(pin, 'click', this.showInfobox);
					bMap.entities.push(pin);
				}
			} else {
				//console.log('$.MicrosoftMap.icons(): default');
				var devInfobox = this.infobox(bMap.getCenter(), 'Designed by 6WiLink Qige', 
						'Address: Suit 3B-1102/1105, Z-Park, Haidian Dist., Beijing, China', true);
				devInfobox.setOptions({ showCloseButton: false });
				this.map.entities.push(devInfobox);
			}
		},
		showInfobox: function(e) {
			//console.log('-- add infobox after pin clicked');
			var obj = e.target;
			var infobox = new Microsoft.Maps.Infobox(obj.getLocation(), {
				title: 'No. | Signal/Noise/SNR | Rx | Tx | Timestamp | Speed', 
				description: obj.msg,
				visible: true, 
				width: 480, height: 90
			});
			//console.log('-- show infobox');
			_bingMap.entities.push(infobox);
		},
		pushpin: function(center, icon) {
			return (new Microsoft.Maps.Pushpin(center, { 
				icon: icon, width: 19, height: 25
			}));
		},
		setView: function(bMap, view) {
			bMap.setView(view);
		},
		pos: function(lat,lng) {
			return (new Microsoft.Maps.Location(lat, lng));
		}
	};
}) (jQuery);

// start
$(document).ready(function() {
	// init basic values
	console.log(_appVersion);
	_file = $.url.get('f');
	_type = $.url.get('t');
	
	if (! _file) _file = 'demo.log';
	if (! _type) _type = 1;
	$.app.init(_file, _type);
	
	// default settings
	_mapConfig = {
		center: $.MicrosoftMap.pos(_appLat,_appLng),
		zoomLevel: 16, points: null, msg: null
	};
  
  //console.log('add Microsoft.Maps first');
	// init Microsoft Bing Maps
	_bingMap = $.MicrosoftMap.init($('#map')[0], _mapConfig.center, debug = true); 

	// fetch data & add points (icon)
	//console.log('parse file into array: '+_file);
	$.get('data/data.php', { f: _file, t: _type }, function(resp) { //console.dir(resp);
		if (typeof(resp.dev) != 'undefined' && typeof(resp.data) != 'undefined') {
			$.app.sync(resp); //console.log('- ajax json data fetched & valid');
		}
		if (typeof(resp.map) != 'undefined' && typeof(resp.map.center) != 'undefined') {
			_mapConfig.zoomLevel = resp.map.zoom; //console.log('-- update map');
			if (_mapConfig.center) {
				delete(_mapConfig.center); //console.log('- release old center');
			}
			_mapConfig.center = $.MicrosoftMap.pos(resp.map.center.lat, resp.map.center.lng);
			_mapConfig.points = resp.data.points;
			_mapConfig.msg = resp.data.msg;
		} else {
			console.log('invalid data');
			$.app.error('File Format Invalid');	
		}
		
    // clear & add new icons
		$.MicrosoftMap.sync(_bingMap, _mapConfig.points); 
    // move & zoom
		$.MicrosoftMap.setView(_bingMap, { center: _mapConfig.center, zoom: _mapConfig.zoomLevel });
	},'json');
});
