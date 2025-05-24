<!DOCTYPE html>
<html lang="en">
<head>
    <!--
                             __    __    _______  _______ 
       .---.-..--.--..-----.|  |  |__|  |       ||   |   |
       |  _  ||_   _||  -__||  |   __   |   -   ||       |
       |___._||__.__||_____||__|  |__|  |_______||__|_|__|
                             \\\_____ axels OBJECT MANAGER

    -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{PAGE_TITLE}}</title>

    <!--
    <script type="text/javascript" src="js/asimax.class.js"></script>
    
    visjs
    <script type="text/javascript" src="vendor/vis/4.21.0/vis.min.js"></script>
    <link href="vendor/vis/4.21.0/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css"/>
    
    -->

    <script type="text/javascript" src="../vendor/jquery/3.6.1/jquery.min.js"></script>

    <!-- datatable -->
    <script type="text/javascript" src="../vendor/datatables/1.10.15/js/jquery.dataTables.min.js"></script>
    <link href="../vendor/datatables/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    
    <!-- fontawesome -->
    <link href="../vendor/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css"/>

    <!-- bootstrap-select -->
    <link rel="stylesheet" href="../vendor/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
    <script src="../vendor/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

    <!-- Adminlte -->
    <link rel="stylesheet" href="{{DIR_ADMINLTE}}/css/adminlte.min.css">

    <link rel="stylesheet" href="{{DIR_ADMINLTEPLUGINS}}/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="{{DIR_ADMINLTEPLUGINS}}/dropzone/dropzone.css" type="text/css" />


    <link rel="stylesheet" href="main.css">

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary {{PAGE_SKIN}} {{PAGE_LAYOUT}}">
    <div class="app-wrapper">

        {{NAVI_TOP}}

        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        

            {{BRAND}}

            <div class="sidebar-wrapper">
                {{NAVI_LEFT}}
            </div>

        </aside>

        <main class="app-main">


            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        {{PAGE_HEADER_LEFT}}
                    </div>
                    <div class="page-hint">{{PAGE_BANNER}}</div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{PAGE_BODY}}
                </div>
            </div>

        </main>

        <footer class="app-footer">

            <div class="float-end d-none d-sm-inline">
                {{PAGE_FOOTER_RIGHT}}
            </div>

            {{PAGE_FOOTER_LEFT}}
        </footer>

        <div class="sidebar-overlay"></div>
    </div>

    <script>{{INJECT_JS}}</script>
    <script src="{{DIR_ADMINLTEPLUGINS}}/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{DIR_ADMINLTEPLUGINS}}/summernote/summernote-bs4.min.js"></script>
    <script src="{{DIR_ADMINLTEPLUGINS}}/dropzone/dropzone.js"></script>


    <script src="{{DIR_ADMINLTE}}/js/adminlte.min.js?v=3.2.0"></script>
    <script type="text/javascript" src="js/functions.js"></script>
    {{JS_BODY_END}}

    <div id="overlay">
        <div id="overlay-text"></div>
    </div>
</body>

</html>