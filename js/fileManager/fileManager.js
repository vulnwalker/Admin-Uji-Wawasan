var fileManager = new baseObject2({
	prefix : 'fileManager',
	url : 'pages.php?page=fileManager',
	formName : 'fileManagerForm',

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
							},
			url: fileManager.url+'&API=refreshList',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#fileManagerForm").html(resp.content.tableContent);
				}else{
					fileManager.errorAlert(resp.err);
				}
			}
		});
	},
	Baru: function(){
		window.location = this.url+"&action=new"+"&location="+$("#currentLocation").val();
	},
	Edit: function(){
	  var errMsg = fileManager.getJumlahChecked();
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
	          fileManager.errorAlert(resp.err);
	        }
	      }
	    });
	  }else{
	     fileManager.errorAlert(errMsg);
	  }
	},
	Release: function(){
		var errMsg = this.getJumlahChecked();
		if(errMsg == '' || errMsg=='Pilih hanya satu data'){
				$.ajax({
						type:'POST',
						data : $("#"+fileManager.formName).serialize(),
						url: fileManager.url+'&API=Release',
						success: function(data) {
							var resp = eval('(' + data + ')');
							if(resp.err==''){
								$("#modalRelease").remove();
	            	$("#tempatModal").html(resp.content);
								$("#modalRelease").modal();
							}else{
								fileManager.errorAlert(resp.err);
							}
						}
					});
			}else{
				fileManager.errorAlert(errMsg);
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
							data : $("#"+fileManager.formName).serialize(),
				      url: fileManager.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  fileManager.suksesAlert("Data Terhapus",fileManager.refreshList);
                }else{
                  fileManager.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      fileManager.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = fileManager.url;
	},
	afterSave: function(){
		window.location = fileManager.url+"&location="+$("#location").val();
	},
  saveNew: function(){
      $.ajax({
        type:'POST',
        data : $("#"+this.formName+"_new").serialize()+"&isiFile="+editor.getValue(),
        url: this.url+'&API=saveNew',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
              fileManager.suksesAlert("Data Tersimpan",fileManager.afterSave);
            }else{
              fileManager.errorAlert(resp.err);
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
              fileManager.suksesAlert("Data Tersimpan",fileManager.afterSave);
            }else{
              fileManager.errorAlert(resp.err);
            }
          }
      });
		},

  pushRelease: function(){
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
						$("#closeModal").click();
							$.ajax({
								type:'POST',
								data : $("#"+fileManager.formName+"_release").serialize(),
								url: fileManager.url+'&API=pushRelease',
									success: function(data) {
									var resp = eval('(' + data + ')');
										if(resp.err==''){
											fileManager.suksesAlert("Push Success",fileManager.refreshList);
										}else{
											fileManager.errorAlert(resp.err);
										}
									}
							});
			});

		},

		setCodeEditor: function(isiFile,programingLanguage){
			require.config({ paths: { 'vs': 'plugins/monaco-editor/min/vs' }});
    	require(['vs/editor/editor.main'], function() {
    		 editor = monaco.editor.create(document.getElementById('codeEditor'), {
					 value: [
						 fileManager.base64Decoder(isiFile)
					 ].join('\n'),
    				language: programingLanguage,

          	theme : 'vs-dark'
    		});
    	});
			// $("#codeEditor").attr("class","col-sm-12");
			// $(".monaco-editor").attr("class","monaco-editor vs-dark col-sm-12");
			// $(".monaco-editor").attr("style","height:800px;");
			// $(".overflow-guard").attr("class","overflow-guard col-sm-12");
			// $(".overflow-guard").attr("style","height:800px;");
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
              fileManager.errorAlert(resp.err);
            }
          }
      });
		},
		formRenameFolder: function(namaFolder){
			$.ajax({
        type:'POST',
				data : {
							namaFolder : fileManager.hexDecode(namaFolder)
						},
        url: this.url+'&API=formRenameFolder',
          success: function(data) {
          var resp = eval('(' + data + ')');
            if(resp.err==''){
							$("#modalFolder").remove();
            	$("#tempatModal").html(resp.content);
							$("#modalFolder").modal();
            }else{
              fileManager.errorAlert(resp.err);
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
								fileManager.refreshList();
	            }else{
	              fileManager.errorAlert(resp.err);
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
								fileManager.refreshList();
	            }else{
	              fileManager.errorAlert(resp.err);
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
		              fileManager.errorAlert(resp.err);
		            }
		          }
		      });
				},





});
