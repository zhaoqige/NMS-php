// 6Harmonics Qige
// Microsoft Bing Maps API v7
// 2016.12.23: + jQuery, add jQuery functions, add "#sidebar"

var _appVersion = 'NMS (Microsoft Bing Maps) v4.0.281216';
var _appLat = 40.0492, _appLng = 116.2902;
var _bingMap = null, _mapConfig = null, _currentSensorSN = '', _currentSensorInfobox;

var _author = 'Designed by 6WiLink Qige', _address = 'Address: Suit 3B-1102/1105, Z-Park, Haidian Dist., Beijing, China';


// window.location.href
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
		},
		sync: function(center, msg) { 
      //console.dir('- set infobx desc: ' + obj.msg + ', while sn: ' + _currentSensorSN);
      if (center && msg) {
        _currentSensorInfobox = new Microsoft.Maps.Infobox(center, {
          title: 'Status | SN | Name | Noise | PM | Temp | Timestamp', 
          description: msg, 
          width: 450, height: 90,
          visible: true, showCloseButton: false
        });
        _bingMap.entities.push(_currentSensorInfobox);
			}
		},
		error: function(msg) {
      $('#sensor-status').removeClass('bad error primary warning').addClass('bad');
			$('#sensor-ts').addClass('error').val(msg);
		},
    update: function() {
      console.log('updated at: ' + new Date());
      $.get('data/_data.php', { k: 'sensor' }, function(resp) { //console.dir(resp);
          //console.log('- ajax json data fetched & valid');
          if (typeof(resp.map) != 'undefined' && typeof(resp.map.center) != 'undefined') {
            _mapConfig.zoomLevel = resp.map.zoom; //console.log('-- update map');
            if (_mapConfig.center) {
              delete(_mapConfig.center); //console.log('- release old center');
            }
            _mapConfig.center = $.MicrosoftMap.pos(resp.map.center.lat, resp.map.center.lng);
          }
          if (typeof(resp.points) != 'undefined') {
            _mapConfig.points = resp.points;
          } else {
            console.log('invalid data');
            $.app.error('File Format Invalid');	
          }
          
          // clear & add new icons
          $.MicrosoftMap.sync(_mapConfig.points); 
          // move & zoom
          if (_currentSensorSN == '') {
            $.MicrosoftMap.setView({ center: _mapConfig.center, zoom: _mapConfig.zoomLevel });
            _currentSensorSN = ' ';
          }
        },'json');      
    }
	};
}) (jQuery);

// Bing Maps
(function($) {
	$.MicrosoftMap = {
		map: null,
		init: function(obj, center, dbg) {
			//console.log('$.MicrosoftMap.init()');
			return new Microsoft.Maps.Map(obj, {
				center: _mapConfig.center, zoom: _mapConfig.zoomLevel,
				credentials: dbg ? 'AhnlfvF1xVTU6hqJs2ueQB7f46mv4JkkkdbqYQ3sPUkYu7CjonMpC8WVFVvG7mMX'
						: 'ApI17LQorAgXC64mQ85EC-ZJlcxUn0pthYc0klwLxi8EzFC0lhnrQEutHj8o3CEL', 
				showMapTypeSelector: false, showBreadcrumb: true, enableClickableLogo: false,
				enableSearchLogo: false, mapTypeId: Microsoft.Maps.MapTypeId.aerial
			});
		},
		sync: function(data) {
			//console.log('$.MicrosoftMap.sync()');
			//console.dir(data);
      delete _currentSensorInfobox;
      _bingMap.entities.clear();
			if ($.isArray(data)) {				
        //console.log('$.MicrosoftMap.icons(): update');
				var idx = 0;
				for(idx in data) {
					var obj = data[idx];
					var pos = this.pos(obj.pos.lat, obj.pos.lng);
					var pin = this.pushpin(pos, 'res/dust-' + obj.level + '.png');
          
          pin.sn = obj.sn;
          pin.msg = obj.status + ' | ' + obj.name + obj.sn + ' | ' + obj.noise + ' | ' + obj.pm + ' | ' + obj.temp + ' | ' + obj.ts;

					Microsoft.Maps.Events.addHandler(pin, 'click', this.showInfobox);
					_bingMap.entities.push(pin);
          
          if (_currentSensorSN && _currentSensorSN != ' ' && _currentSensorSN == obj.sn) {
            $.app.sync(pos, pin.msg);
          }
				}
			} else {
				console.log(_author + '; ' + _address);
				var devInfobox = this.infobox(_bingMap.getCenter(), _author, _address, true);
				devInfobox.setOptions({ showCloseButton: false });
				_bingMap.entities.push(devInfobox);
			}
		},
		showInfobox: function(e) {
      if (_currentSensorInfobox) {
        _currentSensorInfobox.setOptions({ visible: false });
        delete _currentSensorInfobox;
      };
      
			var obj = e.target;
      var pos = obj.getLocation();
      _currentSensorSN = obj.sn;
      $.app.sync(pos, obj.msg);
      
			console.log('-- add infobox after pin clicked');
      _bingMap.setView({ center: pos, zoom: 18 });
		},
    infobox: function(center, title, msg, visible) {
      return (new Microsoft.Maps.Infobox(center, {
        title: title,
        description: msg,
        visible: visible,
        width: 480, height: 90
      }));
    },
		pushpin: function(center, icon) {
			return (new Microsoft.Maps.Pushpin(center, { 
				icon: icon, width: 19, height: 25
			}));
		},
		setView: function(view) {
			_bingMap.setView(view);
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
  $.app.update();
  // update every 30 seconds, sensor api update every 60 seconds
	//setInterval("$.app.update()", 30000);
	setInterval("$.app.update()", 2000); // DEBUG USE ONLY
});
