<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<!--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">-->
<meta charset="UTF-8">
<title>PLATAFORMA | UNIMARKSA S,A</title>

<head>
<style>
    * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }
    .gray {
        
    }
    .text-center{
        align-items: center;
        justify-content: center;
        text-align: center;
    }
	.w3-border {
        border: 1px solid #ccc !important;
    }
    .ml {
        margin-top: 10px !important;
        margin-bottom : 10px !important;
    }
</style>
</head>
<body>
    @yield('content')
</body>
</html>