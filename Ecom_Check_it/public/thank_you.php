<?php
    require_once("../resources/config.php");
    include(TEMPLATE_FRONT.DS."header.php");
?>

<?php
        // echo "<pre>";
        //   var_dump($_POST);
        // echo "</pre>";
        // exit;

        report();
        session_destroy();
?>

<!-- Page Content -->
<div class="container">
    <h1 class="text-center">THANK YOU</h1>
</div>
 <!-- /.container -->

<?php
    include(TEMPLATE_FRONT.DS."footer.php");
?>