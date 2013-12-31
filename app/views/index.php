<!DOCTYPE html>
<html lang="en" ng-app="main">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Git Pretty Stats</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="<?=asset('assets/css/app.css')?>">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
        <script src="<?=asset('assets/js/html5shiv.js')?>"></script>
    <![endif]-->
</head>
<body ng-class="{'list': isList}">

    <ng-view></ng-view>

    <div class="footer">
        <div class="container">
            <div class="row">
                <strong>Git Pretty Stats</strong> -
                <a href="http://www.codingswag.com/git-pretty-stats">homepage</a> -
                <a href="http://www.github.com/modess/git-pretty-stats">github repository</a>
            </div>
        </div>
    </div>

    <script type="text/javascript">base_url = '<?=URL::to('/')?>';</script>
    <script src="<?=asset('assets/js/app.js')?>"></script>
</body>
</html>
