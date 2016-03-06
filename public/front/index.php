<!doctype html>
<html ng-app="sportofittApp">
  <head>
    <meta charset="utf-8">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!-- build:css(.) styles/vendor.css -->
    <!-- bower:css -->
    <!-- endbower -->
    <!-- endbuild -->
    <!-- build:css(.tmp) styles/main.css -->
      <link rel="stylesheet"
            href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet"
            href="../bower_components/font-awesome/css/font-awesome.min.css">

      <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
      <!-- Themify Icons -->
      <link rel="stylesheet"
            href="../bower_components/themify-icons/themify-icons.css">
      <!-- Loading Bar -->
      <link rel="stylesheet"
            href="../bower_components/angular-loading-bar/build/loading-bar.min.css">
      <!-- Animate Css -->
      <link rel="stylesheet"
            href="../bower_components/animate.css/animate.min.css">

      <link rel="stylesheet"
            href="../bower_components/angular-toastr/dist/angular-toastr.css">

    <link rel="stylesheet" href="styles/style.css">
      <link rel="stylesheet" href="styles/user.style.css">

    <!-- endbuild -->

      <style>
          .center { text-align: center; }
          .logo { padding: 80px 0; }
          img { max-width: 100%; margin-bottom: 20px; }
          .map { padding: 70px 20px; background: url("version-selector/map.png"); margin-bottom: 60px; border-radius: 20px; -webkit-border-radius: 20px; }
          .item { transition: .4s ease; -webkit-transition: .4s ease; position: relative; top: 0; margin-bottom: 20px; }
          .item:hover { top: -5px; }
      </style>

  </head>
  <body onunload="" class="page-subpage page-register navigation-top-header" id="page-top">
    <!--[if lte IE 8]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Outer Wrapper-->
    <div id="outer-wrapper">
        <!-- Inner Wrapper -->
        <div id="inner-wrapper">
            <header class="header" ui-view="nav"></header>
            <main ui-view ></main>
            <footer class="footer" ui-view="footer"></footer>
        </div>
    </div>
    <!-- build:js(.) scripts/vendor.js -->
    <!-- bower:js -->
    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Google Apis -->
    <script src='//maps.googleapis.com/maps/api/js'></script>
    <!-- Fastclick -->
    <script src="../bower_components/fastclick/lib/fastclick.js"></script>

    <script src="../bower_components/lodash/lodash.min.js"></script>
    <!-- Angular -->
    <script src="../bower_components/angular/angular.min.js"></script>

    <script src="../bower_components/angular-cookies/angular-cookies.min.js"></script>
    <script src="../bower_components/satellizer/satellizer.min.js"></script>
    <script src="../bower_components/angular-animate/angular-animate.min.js"></script>
    <script src="../bower_components/angular-touch/angular-touch.min.js"></script>
    <script src="../bower_components/angular-sanitize/angular-sanitize.min.js"></script>
    <script
            src="../bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
    <!-- Angular storage -->
    <script src="../bower_components/ngstorage/ngStorage.min.js"></script>
    <!-- Angular Translate -->
    <script
            src="../bower_components/angular-translate/angular-translate.min.js"></script>
    <script
            src="../bower_components/angular-translate-loader-url/angular-translate-loader-url.min.js"></script>
    <script
            src="../bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.min.js"></script>
    <script
            src="../bower_components/angular-translate-storage-local/angular-translate-storage-local.min.js"></script>
    <script
            src="../bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.min.js"></script>
    <!-- oclazyload -->
    <script src="../bower_components/oclazyload/dist/ocLazyLoad.min.js"></script>
    <!-- breadcrumb -->
    <script
            src="../bower_components/angular-breadcrumb/dist/angular-breadcrumb.min.js"></script>
    <!-- UI Bootstrap -->
    <script
            src="../bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    <!-- Loading Bar -->
    <script
            src="../bower_components/angular-loading-bar/build/loading-bar.min.js"></script>
    <!-- Angular Scroll -->
    <script src="../bower_components/angular-scroll/angular-scroll.min.js"></script>
    <script src="../bower_components/angular-toastr/dist/angular-toastr.tpls.min.js"></script>

    <!-- endbower -->
    <!-- endbuild -->

        <!-- build:js({.tmp,app}) scripts/scripts.js -->
        <script src="scripts/app.js"></script>
        <script src="scripts/controllers/main.js"></script>

    <script src="scripts/controllers/auth.js"></script>

    <script src="scripts/controllers/register.js"></script>

    <script src="scripts/services/auth.js"></script>

    <script src="scripts/routes/config.routes.js"></script>
        <!-- endbuild -->
</body>
</html>
