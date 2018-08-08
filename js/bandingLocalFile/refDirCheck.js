var refDirCheck = new baseObject2({
	prefix : 'refDirCheck',
	url : 'pages.php?page=refDirCheck',
	formName : 'refDirCheckForm',


	getJumlahChecked: function() {
		var me = this;
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
		var me = this;

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
		var me = this;

    var c = document.getElementById(idCheckbox).checked;
    var jumlahCheck = parseInt($("#"+elJmlCek).val());
    if(c){
        document.getElementById(elJmlCek).value = jumlahCheck + 1;
    }else{
        document.getElementById(elJmlCek).value = jumlahCheck - 1;
    }
	},
	formatCurrency: function(num) {
		var me = this;

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
		var me = this;

		$("#filterCari").val(a.value);
	},
	refreshList: function(){
		var me = this;

		$.ajax({
			type:'POST',
			data : {
								filterCari : $('#filterCari').val()
							},
			url: this.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#refDirCheckForm").html(resp.content.tableContent);
				}else{
					me.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		var me = this;

		$.ajax({
			data : $("#"+this.formName).serialize(),
			type:'POST',
			url: this.url+'&API=formBaru',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						me.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalForm").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	Edit: function(){
		var me = this;
	  var errMsg = me.getJumlahChecked();
		urlEdit = this.url;
	  if(errMsg == ''){
	    $.ajax({
	      type:'POST',
	      data : $("#"+this.formName).serialize(),
	      url: this.url+'&API=Edit',
	      success: function(data) {
	        var resp = eval('(' + data + ')');
	        if(resp.err==''){
						me.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalForm").modal();
	        }else{
	          me.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     me.errorAlert(errMsg);
	  }
	},
	ManageDisk: function(){
		var me = this;

	  var errMsg = me.getJumlahChecked();
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
	          me.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     me.errorAlert(errMsg);
	  }
	},
	Hapus: function(){
	var me = this;

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
							data : $("#"+me.formName).serialize(),
				      url: me.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  me.suksesAlert("Data Terhapus",me.homePage);
                }else{
                  me.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      me.errorAlert(errMsg);
    }
  },
	homePage: function(){
		var me = this;

		window.location = me.url;
	},
  saveNew: function(){
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
	          data : $("#"+this.formName+"_new").serialize(),
	          url: this.url+'&API=saveNew',
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

});
