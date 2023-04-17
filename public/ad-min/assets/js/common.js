$(function () {
    pages.common.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    common: {
    	init: function () {
    		
    		$(document).on( "click", "#addMore", {}, function(){
    			var html = $('#inputContainer').html();
    			$('#fisrtContainer').append( html );
    		});
    		
    		$(document).on( "click", "#uploadImage", {}, function( e ){
    			e.preventDefault();
    			if( $('#image').val() == '' ){
    				alert($('#image').attr('msg'));
    				return false;
    			}
    			
    			//var formData = new FormData();
    			// Main magic with files here
//    			formData.append('file', $('#image')[0].files[0]); 
    			
    			var filedata = document.getElementsByName("image[]");
                var formdata = false;
		        if (window.FormData) {
		            formdata = new FormData();
		        }
		        var i = 0, len = filedata.length, img, reader, file;
		
		        for (; i < len; i++) {
		            file = filedata[i].files[0];
		            if (formdata) {
		                formdata.append("image[]", file);
		            }
		        }
		        if (formdata) {
	    			$.ajax({
	    			    url: '/admin/media/upload',
	    			    data: formdata,
	    			    // THIS MUST BE DONE FOR FILE UPLOADING
	    			    contentType: false,
	    			    processData: false,
	    			    type:'POST',
	    			    // ... Other options like success and etc
	    			    beforeSend: function() {
	    			    	$('#uploadImage').hide();
	    			    	$('.loading').show();
	    			    },
	    			    success: function( data ) {
	    			    	if( pages.core.isDefined( data.Data ) && data.Data.code == 1 && data.Data.info.length > 0){
	    			    		$('#uploadImage').show();
	    			    		$('.loading').hide();
	    			    		$.each(data.Data.info, function( k, image ){
	    			    			$image = $('<div class="grid-item col-lg-2">'
	    			    					+	'<div class="thumbnail">'
	    			    					+		'<div class="thumb">'
	    			    					+			'<img src="/upload/images'+ image.url_thumnail + '" alt="">'
	    			    					+			'<div class="caption-overflow media-select-image choose-img"'
	    			    					+				'data_id="'+ image.media_id +'"'
	    			    					+				'data-src-thumb="/upload/images'+ image.url_thumnail +'"'
	    			    					+				'data-src="/upload/images'+ image.url +'">'
	    			    					+			'</div>'
	    			    					+		'</div>'
	    			    					+		'<div class="caption media-title">'
	    			    					+			'<h6 class="no-margin"><a href="#" class="text-default">'+ pages.common.truncate( image.url )+'</a></h6>'
	    			    					+		'</div>'
	    			    					+	'</div>'
	    			    					+'</div>');
	    			    			
	    			    			$('.grid').append( $image )
	    			    			// add and lay out newly appended items
	    			    			.masonry( 'prepended', $image );
	    			    			//    			    		$('.grid').masonry('reloadItems');
	    			    			setTimeout(function(){ $grid.masonry() }, 400);
	    			    		});
	    			    		$('#formUploadImage input[name^=image]').val('');
	    			    	}
	    			    },
	    			    error: function() {
	    			    }
	    			})
		        }
    		});
    	},
    	truncate: function( str ){
    		var pos = str.indexOf( '_', 20 );
    		var temp = str.substring( pos + 1 );
    		var posDot = temp.indexOf( '.' );
    		var result = temp.substring( 0, posDot );
    		return result;
    	},
    	setupMasonry: function(){
    		$grid  = $('.grid').masonry({});
    		setTimeout(function(){ $grid.masonry() }, 600);
    	},
    	string_to_slug: function (str) {
    		  str = str.replace(/^\s+|\s+$/g, ''); // trim
    		  str = str.toLowerCase();
    		  // remove accents, swap ñ for n, etc
    		  var from = "ảãạẵẳặằắăậẫẩầấâàáäâẹẽẻệễểềếêèéëêìíïîỉĩịợỡởờớơộỗồốôõỏọòóöôựữửừứưũủụùúüûñç·/_,:;";
    		  var to   = "aaaaaaaaaaaaaaaaaaaeeeeeeeeeeeeeiiiiiiioooooooooooooooooouuuuuuuuuuuuunc------";
    		  for (var i=0, l=from.length ; i<l ; i++) {
    		    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    		  }

    		  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    		    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    		    .replace(/-+/g, '-'); // collapse dashes

    		  return str;
    	},
    	executeSearchForm: function(formId, tableId) {
        	var t = $('#' + tableId).DataTable();
        	$("#" + formId + " [item-type=search]").each(function(){
        		var value = $(this).val();
        		var column = $(this).attr('mapping-column');
        		var dataFormat = $(this).attr('data-format');
        		if ( pages.core.isDefined(dataFormat) && dataFormat != '' && value!='') {
        			value= value.split('/');
        			value = value[1]+'/'+value[0]+'/'+value[2];
        			value = pages.datetime.formatValueSearchDate( value , dataFormat );
        		}
        		t.column(column).search(value, false, false);
        	});
        	t.draw();
        },
    	setupDatePicker:function(){
    		$('.datepicker').each(function(){
	    		var format = 'dd/mm/yyyy';
	    		if ( pages.core.isDefined( $(this).attr("format") ) && $(this).attr("format") != "" ) {
	    			format = $(this).attr("format");
	    		}
	    		$(this).datepicker({
	        		format: format,
	        		autoclose: 1,
	        		todayHighlight: 1,
	        		minView: 2,
	        		fontAwesome: true
	            });
	    	});
    	},
        dataTableLang: {
            "sEmptyTable": translate("no-data-available-in-table"),
            "sInfo": translate("showing")+' _START_ '+ translate("to") +' _END_ '+translate("of") +' _TOTAL_ '+ translate("entries"),
            "sInfoEmpty":  translate("showing")+' 0 '+ translate("to") +' 0 '+translate("of") +' 0 '+ translate("entries"),
            "sInfoFiltered": translate("filtered-from")+ " _MAX_ " + translate("total-entries"),
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "<span>"+translate("show")+":</span> _MENU_",
            "sLoadingRecords": translate("loading"),
            "sProcessing": "<span class=\"fa fa-spinner fa-spin icon-loadding\"></span> "+translate("processing"),
            "sSearch": "<span>"+translate('search')+":</span> _INPUT_",
            "sZeroRecords": translate("no-matching-records-found"),
            "oPaginate": {
                "sFirst": translate("first"),
                "sLast": translate("last"),
                "sNext": translate("next"),
                "sPrevious": translate("previous")
            },
            "oAria": {
                "sSortAscending": ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        },
        //setup datatables by ajax
        setupDataTable: function (selector, requestUrl, aoColumns, columnDefs, opts) {
            var me = this;
            var stateSave = false;
            // Do nothing to an already setup table
            if ($(selector).hasClass('no-data') || $(selector).hasClass('setup')) {
                return;
            }
            var ordering = true;
            if ($(selector).hasClass('no-ordering')) {
                ordering = false;
            }
            var serial = false;
            if ($(selector).hasClass('serial')) {
                serial = true;
            }
            order = [[0, 'desc']];
            if (pages.core.isDefined(opts) && pages.core.isDefined(opts.order)) {
                order = opts.order;
            }
            $(selector).addClass('setup');
            $(selector).on('xhr.dt', function (e, settings, json) {
                if (json && json.Code == pages.constant.CODE_SESSION_EXPIRED) {
                    //go to login page if session expire
                    window.location.href = "/";
                    return false;
                }
            });
            $(selector).dataTable({
                "processing": true,
                "serverSide": true,
                "ordering": ordering,
                "stateSave": stateSave,
                "bDestroy": true,
                "stateDuration": 0,
                "paging": true,
                "sPaginationType": "full_numbers",
                "dom": '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                "language": pages.common.dataTableLang,
                "pageLength": 10,
                "ajax": {
                    "url": requestUrl,
                    "dataType": "json",
                    "silent": true,
                    "silent_sp": true
                },
                "aoColumns": aoColumns,
                "columnDefs": columnDefs,
                "order": order,
                "orderMulti": true,
                "fnDrawCallback": function () {
                    if ($(".dataTables_paginate").find(".paginate_button").length <= 5) {
                        $('.dataTables_wrapper div.dataTables_paginate').hide();
                    } else {
                        $('.dataTables_wrapper div.dataTables_paginate').show();
                    }
                    pages.common.setupCheckbox();
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    if (serial == true) {
                        $("td:first", nRow).html(iDisplayIndex + 1);
                        return nRow;
                    }
                }
            });
            
             // Add placeholder to the datatable filter option
            $('.dataTables_filter input[type=search]').attr('placeholder', translate('type-to-filter'));
            // Enable Select2 select for the length option
            $('.dataTables_length select').select2({
                minimumResultsForSearch: "-1"
            });
        },
        setupCheckbox : function(){
            // Primary
            $(".control-primary").uniform({
                radioClass: 'choice',
                wrapperClass: 'border-primary-600 text-primary-800'
            });
        },
    }
});