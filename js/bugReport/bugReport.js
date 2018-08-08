var bugReport = new baseObject2({
	prefix : 'bugReport',
	url : 'pages.php?page=bugReport',
	formName : 'bugReportForm',

	getJumlahChecked: function() {
    var jmldata= document.getElementById( this.prefix+'_jmlcek' ).value;
    for(var i=0; i < jmldata; i++){
      var box = document.getElementById( this.prefix+'_cb' + i);
      if( box.checked){
        break;
      }
    }
    var err = "";
    if(jmldata == 0){
        err = "Pilih data";
    }else if(jmldata > 1){
        err = "Pilih hanya satu data";
    }
    return err;
	},
	checkSemua: function(jumlahData,fldName,elHeaderChecked,elJmlCek,fuckYeah) {
    if (!fldName) {
      fldName = 'cb';
    }
    if (!elHeaderChecked) {
      elHeaderChecked = 'toggle';
    }
    var c = fuckYeah.checked;
    var n2 = 0;
    for (i=0; i < jumlahData ; i++) {
     cb = document.getElementById(fldName+i);
     if (cb) {
       cb.checked = c;
       n2++;
     }
    }
    if (c) {
     document.getElementById(elJmlCek).value = n2;
    } else {
     document.getElementById(elJmlCek).value = 0;
    }
	},
	thisChecked: function(idCheckbox,elJmlCek) {
    var c = document.getElementById(idCheckbox).checked;
    var jumlahCheck = parseInt($("#"+elJmlCek).val());
    if(c){
        document.getElementById(elJmlCek).value = jumlahCheck + 1;
    }else{
        document.getElementById(elJmlCek).value = jumlahCheck - 1;
    }
	},
	formatCurrency: function(num) {
		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
		num = "0";
		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();
		if(cents<10)
		cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+'.'+
		num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') + '' + num + ',' + cents);
	},
	setValueFilter: function(a){
		// $("#filterCari").val(a.value);
		var table = $('#dataServer').DataTable();
		table.search($(a).val()).draw() ;
	},
	refreshList: function(){
		$.ajax({
			type:'POST',
			data : {
								filterCari : $('#filterCari').val()
							},
			url: this.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){

					window.location.reload();
				}else{
					bugReport.errorAlert(resp.err);
				}
			}
		});
	},
	exec_body_scripts: function(text) {
		 var scripts = '';
		 var cleaned = text.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
				 scripts += arguments[1] + '\n';
				 return '';
		 });

		 if (window.execScript){
				 window.execScript(scripts);
		 } else {
				 var head = document.getElementsByTagName('head')[0];
				 var scriptElement = document.createElement('script');
				 scriptElement.setAttribute('type', 'text/javascript');
				 scriptElement.innerText = scripts;
				 head.appendChild(scriptElement);
				 head.removeChild(scriptElement);
		 }
		 return cleaned;

  },
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
		var errMsg = bugReport.getJumlahChecked();
		urlEdit = this.url;
		if(errMsg == ''){
			$.ajax({
				type:'POST',
				data : $("#"+this.formName).serialize(),
				url: this.url+'&API=Edit',
				success: function(data) {
					var resp = eval('(' + data + ')');
					if(resp.err==''){
						$("#modalRelease").remove();
						$("#tempatModal").html(resp.content);
						$("#modalRelease").modal();
						"use strict";
						$(".multi-select").multiSelect();
						$(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
					}else{
						bugReport.errorAlert(resp.err);
					}
				}
			});
		}else{
			 bugReport.errorAlert(errMsg);
		}
	},
	ManageDisk: function(){
	  var errMsg = bugReport.getJumlahChecked();
		urlEdit = this.url;
	  if(errMsg == ''){
	    $.ajax({
	      type:'POST',
	      data : $("#"+this.formName).serialize(),
	      url: this.url+'&API=ManageDisk',
	      success: function(data) {
	        var resp = eval('(' + data + ')');
	        if(resp.err==''){
	          window.location = "pages.php?page=refDisk&idServer="+resp.content.idEdit;
	        }else{
	          bugReport.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     bugReport.errorAlert(errMsg);
	  }
	},
	Hapus: function(){
  var errMsg = this.getJumlahChecked();
  if(errMsg == '' || errMsg=='Pilih hanya satu data'){
    swal({
          title: "Hapus Data ?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
          closeOnConfirm: false
        },
        function(){
          $.ajax({
              type:'POST',
							data : $("#"+bugReport.formName).serialize(),
				      url: bugReport.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  bugReport.suksesAlert("Data Terhapus",bugReport.homePage);
                }else{
                  bugReport.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      bugReport.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = bugReport.url;
	},
  saveNew: function(){
	  // swal({
	  //       title: "Simpan Data ?",
	  //       text: "",
	  //       type: "info",
	  //       confirmButtonText: "Ya",
	  //       cancelButtonText: "Tidak",
	  //       showCancelButton: true,
	  //       closeOnConfirm: false,
	  //       showLoaderOnConfirm: true
	  //     }, function () {
	        $.ajax({
	          type:'POST',
	          data : $("#"+this.formName+"_new").serialize(),
	          url: this.url+'&API=saveNew',
	            success: function(data) {
	            var resp = eval('(' + data + ')');
	              if(resp.err==''){
	                bugReport.suksesAlert("Data Tersimpan",bugReport.homePage);
	              }else{
	                bugReport.errorAlert(resp.err);
	              }
	            }
	        });
	      // });
		},
		saveEdit: function(idEdit){
			var me = this;

		  // swal({
		  //       title: "Simpan Data ?",
		  //       text: "",
		  //       type: "info",
		  //       confirmButtonText: "Ya",
		  //       cancelButtonText: "Tidak",
		  //       showCancelButton: true,
		  //       closeOnConfirm: false,
		  //       showLoaderOnConfirm: true
		  //     }, function () {
		        $.ajax({
		          type:'POST',
		          data : $("#"+this.formName+"_edit").serialize()+"&idEdit="+idEdit,
		          url: this.url+'&API=saveEdit',
		            success: function(data) {
		            var resp = eval('(' + data + ')');
		              if(resp.err==''){
		                me.suksesAlert("Data Tersimpan",me.closeModal);
										me.refreshList();
		              }else{
		                me.errorAlert(resp.err);
		              }
		            }
		        });
		      // });
			},
			closeModal: function(){
				swal.close();
				$("#closeModal").click();
			}


});
