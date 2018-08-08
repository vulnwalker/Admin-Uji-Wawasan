var detailRelease = new baseObject2({
	prefix : 'detailRelease',
	url : 'pages.php?page=detailRelease',
	formName : 'detailReleaseForm',

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
	refreshList: function(currentLocation){
		if(!currentLocation){
			currentLocation = $('#currentLocation').val();
		}
		$.ajax({
			type:'POST',
			data : {
								currentLocation : currentLocation,
								idRelease : $("#idRelease").val()
							},
			url: detailRelease.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#detailReleaseForm").html(resp.content.tableContent);
					if(currentLocation == '/'){
						$("#currentLocation").val('');
					}
				}else{
					detailRelease.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new"+"&location="+$("#currentLocation").val();
	},
	Edit: function(){
	  var errMsg = detailRelease.getJumlahChecked();
		urlEdit = this.url;
	  if(errMsg == ''){
	    $.ajax({
	      type:'POST',
	      data : $("#"+this.formName).serialize(),
	      url: this.url+'&API=Edit',
	      success: function(data) {
	        var resp = eval('(' + data + ')');
	        if(resp.err==''){
	          window.location = urlEdit+"&action=edit&location="+$("#currentLocation").val()+"&namaFile="+resp.content.namaFile;
	        }else{
	          detailRelease.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     detailRelease.errorAlert(errMsg);
	  }
	},
	Release: function(){
		var errMsg = this.getJumlahChecked();
		if(errMsg == '' || errMsg=='Pilih hanya satu data'){
				$.ajax({
						type:'POST',
						data : $("#"+detailRelease.formName).serialize()+"&idRelease="+$("#idRelease").val(),
						url: detailRelease.url+'&API=Release',
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
								detailRelease.errorAlert(resp.err);
							}
						}
					});
			}else{
				detailRelease.errorAlert(errMsg);
			}
	},
	pushAndExecute: function(namaSql){
			$.ajax({
					type:'POST',
					data : {
											locationRelease : $("#locationRelease").val(),
											currentLocation : $("#currentLocation").val(),
											idRelease : $("#idRelease").val(),
											namaSql : namaSql,

								},
					url: detailRelease.url+'&API=pushAndExecute',
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
							detailRelease.errorAlert(resp.err);
						}
					}
				});
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
							data : $("#"+detailRelease.formName).serialize(),
				      url: detailRelease.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  detailRelease.suksesAlert("Data Terhapus",detailRelease.refreshList);
                }else{
                  detailRelease.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      detailRelease.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = detailRelease.url;
	},
  saveNew: function(){
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize()+"&isiFile="+editor.getValue(),
        url: this.url+'&API=saveNew',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
              detailRelease.suksesAlert("Data Tersimpan",detailRelease.homePage);
            }else{
              detailRelease.errorAlert(resp.err);
            }
          }
      });
		},
  saveEdit: function(){
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_edit").serialize()+"&isiFile="+editor.getValue(),
        url: this.url+'&API=saveEdit',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
              detailRelease.suksesAlert("Data Tersimpan",detailRelease.homePage);
            }else{
              detailRelease.errorAlert(resp.err);
            }
          }
      });
		},

  setAsDatabaseFile: function(databaseFile){
		swal({
				title: "Jadikan Database File ?",
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
							data : {
												idRelease : $("#idRelease").val(),
												databaseFile : databaseFile,
												currentLocation : $("#currentLocation").val(),

										},
							url: detailRelease.url+'&API=setAsDatabaseFile',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
											swal.close();
									}else{
											detailRelease.errorAlert(resp.err);
									}
								}
						});
			});

		},
  executeRelease: function(){
		swal({
				title: "Push Release ?",
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
							data : $("#"+detailRelease.formName+"_release").serialize()+"&idRelease="+$("#idRelease").val(),
							url: detailRelease.url+'&API=executeRelease',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
											$("#closeModal").click();
											detailRelease.pushRelease(1);
									}else{
											detailRelease.errorAlert(resp.err);
									}
								}
						});
			});

		},
  sendSqlFile: function(){
		swal({
				title: "Execute Sql ?",
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
							data : $("#"+detailRelease.formName+"_release").serialize()+"&idRelease="+$("#idRelease").val(),
							url: detailRelease.url+'&API=sendSqlFile',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
											$("#closeModal").click();
											detailRelease.executeSqlFile(1);
									}else{
											detailRelease.errorAlert(resp.err);
									}
								}
						});
			});

		},
  pushRelease: function(urutanKe){
				$.ajax({
					type:'POST',
					data : $("#"+detailRelease.formName+"_release").serialize()+"&urutanKe="+urutanKe+"&idRelease="+$("#idRelease").val(),
					url: detailRelease.url+'&API=pushRelease',
						success: function(data) {
						var resp = eval('(' + data + ')');
							if(resp.err==''){
								if(resp.content.status == 'OK'){
									detailRelease.suksesAlert("Push Success",detailRelease.refreshList);
								}else{
									detailRelease.pushRelease(resp.content.urutanKe);
								}
							}else{
								detailRelease.errorAlert(resp.err);
							}
						}
				});
		},
  executeSqlFile: function(urutanKe){
				$.ajax({
					type:'POST',
					data : $("#"+detailRelease.formName+"_release").serialize()+"&urutanKe="+urutanKe+"&idRelease="+$("#idRelease").val(),
					url: detailRelease.url+'&API=executeSqlFile',
						success: function(data) {
						var resp = eval('(' + data + ')');
							if(resp.err==''){
								if(resp.content.status == 'OK'){
									detailRelease.suksesAlert("Execute Success",detailRelease.refreshList);
								}else{
									detailRelease.pushRelease(resp.content.urutanKe);
								}
							}else{
								detailRelease.errorAlert(resp.err);
							}
						}
				});
		},

		setCodeEditor: function(isiFile,programingLanguage){
			require.config({ paths: { 'vs': 'plugins/monaco-editor/min/vs' }});
    	require(['vs/editor/editor.main'], function() {
    		 editor = monaco.editor.create(document.getElementById('codeEditor'), {
					 value: [
						 detailRelease.base64Decoder(isiFile)
					 ].join('\n'),
    				language: programingLanguage,
          	theme : 'vs-dark'
    		});
    	});
		},
		changeLanguage: function(){
			var model = editor.getModel();
			monaco.editor.setModelLanguage(model,$("#programingLanguage").val());
		},
		base64Decoder: function(base64){
			return $.base64.decode(base64);
		},
		formNewFolder: function(){
			$.ajax({
        type:'POST',
        url: this.url+'&API=formNewFolder',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
							$("#modalFolder").remove();
            	$("#tempatModal").html(resp.content);
							$("#modalFolder").modal();
            }else{
              detailRelease.errorAlert(resp.err);
            }
          }
      });
		},
		formRenameFolder: function(namaFolder){
			$.ajax({
        type:'POST',
				data : {
							namaFolder : detailRelease.hexDecode(namaFolder)
						},
        url: this.url+'&API=formRenameFolder',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
							$("#modalFolder").remove();
            	$("#tempatModal").html(resp.content);
							$("#modalFolder").modal();
            }else{
              detailRelease.errorAlert(resp.err);
            }
          }
      });
		},
		saveNewDir: function(){
	      $.ajax({
	        type:'POST',
	        data : {
										location : $("#currentLocation").val(),
										namaFolder : $("#namaFolder").val(),
									},
	        url: this.url+'&API=saveNewDir',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
								$("#closeModal").click();
								detailRelease.refreshList();
	            }else{
	              detailRelease.errorAlert(resp.err);
	            }
	          }
	      });
			},
		saveRenameFolder: function(){
	      $.ajax({
	        type:'POST',
	        data : {
										location : $("#currentLocation").val(),
										namaFolder : $("#namaFolder").val(),
										hiddenNamaFolder : $("#hiddenNamaFolder").val(),
									},
	        url: this.url+'&API=saveRenameFolder',
	          success: function(data) {
	          var resp = eval('(' + data + ')');
	            if(resp.err==''){
								$("#closeModal").click();
								detailRelease.refreshList();
	            }else{
	              detailRelease.errorAlert(resp.err);
	            }
	          }
	      });
			},
			releaseChanged: function(){
		      $.ajax({
		        type:'POST',
		        data : $("#"+this.formName+"_release").serialize(),
		        url: this.url+'&API=releaseChanged',
		          success: function(data) {
		          var resp = eval('(' + data + ')');
		            if(resp.err==''){
		              $("#folderRelease").val(resp.content.folderRelease);
		            }else{
		              detailRelease.errorAlert(resp.err);
		            }
		          }
		      });
				},





});
