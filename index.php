<?php
$root = '';
$title = 'KIDH - Data Export';
session_start();
unset($_SESSION['month']);

require($root . 'header.php')
?>

<script src="js/moment.min.js" type="text/javascript"></script>

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
                    <span class="input-group-addon modal-addon">Date From:</span>
                    <input type="date" name="date_from" data-date="" class="form-control" data-date-format="DD MMMM YYYY"/>
                </div>

            </div>

            <div class="col-sm-4 text-center text-primary">

                <div class="input-group modal-input">
                    <span class="input-group-addon modal-addon">Date To:</span>
                    <input type="date" name="date_to" data-date="" class="form-control" data-date-format="DD MMMM YYYY"/>
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

<script type="text/javascript">
    $("input").on("change", function () {
        this.setAttribute(
                "data-date",
                moment(this.value, "YYYY-MM-DD")
                .format(this.getAttribute("data-date-format"))
                )
    }).trigger("change")
</script>

<?php
require($root . 'footer.php');
?>