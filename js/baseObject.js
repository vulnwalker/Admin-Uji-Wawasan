baseObject2 = function(params_){
	this.params=params_;
	/*daftar params default */
	//this.elCurrPage = 'HalDefault';
	this.prefix = 'daftar';// 'pbb_penetapan_daftar';
	this.url = 'index.php?Op=9&Pg=sppt';
	this.formName = 'adminForm';
	this.filterCbxChecked = false;
	this.daftarPilih= new Array();
	this.withPilih = false;
	this.jmlPerHal = 25;
	this.rowID = 0;
	this.tampilFilterColapse = 0;
	this.tampilFilterColapseMin = 37;
	this.rowHead = 1;
	this.suksesAlert =function(pesan,action){
				swal({
					title: pesan,
					text: "",
					type: "success",
					showCancelButton: false,
					closeOnConfirm: false
				}, function () {
						if(action){
							action();
						}
						swal.close();
				});
		}
	this.errorAlert=function(pesan){
			swal(pesan, "", "warning");
	}
	this.hexDecode=function(hex) {
    var str = '';
    for (var i = 0; i < hex.length; i += 2) str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
    return str;
	}
	this.createDialog = function(){
		$('body').append("<div id='tempatModal'></div>");
	}
	this.deleteModal = function(){
			$("#modalForm").remove();
	},
	this.closeModal = function(){
			$("#closeModal").click();
	},
	this.initial = function(){
		for (var name in this.params) {
			eval( 'if(this.params.'+name+' != null) this.'+name+'= this.params.'+name+'; ');
		}
	}
	this.initial();
}
