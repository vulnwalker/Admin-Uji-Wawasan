var historyBackup = new baseObject2({
	prefix : 'historyBackup',
	url : 'pages.php?page=historyBackup',
	formName : 'historyBackupForm',

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
		$("#filterCari").val(a.value);
	},
	refreshList: function(){
		$.ajax({
			type:'POST',
			data : {
								filterCari : $('#filterCari').val()
							},
			url: historyBackup.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#historyBackupForm").html(resp.content.tableContent);
				}else{
					historyBackup.errorAlert(resp.err);
				}
			}
		});
	},
	hardDiskChanged: function(){
		$.ajax({
			type:'POST',
			data : {
								idServer : $("#serverTujuan").val(),
								hardDiskTujuan : $("#hardDiskTujuan").val(),
							},
			url: historyBackup.url+'&API=hardDiskChanged',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#hardDiskSize").val(resp.content.hardDiskSize);
					$("#hardDiskUsed").val(resp.content.hardDiskUsed);
					$("#hardDiskFree").val(resp.content.hardDiskFree);
					$(".progressbar").attr('data-value',resp.content.persen);
					$("#persentase").attr('style','width:'+resp.content.persen+"%");
					$(".progress-label").text(resp.content.persen+"%");
					$("#backupLocation").val(resp.content.backupLocation);
				}else{
					historyBackup.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
	  var errMsg = historyBackup.getJumlahChecked();
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
	          historyBackup.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     historyBackup.errorAlert(errMsg);
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
							data : $("#"+historyBackup.formName).serialize(),
				      url: historyBackup.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  historyBackup.suksesAlert("Data Terhapus",historyBackup.homePage);
                }else{
                  historyBackup.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      historyBackup.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = historyBackup.url;
	},
	sendTo: function(){
  var errMsg = this.getJumlahChecked();
  if(errMsg == '' || errMsg=='Pilih hanya satu data'){
      $.ajax({
          type:'POST',
					data : $("#"+historyBackup.formName).serialize(),
		      url: historyBackup.url+'&API=sendTo',

          success: function(data) {
            var resp = eval('(' + data + ')');
            if(resp.err==''){
								$("#modalSendTo").remove();
								$("#tempatModal").html(resp.content);
								$("#modalSendTo").modal();
            }else{
              historyBackup.errorAlert(resp.err);
            }
          }
        });
    }else{
      historyBackup.errorAlert(errMsg);
    }
  },

	saveSendTo: function(){
		swal({
	      title: "Copy Backup ?",
	      text: "",
	      type: "info",
	      confirmButtonText: "Ya",
	      cancelButtonText: "Tidak",
	      showCancelButton: true,
	      closeOnConfirm: false,
	      showLoaderOnConfirm: true
	    }, function () {
							$.ajax({
								type:'POST',
								data : $("#"+historyBackup.formName+"_sendTo").serialize(),
								url: historyBackup.url+'&API=saveSendTo',
								timeout: 9000000000000,
									success: function(data) {
									var resp = eval('(' + data + ')');
										if(resp.err==''){
												$("#closeModal").click();
												historyBackup.copyDatabase(1);
										}else{
												historyBackup.errorAlert(resp.err);
										}
									}
							});
	    });
	},

	copyDatabase: function(urutanKe){
			$.ajax({
				type:'POST',
				data : $("#"+historyBackup.formName+"_sendTo").serialize()+"&urutanKe="+urutanKe,
				url: historyBackup.url+'&API=copyDatabase',
				timeout: 9000000000000,
					error: function (xhr, ajaxOptions, thrownError) {
						console.log(xhr.status);
						console.log(thrownError);
						historyBackup.copyDatabase(urutanKe+1);
		      },
					success: function(data) {
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							if(resp.content.status == 'OK'){
								historyBackup.suksesAlert("Copy Success",historyBackup.refreshList);
							}else{
								historyBackup.copyDatabase(resp.content.urutanKe);
							}
						}else{
							historyBackup.errorAlert(resp.err);
						}
					}
			});
	},



});
