var bandingServerFile = new baseObject2({
	prefix : 'bandingServerFile',
	url : 'pages.php?page=bandingServerFile',
	formName : 'bandingServerFileForm',

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
			url: this.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#bandingServerFileForm").html(resp.content.tableContent);
				}else{
					bandingServerFile.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
	  var errMsg = bandingServerFile.getJumlahChecked();
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
	          bandingServerFile.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     bandingServerFile.errorAlert(errMsg);
	  }
	},
	ManageDisk: function(){
	  var errMsg = bandingServerFile.getJumlahChecked();
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
	          bandingServerFile.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     bandingServerFile.errorAlert(errMsg);
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
							data : $("#"+bandingServerFile.formName).serialize(),
				      url: bandingServerFile.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  bandingServerFile.suksesAlert("Data Terhapus",bandingServerFile.homePage);
                }else{
                  bandingServerFile.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      bandingServerFile.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = bandingServerFile.url;
	},
  submitCheck: function(){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize(),
        url: this.url+'&API=submitCheck',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
							var myTable = document.getElementById("tableResult");
							var rowCount = myTable.rows.length;
							for (var x=rowCount-1; x>0; x--) {
							   myTable.deleteRow(x);
							}
              if(resp.content.jumlahData == 0){
								alert("Data tidak di temukan");
							}else{
								me.prosesCheck(1,resp.content.jumlahData,resp.content.idFileCheck);
							}
            }else{
              bandingServerFile.errorAlert(resp.err);
            }
          }
      });
		},
  showList: function(){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize(),
        url: this.url+'&API=showList',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
							var myTable = document.getElementById("tableResult");
							var rowCount = myTable.rows.length;
							for (var x=rowCount-1; x>0; x--) {
							   myTable.deleteRow(x);
							}
              if(resp.content.jumlahData == 0){
								alert("Data tidak di temukan");
							}else{
								$("#tableResult").find('tbody').append( resp.content.tableResult );
							}
            }else{
              bandingServerFile.errorAlert(resp.err);
            }
          }
      });
		},
  prosesCheck: function(nomorUrut,jumlahData,idFileCheck){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&idFileCheck="+idFileCheck,
        url: this.url+'&API=prosesCheck',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.content.sukses!=''){
							$("#prosesBarValue").attr("data-value",100);
							$("#prosesBarWarna").attr("style","width:"+100+"%");
							$("#prosesBarText").text("100%");
							$("#tempatButtonPush").html("<button type='button' onclick=bandingServerFile.pushFileSelected(); class='btn btn-alt btn-hover btn-success'> <span>Push</span><i class='glyph-icon icon-save'></i></button>")
							alert("Check Selesai");
            }else{
							$("#prosesBarValue").attr("data-value",resp.content.persen);
							$("#prosesBarWarna").attr("style","width:"+resp.content.persen+"%");
							$("#prosesBarText").text(resp.content.persenText);
							var table = document.getElementById("tableResult");
							var row = table.insertRow(nomorUrut);
							var cell1 = row.insertCell(0);
							var cell2 = row.insertCell(1);
							var cell3= row.insertCell(2);
							var cell4 = row.insertCell(3);
							var cell5 = row.insertCell(4);
							var cell6 = row.insertCell(5);
							var idCheckbox = nomorUrut - 1;
							cell1.innerHTML = nomorUrut;
							if(resp.content.reason != ''){
								cell2.innerHTML = "<input class='custom-checkbox' type='checkbox' id='bandingServerFile_cb"+ idCheckbox+"' name='bandingServerFile_cb[]' value='"+resp.content.namaFile+"' onchange='bandingServerFile.thisChecked('bandingServerFile_cb"+idCheckbox+"','bandingServerFile_jmlcek');'>";
							}else{
								cell2.innerHTML = "";
							}
							cell3.innerHTML = resp.content.namaFile;
							cell4.innerHTML = resp.content.tanggalDiServer;
							cell5.innerHTML = resp.content.status;
							cell6.innerHTML = resp.content.reason;
							$("#bandingServerFile_toogle").attr("onclick","bandingServerFile.checkSemua("+nomorUrut+",'bandingServerFile_cb','','bandingServerFile_jmlcek',this)");
							nomorUrut = nomorUrut + 1;
              me.prosesCheck(nomorUrut,jumlahData,idFileCheck);
            }
          }
      });
		},
		pushFileSelected: function(){
				$(window).scrollTop(0);
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize(),
	        url: this.url+'&API=pushFileSelected',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
	              if(resp.content.jumlahData == 0){
									alert("Data tidak di temukan");
								}else{
									me.prosesPush(1,resp.content.namaFile,resp.content.jumlahData);
								}
	            }else{
	              bandingServerFile.errorAlert(resp.err);
	            }
	          }
	      });
		},
		prosesPush: function(nomorUrut,namaFile,jumlahData){
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&namaFile="+namaFile,
	        url: this.url+'&API=prosesPush',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.content.sukses!=''){
								$("#prosesBarValue").attr("data-value",100);
								$("#prosesBarWarna").attr("style","width:"+100+"%");
								$("#prosesBarText").text("100%");
								alert("Copy Selesai");
	            }else{
								$("#prosesBarValue").attr("data-value",resp.content.persen);
								$("#prosesBarWarna").attr("style","width:"+resp.content.persen+"%");
								$("#prosesBarText").text(resp.content.persenText);
								nomorUrut = nomorUrut + 1;
	              me.prosesPush(nomorUrut,resp.content.namaFile,jumlahData);
	            }
	          }
	      });
			},



});
