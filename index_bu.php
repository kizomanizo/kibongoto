<?php
$root = '';
$title = 'KIDH - Data Export';
session_start();
unset($_SESSION['month']);

require($root . 'header.php')
?>


<form action="excel/export.php" method="post">

    <div class="container">
        <div class="row" style="padding-top:30px">
            <div class="col-sm-12 text-center">
                <span class="text-success" style="font-size:3em"><span class="fa fa-print"></span> KIDH Data Export<small class="text-info" style="font-weight:bold"></small>
                    <hr />
            </div>
        </div>

        <div class="row" style="margin-bottom:20px">

            <div class="col-sm-4 text-center text-primary">

                <div class="input-group modal-input">
                    <span class="input-group-addon modal-addon">Month:</span>
                    <select class="form-control" name="month">
                        <?php
                        for ($m = 1; $m < 13; $m++)
                            echo '<option>' . sprintf('%02d', $m) . '</option>';
                        ?>
                    </select>
                </div>

            </div>

            <div class="col-sm-4 text-center text-primary">

                <div class="input-group modal-input">
                    <span class="input-group-addon modal-addon">Year:</span>

                    <select class="form-control" name="year">
                        <?php
                        for ($y = 2015; $y < (date('Y') + 1); $y++)
                            echo '<option>' . $y . '</option>';
                        ?>
                    </select>

                </div>

            </div>

            <div class="col-sm-4 text-center text-primary">

                <div class="input-group modal-input">
                    <button class="btn btn-primary btn-block"> <span class="fa fa-check"></span> Export to Excel</button>
                </div>

            </div>

        </div>


</form>



<?php
if (isset($_POST['start_ip']))
    include('find.php');
?>






<div class="row text-info" style="padding-top:20px; font-size:1.2em">
    <div class="col-sm-12">


        <div id="progress_box" class="progressbox">
            <div id="progress_bar" class="progressbar back-primary"></div>
            <div id="status_txt" class="statustxt"></div>
        </div>

        <div id="output">








        </div>








    </div>
</div>


</div>

<?php
require($root . 'footer.php');
?>