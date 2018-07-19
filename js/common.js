$(function() { 
	init(); 

	$('body').on('loaded.bs.modal', function (e) {
		init();
	});
	
	$('body').on('hidden.bs.modal', '.modal', function () {
		//$('#modal-sm, #modal_md, #modal-lg').html(loadingBar);
		$(this).removeData('bs.modal');
	});
});

function init() {
	$('.tipsy').tooltip();
	$('#customer_id').filterByText($('#filter_customers'));  
	$('#supplier_id').filterByText($('#filter_suppliers'));  
	$('#product_id').filterByText($('#filter_products'));
	$('#final_product_id').filterByText($('#filter_final_products'));
	$('#person_id').filterByText($('#filter_persons'));
	saleProducts = []; purchaseProducts = []; documentProducts = [];
	
	$('.numeric').on('keyup', function() {
		if($('#'+this.id).val() != $('#'+this.id).val().replace(/[^0-9\.]/g, '')) {
			$('#'+this.id).val($('#'+this.id).val().replace(/[^0-9\.]/g, ''));
		}
	});

	$('.amount').on('keyup', function() {
		tempAmount = $('#'+this.id).val();
		if(tempAmount  != tempAmount.replace(/[^0-9\.]/g, '')) {
			tempAmount = tempAmount.replace(/[^0-9\.]/g, '');
		}
		$('#'+this.id).val((parseFloat(tempAmount)).formatMoney(0, '.', ','));
	});

	/*
	$('.datepicker').datetimepicker({
		immediateUpdates: true,
		format: 'DD-MMM-YYYY',
		autoclose: true,
		toDay: true
	});
	*/
	
	$('.datetimepicker').datetimepicker({
		immediateUpdates: true,
		format: 'DD-MMM-YYYY hh:mm A',
		autoclose: true,
		toDay: true
	});
	  
	var datacontainer = $("body");
	var progressbox = $("#progress_box");
	var progressbar = $("#progress_bar");
	var statustxt = $("#status_txt");
	var submitbutton = $("#submit_button");
	var myform 	= $("#ajax_form");
	var output 	= $("#output");
	var completed = "0%";
	
	$(myform).ajaxForm({
		beforeSend: function() { //before sending form
			submitbutton.attr("disabled", "disabled"); // disable upload button
			submitbutton.css("opacity", "0.5");
			submitbutton.button('complete');
			statustxt.empty();
			progressbox.show(); //show progressbar
			progressbar.width(completed); //initial value 0% of progressbar
			statustxt.html(completed); //set status text
			statustxt.css("color","#000"); //initial color of status text
		},
		
		uploadProgress: function(event, position, total, percentComplete) { //on progress
			progressbar.width(percentComplete + "%") //update progressbar percent complete
			statustxt.html(percentComplete + "%"); //update status text
			if(percentComplete>48) {
				statustxt.css("color","#fff"); //change status text to white after 50%
			}
		},
		
		complete: function(response) { // on complete
			output.html(response.responseText); //update element with received data
			output.fadeIn(1000);
			//myform.resetForm();  // reset form
			submitbutton.css("opacity", "1.0");
			submitbutton.removeAttr("disabled"); //enable submit button
			submitbutton.button('reset')
			progressbox.hide(); // hide progressbar
		}
	});
}

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

jQuery.fn.filterByText = function(textbox) {
	
	return this.each(function() {
        var select = this;
        var options = [];
        $(select).find('option').each(function() {
            options.push({value: $(this).val(), text: $(this).text()});
        });
        $(select).data('options', options);

        $(textbox).bind('change keyup', function() {
            var options = $(select).empty().data('options');
            var search = $.trim($(this).val());
            var regex = new RegExp(search,"gi");

            $.each(options, function(i) {
                var option = options[i];
                if(option.text.match(regex) !== null) {
                    $(select).append(
                        $('<option>').text(option.text).val(option.value)
                    );
                }
            });
        });
    });
}

var selectedItem = '';
var loadingBar = '<div class="modal-content">'+
	'<div class="modal-header">'+
	'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
	'<h4 class="modal-title" id="myModalLabel">Downloading...</h4>'+
	'</div>'+
	'<div class="modal-body">'+
	'<div style="height:30px"><div id="progress" class="progress">'+
	'<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>'+
	'</div></div>'+
	'</div>'+
	'<div class="modal-footer">'+
	'<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>'+
	'</div>';

function numbersOnly(id) {
	$('#'+id).val($('#'+id).val().replace(/[^0-9\.]/g, ''));
}

function loadList(url, p) {	
	url = typeof url !== 'undefined' ? url : 'list.php';
	p = typeof p !== 'undefined' ? p : 0;
	
	k = $('#keyword').val();
	sd = $('#start_date').val();
	ed = $('#end_date').val();

	$('#list_container').html(loadingBar);
	$('#list_container').load(encodeURI(url+'?k='+k+'&p='+p+'&sd='+sd+'&ed='+ed+'&t='+moment()));
}

function deleteItem(itemName, deleteLink, loadLink){
	selectedItem = itemName;
	$('#delete_body').html(itemName+' will be deleted.');
	$('#delete_modal').modal('show');
	$('#delete_button').click(function() { 
		r = $.ajax({type: 'GET', cache:false, async:false, url:deleteLink}).responseText;
		loadList('list.php', 1);
		$('#main_menu').load('../main_menu.php');
		$('#delete_modal').modal('hide');
		$('#modal_md').modal('hide');
		$('#delete_button').unbind();
	});
}

function isNumeric(sText){	
	var ValidChars = "0123456789.";
	var IsNumber=true;
	var Char;
	for (i = 0; i < sText.length && IsNumber == true; i++) { 
		Char = sText.charAt(i); 
		if (ValidChars.indexOf(Char) == -1) {
			IsNumber = false;
		}
	}
	return IsNumber;
}

function refreshList() {
	loadList('list.php', 1);
	setTimeout(function() { $('#modal_md').modal('hide'); }, 1000);
}

function checkAll(status, className) {
	if(status) {
		$('input[id*="'+className+'"]').prop('checked', true);
	} else {
		$('input[id*="'+className+'"]').prop('checked', false);
	}
}

function heart_beat() {
	$('#real_time').html(moment().format('h:mm:ss a'));
}

function loadReport(report) {
	sd = $('#start_date').val();
	ed = $('#end_date').val();
	$('#report_area').html(loadingBar).load(encodeURI(report+'?k='+k+'&sd='+sd+'&ed='+ed+'&t='+moment()));
}

function printReport(report) {
	sd = $('#start_date').val();
	ed = $('#end_date').val();
	//$('#report_area').html(loadingBar).load(encodeURI(report+'?k='+k+'&sd='+sd+'&ed='+ed+'&t='+moment()));
	window.print();
}

setInterval('heart_beat()', 1000);

