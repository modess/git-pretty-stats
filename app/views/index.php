<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Git Pretty Stats</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,500,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="styles/vendor.css">
    <link rel="stylesheet" href="styles/main.css">
  </head>
  <body ng-app="gitPrettyStats">

    <div ui-view></div>

    <script type="text/javascript">var base_url = '<?php echo URL::to("/"); ?>';</script>
    <script src="scripts/vendor.js"></script>
    <script src="scripts/templates.js"></script>
    <script src="scripts/app.js"></script>
</body>
</html>
