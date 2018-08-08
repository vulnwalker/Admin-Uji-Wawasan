var bandingDatabaseServer = new baseObject2({
	prefix : 'bandingDatabaseServer',
	url : 'pages.php?page=bandingDatabaseServer',
	formName : 'bandingDatabaseServerForm',

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
					$("#bandingDatabaseServerForm").html(resp.content.tableContent);
				}else{
					bandingDatabaseServer.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new";
	},
	Edit: function(){
	  var errMsg = bandingDatabaseServer.getJumlahChecked();
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
	          bandingDatabaseServer.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     bandingDatabaseServer.errorAlert(errMsg);
	  }
	},
	ManageDisk: function(){
	  var errMsg = bandingDatabaseServer.getJumlahChecked();
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
	          bandingDatabaseServer.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     bandingDatabaseServer.errorAlert(errMsg);
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
							data : $("#"+bandingDatabaseServer.formName).serialize(),
				      url: bandingDatabaseServer.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  bandingDatabaseServer.suksesAlert("Data Terhapus",bandingDatabaseServer.homePage);
                }else{
                  bandingDatabaseServer.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      bandingDatabaseServer.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = bandingDatabaseServer.url;
	},
  checkStruktur: function(){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize(),
        url: this.url+'&API=checkStruktur',
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
								me.prosesCheckStruktur(1,resp.content.jumlahData,resp.content.idFileCheck);
							}
            }else{
              bandingDatabaseServer.errorAlert(resp.err);
            }
          }
      });
		},
  checkTriger: function(){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize(),
        url: this.url+'&API=checkTriger',
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
								me.prosesCheckTriger(1,resp.content.jumlahData,resp.content.idFileCheck);
							}
            }else{
              bandingDatabaseServer.errorAlert(resp.err);
            }
          }
      });
		},
  checkRoutine: function(){
			var me = this;
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize(),
        url: this.url+'&API=checkRoutine',
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
								me.prosesCheckRoutine(1,resp.content.jumlahData,resp.content.idFileCheck);
							}
            }else{
              bandingDatabaseServer.errorAlert(resp.err);
            }
          }
      });
		},
		prosesCheckStruktur: function(nomorUrut,jumlahData,idFileCheck){
				var me = this;
				$.ajax({
					type:'POST',
					data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&idFileCheck="+idFileCheck,
					url: this.url+'&API=prosesCheckStruktur',
						success: function(data) {
						var resp = eval('(' + data + ')');
							if(resp.content.sukses!=''){
								$("#prosesBarValue").attr("data-value",100);
								$("#prosesBarWarna").attr("style","width:"+100+"%");
								$("#prosesBarText").text("100%");
								$("#tempatButtonPush").html("<button type='button' onclick=bandingDatabaseServer.pushStrukturSelected(); class='btn btn-alt btn-hover btn-success'> <span>Push</span><i class='glyph-icon icon-save'></i></button>")
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
								cell2.innerHTML = "<input class='custom-checkbox' type='checkbox' id='bandingDatabaseServer_cb"+ idCheckbox+"' name='bandingDatabaseServer_cb[]' value='"+resp.content.valueOfCheckbox+"' onchange='bandingDatabaseServer.thisChecked('bandingDatabaseServer_cb"+idCheckbox+"','bandingDatabaseServer_jmlcek');'>";
								cell3.innerHTML = resp.content.tableName;
								cell4.innerHTML = resp.content.columnName;
								cell5.innerHTML = resp.content.status;
								cell6.innerHTML = resp.content.reason;
								$("#bandingDatabaseServer_toogle").attr("onclick","bandingDatabaseServer.checkSemua("+nomorUrut+",'bandingDatabaseServer_cb','','bandingDatabaseServer_jmlcek',this)");
								nomorUrut = nomorUrut + 1;
								me.prosesCheckStruktur(nomorUrut,jumlahData,idFileCheck);
							}
						}
				});
			},
		prosesCheckTriger: function(nomorUrut,jumlahData,idFileCheck){
				var me = this;
				$.ajax({
					type:'POST',
					data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&idFileCheck="+idFileCheck,
					url: this.url+'&API=prosesCheckTriger',
						success: function(data) {
						var resp = eval('(' + data + ')');
							if(resp.content.sukses!=''){
								$("#prosesBarValue").attr("data-value",100);
								$("#prosesBarWarna").attr("style","width:"+100+"%");
								$("#prosesBarText").text("100%");
								$("#tempatButtonPush").html("<button type='button' onclick=bandingDatabaseServer.pushTrigerSelected(); class='btn btn-alt btn-hover btn-success'> <span>Push</span><i class='glyph-icon icon-save'></i></button>")
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
								var idCheckbox = nomorUrut - 1;
								cell1.innerHTML = nomorUrut;
								cell2.innerHTML = "<input class='custom-checkbox' type='checkbox' id='bandingDatabaseServer_cb"+ idCheckbox+"' name='bandingDatabaseServer_cb[]' value='"+resp.content.valueOfCheckbox+"' onchange='bandingDatabaseServer.thisChecked('bandingDatabaseServer_cb"+idCheckbox+"','bandingDatabaseServer_jmlcek');'>";
								cell3.innerHTML = resp.content.trigerName;
								cell4.innerHTML = resp.content.status;
								cell5.innerHTML = resp.content.reason;
								$("#bandingDatabaseServer_toogle").attr("onclick","bandingDatabaseServer.checkSemua("+nomorUrut+",'bandingDatabaseServer_cb','','bandingDatabaseServer_jmlcek',this)");
								nomorUrut = nomorUrut + 1;
								me.prosesCheckTriger(nomorUrut,jumlahData,idFileCheck);
							}
						}
				});
			},
		prosesCheckRoutine: function(nomorUrut,jumlahData,idFileCheck){
				var me = this;
				$.ajax({
					type:'POST',
					data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&idFileCheck="+idFileCheck,
					url: this.url+'&API=prosesCheckRoutine',
						success: function(data) {
						var resp = eval('(' + data + ')');
							if(resp.content.sukses!=''){
								$("#prosesBarValue").attr("data-value",100);
								$("#prosesBarWarna").attr("style","width:"+100+"%");
								$("#prosesBarText").text("100%");
								$("#tempatButtonPush").html("<button type='button' onclick=bandingDatabaseServer.pushRoutineSelected(); class='btn btn-alt btn-hover btn-success'> <span>Push</span><i class='glyph-icon icon-save'></i></button>")
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
								var idCheckbox = nomorUrut - 1;
								cell1.innerHTML = nomorUrut;
								cell2.innerHTML = "<input class='custom-checkbox' type='checkbox' id='bandingDatabaseServer_cb"+ idCheckbox+"' name='bandingDatabaseServer_cb[]' value='"+resp.content.valueOfCheckbox+"' onchange='bandingDatabaseServer.thisChecked('bandingDatabaseServer_cb"+idCheckbox+"','bandingDatabaseServer_jmlcek');'>";
								cell3.innerHTML = resp.content.routineName;
								cell4.innerHTML = resp.content.status;
								cell5.innerHTML = resp.content.reason;
								$("#bandingDatabaseServer_toogle").attr("onclick","bandingDatabaseServer.checkSemua("+nomorUrut+",'bandingDatabaseServer_cb','','bandingDatabaseServer_jmlcek',this)");
								nomorUrut = nomorUrut + 1;
								me.prosesCheckRoutine(nomorUrut,jumlahData,idFileCheck);
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
              bandingDatabaseServer.errorAlert(resp.err);
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
							$("#tempatButtonPush").html("<button type='button' onclick=bandingDatabaseServer.pushFileSelected(); class='btn btn-alt btn-hover btn-success'> <span>Push</span><i class='glyph-icon icon-save'></i></button>")
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
							var idCheckbox = nomorUrut - 1;
							cell1.innerHTML = nomorUrut;
							cell2.innerHTML = "<input class='custom-checkbox' type='checkbox' id='bandingDatabaseServer_cb"+ idCheckbox+"' name='bandingDatabaseServer_cb[]' value='"+resp.content.namaFile+"' onchange='bandingDatabaseServer.thisChecked('bandingDatabaseServer_cb"+idCheckbox+"','bandingDatabaseServer_jmlcek');'>";
							cell3.innerHTML = resp.content.namaFile;
							cell4.innerHTML = resp.content.status;
							cell5.innerHTML = resp.content.reason;
							$("#bandingDatabaseServer_toogle").attr("onclick","bandingDatabaseServer.checkSemua("+nomorUrut+",'bandingDatabaseServer_cb','','bandingDatabaseServer_jmlcek',this)");
							nomorUrut = nomorUrut + 1;
              me.prosesCheck(nomorUrut,jumlahData,idFileCheck);
            }
          }
      });
		},
		pushStrukturSelected: function(){
				$(window).scrollTop(0);
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize(),
	        url: this.url+'&API=pushStrukturSelected',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
	              if(resp.content.jumlahData == 0){
									alert("Data tidak di temukan");
								}else{
									me.prosesPushStruktur(1,resp.content.valueStruktur,resp.content.jumlahData);
								}
	            }else{
	              bandingDatabaseServer.errorAlert(resp.err);
	            }
	          }
	      });
		},
		pushTrigerSelected: function(){
				$(window).scrollTop(0);
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize(),
	        url: this.url+'&API=pushTrigerSelected',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
	              if(resp.content.jumlahData == 0){
									alert("Data tidak di temukan");
								}else{
									me.prosesPushTriger(1,resp.content.valueTriger,resp.content.jumlahData);
								}
	            }else{
	              bandingDatabaseServer.errorAlert(resp.err);
	            }
	          }
	      });
		},
		pushRoutineSelected: function(){
				$(window).scrollTop(0);
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize(),
	        url: this.url+'&API=pushRoutineSelected',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
	              if(resp.content.jumlahData == 0){
									alert("Data tidak di temukan");
								}else{
									me.prosesPushRoutine(1,resp.content.valueRoutine,resp.content.jumlahData);
								}
	            }else{
	              bandingDatabaseServer.errorAlert(resp.err);
	            }
	          }
	      });
		},
		prosesPushStruktur: function(nomorUrut,valueStruktur,jumlahData){
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&valueStruktur="+valueStruktur,
	        url: this.url+'&API=prosesPushStruktur',
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
	              me.prosesPushStruktur(nomorUrut,resp.content.valueStruktur,jumlahData);
	            }
	          }
	      });
			},
		prosesPushTriger: function(nomorUrut,valueTriger,jumlahData){
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&valueTriger="+valueTriger,
	        url: this.url+'&API=prosesPushTriger',
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
	              me.prosesPushTriger(nomorUrut,resp.content.valueTriger,jumlahData);
	            }
	          }
	      });
			},
		prosesPushRoutine: function(nomorUrut,valueRoutine,jumlahData){
				var me = this;
	      $.ajax({
	        type:'POST',
	        data : $("#"+this.formName+"_new").serialize()+"&nomorUrut="+nomorUrut+"&jumlahData="+jumlahData+"&valueRoutine="+valueRoutine,
	        url: this.url+'&API=prosesPushRoutine',
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
	              me.prosesPushRoutine(nomorUrut,resp.content.valueTriger,jumlahData);
	            }
	          }
	      });
			},
			optionChanged: function(){
				var me = this;
				if($("#optionBanding").val() == 'struktur'){
					$("#buttonShow").attr("onclick","bandingDatabaseServer.showListStruktur()");
					$("#buttonCheck").attr("onclick","bandingDatabaseServer.checkStruktur()");
					$.ajax({
		        type:'POST',
		        data : $("#"+this.formName+"_new").serialize(),
		        url: me.url+'&API=tableResultStruktur',
		          success: function(data) {
		          var resp = eval('(' + data + ')');
		            if(resp.err==''){
									$("#tempatTableResult").html(resp.content);
		            }else{
		              bandingDatabaseServer.errorAlert(resp.err);
		            }
		          }
		      });
				}else if($("#optionBanding").val() == 'triger'){
					$("#buttonShow").attr("onclick","bandingDatabaseServer.showListTriger()");
					$("#buttonCheck").attr("onclick","bandingDatabaseServer.checkTriger()");
					$.ajax({
		        type:'POST',
		        data : $("#"+this.formName+"_new").serialize(),
		        url: me.url+'&API=tableResultTriger',
		          success: function(data) {
		          var resp = eval('(' + data + ')');
		            if(resp.err==''){
									$("#tempatTableResult").html(resp.content);
		            }else{
		              bandingDatabaseServer.errorAlert(resp.err);
		            }
		          }
		      });
				}else if($("#optionBanding").val() == 'routine'){
					$("#buttonShow").attr("onclick","bandingDatabaseServer.showListRoutine()");
					$("#buttonCheck").attr("onclick","bandingDatabaseServer.checkRoutine()");
					$.ajax({
		        type:'POST',
		        data : $("#"+this.formName+"_new").serialize(),
		        url: me.url+'&API=tableResultRoutine',
		          success: function(data) {
		          var resp = eval('(' + data + ')');
		            if(resp.err==''){
									$("#tempatTableResult").html(resp.content);
		            }else{
		              bandingDatabaseServer.errorAlert(resp.err);
		            }
		          }
		      });
				}
			},
			showListStruktur: function(){
					var me = this;
					$.ajax({
						type:'POST',
						data : $("#"+this.formName+"_new").serialize(),
						url: this.url+'&API=showListStruktur',
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
									bandingDatabaseServer.errorAlert(resp.err);
								}
							}
					});
				},
			showListTriger: function(){
					var me = this;
					$.ajax({
						type:'POST',
						data : $("#"+this.formName+"_new").serialize(),
						url: this.url+'&API=showListTriger',
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
									bandingDatabaseServer.errorAlert(resp.err);
								}
							}
					});
				},
			showListRoutine: function(){
					var me = this;
					$.ajax({
						type:'POST',
						data : $("#"+this.formName+"_new").serialize(),
						url: this.url+'&API=showListRoutine',
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
									bandingDatabaseServer.errorAlert(resp.err);
								}
							}
					});
				},

});
