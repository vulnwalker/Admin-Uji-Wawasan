var refServer = new baseObject2({
	prefix : 'refServer',
	url : 'pages.php?page=refServer',
	formName : 'refServerForm',

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
					$("#refServerForm").html(resp.content.tableContent);
				}else{
					refServer.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
	  var errMsg = refServer.getJumlahChecked();
		urlEdit = this.url;
	  if(errMsg == ''){
	    $.ajax({
	      type:'POST',
	      data : $("#"+this.formName).serialize(),
	      url: this.url+'&API=Edit',
	      success: function(data) {
	        var resp = eval('(' + data + ')');
	        if(resp.err==''){
	          window.location = urlEdit+"&action=edit&idEdit="+resp.content.idEdit;
	        }else{
	          refServer.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     refServer.errorAlert(errMsg);
	  }
	},
	ManageDisk: function(){
	  var errMsg = refServer.getJumlahChecked();
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
	          refServer.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     refServer.errorAlert(errMsg);
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
							data : $("#"+refServer.formName).serialize(),
				      url: refServer.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  refServer.suksesAlert("Data Terhapus",refServer.homePage);
                }else{
                  refServer.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      refServer.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = refServer.url;
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
	                refServer.suksesAlert("Data Tersimpan",refServer.homePage);
	              }else{
	                refServer.errorAlert(resp.err);
	              }
	            }
	        });
	      // });
		},
  saveEdit: function(idEdit){
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
	                refServer.suksesAlert("Data Tersimpan",refServer.homePage);
	              }else{
	                refServer.errorAlert(resp.err);
	              }
	            }
	        });
	      // });
		},



});
