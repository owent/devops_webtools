<link class="include" rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.min.css" />

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="js/jqplot/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>

<script type="text/javascript" src="js/oss/oss-main.js"></script>
<script type="text/javascript" src="js/oss/oss-x-method.js"></script>
<script type="text/javascript">
    window.oss_config = window.oss_config || {};
    oss_config.api_url = "<?php echo $_SERVER['SCRIPT_NAME']; ?>";
    oss_config.env = "<?php echo empty($_REQUEST['oss_env'])? 'default': $_REQUEST['oss_env']; ?>";
</script>
