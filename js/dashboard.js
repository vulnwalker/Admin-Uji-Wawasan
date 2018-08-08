var dashboard = new baseObject2({
  prefix: "dashboard",
  url: "pages.php?page=dashboard",
  formName: "dashboardForm",

  getJumlahChecked: function() {
    var jmldata = document.getElementById(this.prefix + "_jmlcek").value;
    for (var i = 0; i < jmldata; i++) {
      var box = document.getElementById(this.prefix + "_cb" + i);
      if (box.checked) {
        break;
      }
    }
    var err = "";
    if (jmldata == 0) {
      err = "Pilih data";
    } else if (jmldata > 1) {
      err = "Pilih hanya satu data";
    }
    return err;
  },
  checkSemua: function(
    jumlahData,
    fldName,
    elHeaderChecked,
    elJmlCek,
    fuckYeah
  ) {
    if (!fldName) {
      fldName = "cb";
    }
    if (!elHeaderChecked) {
      elHeaderChecked = "toggle";
    }
    var c = fuckYeah.checked;
    var n2 = 0;
    for (i = 0; i < jumlahData; i++) {
      cb = document.getElementById(fldName + i);
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
  thisChecked: function(idCheckbox, elJmlCek) {
    var c = document.getElementById(idCheckbox).checked;
    var jumlahCheck = parseInt($("#" + elJmlCek).val());
    if (c) {
      document.getElementById(elJmlCek).value = jumlahCheck + 1;
    } else {
      document.getElementById(elJmlCek).value = jumlahCheck - 1;
    }
  },
  formatCurrency: function(num) {
    num = num.toString().replace(/\$|\,/g, "");
    if (isNaN(num)) num = "0";
    sign = num == (num = Math.abs(num));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10) cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
      num =
        num.substring(0, num.length - (4 * i + 3)) +
        "." +
        num.substring(num.length - (4 * i + 3));
    return (sign ? "" : "-") + "" + num + "," + cents;
  },
  setValueFilter: function(a) {
    $("#filterCari").val(a.value);
  },
  refreshList: function() {
    $.ajax({
      type: "POST",
      data: {
        filterCari: $("#filterCari").val()
      },
      url: this.url + "&API=refreshList",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
          $("#dashboardForm").html(resp.content.tableContent);
        } else {
          dashboard.errorAlert(resp.err);
        }
      }
    });
  },
  Baru: function() {
    window.location = this.url + "&action=new";
  },
  Edit: function() {
    var errMsg = dashboard.getJumlahChecked();
    urlEdit = this.url;
    if (errMsg == "") {
      $.ajax({
        type: "POST",
        data: $("#" + this.formName).serialize(),
        url: this.url + "&API=Edit",
        success: function(data) {
          var resp = eval("(" + data + ")");
          if (resp.err == "") {
            window.location =
              urlEdit + "&action=edit&idEdit=" + resp.content.idEdit;
          } else {
            dashboard.errorAlert(resp.err);
          }
        }
      });
    } else {
      dashboard.errorAlert(errMsg);
    }
  },
  Hapus: function() {
    var errMsg = this.getJumlahChecked();
    if (errMsg == "" || errMsg == "Pilih hanya satu data") {
      swal(
        {
          title: "Hapus Data ?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
          closeOnConfirm: false
        },
        function() {
          $.ajax({
            type: "POST",
            data: $("#" + dashboard.formName).serialize(),
            url: dashboard.url + "&API=Hapus",
            success: function(data) {
              var resp = eval("(" + data + ")");
              if (resp.err == "") {
                dashboard.suksesAlert("Data Terhapus", dashboard.homePage);
              } else {
                dashboard.errorAlert(resp.err);
              }
            }
          });
        }
      );
    } else {
      dashboard.errorAlert(errMsg);
    }
  },
  homePage: function() {
    window.location = dashboard.url;
  },
  closeMassage: function() {
    swal.close();
  },
  saveNew: function() {
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
      type: "POST",
      data: $("#" + this.formName + "_new").serialize(),
      url: this.url + "&API=saveNew",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
          dashboard.suksesAlert("Data Tersimpan", dashboard.homePage);
        } else {
          dashboard.errorAlert(resp.err);
        }
      }
    });
    // });
  },
  rebootServer: function(id) {
    swal(
      {
        title: "Reboot Server ?",
        text: "",
        type: "info",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      },
      function() {
        $.ajax({
          type: "POST",
          data: { id: id },
          url: dashboard.url + "&API=rebootServer",
          success: function(data) {
            var resp = eval("(" + data + ")");
            if (resp.err == "") {
              dashboard.suksesAlert("Reboot Sukses", dashboard.homePage);
            } else {
              dashboard.errorAlert(resp.err);
            }
          }
        });
      }
    );
  },
  webStatusChanged: function(id, checked) {
    if (checked == true) {
      dashboard.lifeApache(id);
    } else {
      dashboard.killApache(id);
    }
  },
  serverStatusChanged: function(id, checked) {},
  lifeApache: function(id) {
    swal(
      {
        title: "Nyalakan Website ?",
        text: "",
        type: "info",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      },
      function() {
        $.ajax({
          type: "POST",
          data: { id: id },
          url: dashboard.url + "&API=lifeApache",
          success: function(data) {
            var resp = eval("(" + data + ")");
            if (resp.err == "") {
              swal.close();
              // dashboard.suksesAlert("Website dinyalakan",dashboard.homePage);
            } else {
              dashboard.errorAlert(resp.err);
            }
          }
        });
      }
    );
  },
  killApache: function(id) {
    swal(
      {
        title: "Matikan Website ?",
        text: "",
        type: "info",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      },
      function() {
        $.ajax({
          type: "POST",
          data: { id: id },
          url: dashboard.url + "&API=killApache",
          success: function(data) {
            var resp = eval("(" + data + ")");
            if (resp.err == "") {
              swal.close();
            } else {
              dashboard.errorAlert(resp.err);
            }
          }
        });
      }
    );
  },
  dashboard: function(id) {
    window.location = "pages.php?page=dashboard&id=" + id;
  },
  getInfoServer: function(idServer) {
		$("#tempatLoading").html(this.loadingPage("Mengambil Info Server"));
		$("#modalLoading").modal({backdrop: 'static', keyboard: false});
    $.ajax({
      type: "POST",
      data: {
        idServer: idServer,
				optionStatistik :$("#optionStatistik"+idServer).val()
      },
      error: function (request, status, error) {
        dashboard.getInfoServer(idServer);
      },
      url: this.url + "&API=getInfoServer",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
					$("#closeModal").click();
          $("#panelServer" + idServer).html(resp.cek);
          dashboard.exec_body_scripts(resp.cek);
        } else {
          dashboard.errorAlert(resp.err);
        }
      }
    });
  },
  graphServerChanged: function(idServer) {
		$("#tempatLoading").html(this.loadingPage("Mengambil Info Server"));
		$("#modalLoading").modal({backdrop: 'static', keyboard: false});
    $.ajax({
      type: "POST",
      data: {
        idServer: idServer,
				optionStatistik :$("#optionStatistik"+idServer).val(),
				dateRange :$("#kurunWaktu"+idServer).val(),
        checkBoxMemoryUsage : $("#checkBoxMemoryUsage"+idServer+":checked").val(),
        checkBoxCpuUsage : $("#checkBoxCpuUsage"+idServer+":checked").val(),
        checkBoxDiskUsage : $("#checkBoxDiskUsage"+idServer+":checked").val(),
      },
      error: function (request, status, error) {
        dashboard.getInfoServer(idServer);
      },
      url: this.url + "&API=graphServerChanged",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
					$("#closeModal").click();
          $("#graphServer" + idServer).html(resp.cek);
          dashboard.exec_body_scripts(resp.cek);
        } else {
          dashboard.errorAlert(resp.err);
        }
      }
    });
  },
  optionsGraphChanged: function(idServer) {

    $.ajax({
      type: "POST",
      data: {
        idServer: idServer,
				optionStatistik :$("#optionStatistik"+idServer).val()
      },
      error: function (request, status, error) {
        dashboard.getInfoServer(idServer);
      },
      url: this.url + "&API=optionsGraphChanged",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
          $("#spanKurunWaktu" + idServer).html(resp.content);
          dashboard.exec_body_scripts(resp.content);
        } else {
          dashboard.errorAlert(resp.err);
        }
      }
    });
  },
  showGraph: function(idServer) {
      dashboard.graphServerChanged(idServer);
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
	loadingPage:function(text){
			return "<div class='modal fade bs-example-modal-sm' id='modalLoading' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'><div class='modal-dialog modal-sm'><div class='modal-content'><div class='modal-body'><div class='form-group'><div class='row'><label class='col-sm-12control-label' style='margin-top:6px;'>"+text+"</label> </div> </div> </div> <div class='modal-footer' style='display:none;'> <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button> </div> </div> </div> </div>";
	},
});
