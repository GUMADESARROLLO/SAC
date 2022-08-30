<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>PLATAFORMA | SAC </title>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    
    <script src="{{ asset('js/theme_gumadesk/config.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/overlayscrollbars/OverlayScrollbars.min.js') }}"></script>
    <link href="{{ asset('js/theme_gumadesk/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/glightbox/glightbox.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/plyr/plyr.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/dropzone/dropzone.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/leaflet/leaflet.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/leaflet.markercluster/MarkerCluster.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/leaflet.markercluster/MarkerCluster.Default.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/fullcalendar/main.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('js/theme_gumadesk/vendors/swiper/swiper-bundle.min.css') }}" rel="stylesheet" >
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('js/theme_gumadesk/vendors/overlayscrollbars/OverlayScrollbars.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('css/theme_gumadesk/css/theme-rtl.min.css') }}" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('css/theme_gumadesk/css/theme.min.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('css/theme_gumadesk/css/user-rtl.min.css') }}" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('css/theme_gumadesk/css/user.min.css') }}" rel="stylesheet" id="user-style-default">
    <link href="{{ asset('js/theme_gumadesk/vendors/choices/choices.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script>
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
        function prettyDate(time){
	var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		day_diff = Math.floor(diff / 86400);
			
	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return;
			
	return day_diff == 0 && (
			diff < 60 && "just now" ||
			diff < 120 && "1 minute ago" ||
			diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
			diff < 7200 && "1 hour ago" ||
			diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
		day_diff == 1 && "Yesterday" ||
		day_diff < 7 && day_diff + " days ago" ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
}

// If jQuery is included in the page, adds a jQuery plugin to handle it as well
if ( typeof jQuery != "undefined" )
	jQuery.fn.prettyDate = function(){
		return this.each(function(){
			var date = prettyDate(this.title);
			if ( date )
				jQuery(this).text( date );
		});
	};
        
    </script>
    <style>
        .dBorder {
            border: 1px solid #ccc !important;
        }
    </style>
</head>
<form id="logout-form" action="{{ route('logout') }}" method="post">
    @csrf
</form>
<body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <div id="app">
        @yield('content')
    </div>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->




    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->

    <script src="{{ asset('js/theme_gumadesk/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/glightbox/glightbox.min.js') }}"></script>    
    <script src="{{ asset('js/theme_gumadesk/flatpickr.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/echarts/echarts.min.js') }}"></script>    
    <script src="{{ asset('js/theme_gumadesk/world.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/plyr/plyr.polyfilled.min.js') }}"></script>    
    <script src="{{ asset('js/theme_gumadesk/vendors/countup/countUp.umd.js') }}"></script>    
    <script src="{{ asset('js/theme_gumadesk/vendors/chart/chart.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/leaflet.markercluster/leaflet.markercluster.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/fullcalendar/main.min.js') }}" ></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/lodash/lodash.min.js') }}"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/theme.js') }}"></script>
    <script src="{{ asset('js/theme_gumadesk/vendors/choices/choices.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/Numeral.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script>
    @yield('metodosjs')
    

    <script type="text/javascript">

        function isValue(value, def, is_return) {
            if ( $.type(value) == 'null'
                || $.type(value) == 'undefined'
                || $.trim(value) == ''
                || ($.type(value) == 'number' && !$.isNumeric(value))
                || ($.type(value) == 'array' && value.length == 0)
                || ($.type(value) == 'object' && $.isEmptyObject(value)) ) {
                return ($.type(def) != 'undefined') ? def : false;
            } else {
                return ($.type(is_return) == 'boolean' && is_return === true ? value : true);
            }
        }
    </script>

    
</body>

</html>