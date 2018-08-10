var refSession = new baseObject2({
  prefix: "refSession",
  url: "pages.php?page=refSession",
  formName: "refSessionForm",
  idFileCheck: 0,
  optionPushFile: 0,
  optionPushDataBase: 0,
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
    // $("#filterCari").val(a.value);
    var table = $("#dataServer").DataTable();
    table.search($(a).val()).draw();
  },
  refreshList: function() {
    $.ajax({
      type: "POST",
      data: {
        filterCari: $("#filterCari").val()
      },
      url: refSession.url + "&API=refreshList",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
          $("#refSessionForm").html(resp.content.tableContent);
        } else {
          refSession.errorAlert(resp.err);
        }
      }
    });
  },
  Baru: function() {
    $.ajax({
      type: "POST",
      data: $("#" + this.formName).serialize(),
      url: this.url + "&API=Baru",
      success: function(data) {
        var resp = eval("(" + data + ")");
        if (resp.err == "") {
          $("#modalForm").remove();
          $("#tempatModal").html(resp.content);
          $("#modalForm").modal();
          ("use strict");
          $("#kurunTanggal").daterangepicker({
            autoApply: true,
            locale: {
              format: "DD-MM-YYYY"
            }
          });
        } else {
          refSession.errorAlert(resp.err);
        }
      }
    });
  },
  Edit: function() {
    var errMsg = refSession.getJumlahChecked();
    urlEdit = this.url;
    if (errMsg == "") {
      $.ajax({
        type: "POST",
        data: $("#" + this.formName).serialize(),
        url: this.url + "&API=Edit",
        success: function(data) {
          var resp = eval("(" + data + ")");
          if (resp.err == "") {
            $("#modalForm").remove();
            $("#tempatModal").html(resp.content);
            $("#modalForm").modal();
            ("use strict");
            $("#kurunTanggal").daterangepicker({
              autoApply: true,
              locale: {
                format: "DD-MM-YYYY"
              }
            });
          } else {
            refSession.errorAlert(resp.err);
          }
        }
      });
    } else {
      refSession.errorAlert(errMsg);
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
            data: $("#" + refSession.formName).serialize(),
            url: refSession.url + "&API=Hapus",
            success: function(data) {
              var resp = eval("(" + data + ")");
              if (resp.err == "") {
                refSession.suksesAlert("Data Terhapus", refSession.homePage);
              } else {
                refSession.errorAlert(resp.err);
              }
            }
          });
        }
      );
    } else {
      refSession.errorAlert(errMsg);
    }
  },
  homePage: function() {
    window.location = refSession.url;
  },

  saveNew: function() {
    var me = this;
    swal(
      {
        title: "Simpan ?",
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
          data: $("#" + refSession.formName + "_input").serialize(),
          url: refSession.url + "&API=saveNew",
          success: function(data) {
            var resp = eval("(" + data + ")");
            if (resp.err == "") {
              refSession.suksesAlert("Data Tersimpan", refSession.homePage);
            } else {
              refSession.errorAlert(resp.err);
            }
          }
        });
      }
    );
  },
  saveEdit: function() {
    var me = this;
    swal(
      {
        title: "Simpan ?",
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
          data: $("#" + refSession.formName + "_input").serialize(),
          url: refSession.url + "&API=saveEdit",
          success: function(data) {
            var resp = eval("(" + data + ")");
            if (resp.err == "") {
              refSession.suksesAlert("Data Tersimpan", refSession.homePage);
            } else {
              refSession.errorAlert(resp.err);
            }
          }
        });
      }
    );
  },
  imageChanged: function() {
    var me = this;
    var filesSelected = document.getElementById("fileInputSession").files;
    if (filesSelected.length > 0) {
      var fileToLoad = filesSelected[0];
      var fileReader = new FileReader();
      fileReader.onload = function(fileLoadedEvent) {
        $("#thumbnailImages").attr("src", fileLoadedEvent.target.result);
        $("#gambarSession").val(fileLoadedEvent.target.result);
      };
      fileReader.readAsDataURL(fileToLoad);
    }
  }
});
