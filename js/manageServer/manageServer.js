var manageServer = new baseObject2({
	prefix : 'manageServer',
	url : 'pages.php?page=manageServer',
	formName : 'manageServerForm',

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

	loadSSHContent: function(idServer){
		$.ajax({
			type:'POST',
			data : {
								idServer : idServer,
							},
			url: manageServer.url+'&API=loadSSHContent',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#tabSSH").html(resp.content);
				}else{
					manageServer.errorAlert(resp.err);
				}
			}
		});
	},
	loadMysqlContent: function(idServer){
		$.ajax({
			type:'POST',
			data : {
								idServer : idServer,
							},
			url: manageServer.url+'&API=loadMysqlContent',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#tabDatabase").html(resp.content);
				}else{
					manageServer.errorAlert(resp.err);
				}
			}
		});
	},
	addUserSSH: function(){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
						},
			type:'POST',
			url: this.url+'&API=addUserSSH',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalUserSSH").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	addUserMysql: function(){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
						},
			type:'POST',
			url: this.url+'&API=addUserMysql',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalUserMysql").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	editUserSSH: function(usernameSSH){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
								usernameSSH : usernameSSH,
						},
			type:'POST',
			url: this.url+'&API=editUserSSH',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalUserSSH").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	editUserMysql: function(usernameMysql){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
								usernameMysql : usernameMysql,
						},
			type:'POST',
			url: this.url+'&API=editUserMysql',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalUserMysql").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	backupDatabase: function(databasename){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
								databasename : databasename,
						},
			type:'POST',
			url: this.url+'&API=backupDatabase',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalBackup").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	renameDatabase: function(databasename){
		$.ajax({
			data:	{
								idServer : $("#idServer").val(),
								databasename : databasename,
						},
			type:'POST',
			url: this.url+'&API=renameDatabase',
				success: function(data) {
				var resp = eval('(' + data + ')');
					if(resp.err==''){
						manageServer.deleteModal();
						$("#tempatModal").html(resp.content);
						$("#modalBackup").modal();
					}else{
						manageServer.errorAlert(resp.err);
					}
				}
		});
	},
	saveNewUserSSH: function(){
			$.ajax({
				type:'POST',
				data : {
							idServer : $("#idServer").val(),
							idSSH : $("#idSSH").val(),
							usernameSSH : $("#usernameSSH").val(),
							passwordSSH : $("#passwordSSH").val(),
							homeDirectory : $("#homeDirectory").val(),
					},
				url: this.url+'&API=saveNewUserSSH',
					success: function(data) {
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							$("#closeModal").click();
							manageServer.loadSSHContent($("#idServer").val());
						}else{
							manageServer.errorAlert(resp.err);
						}
					}
			});
		},
	saveNewUserMysql: function(){
			$.ajax({
				type:'POST',
				data : {
							idServer : $("#idServer").val(),
							usernameMysql : $("#usernameMysql").val(),
							passwordMysql : $("#passwordMysql").val(),
					},
				url: this.url+'&API=saveNewUserMysql',
					success: function(data) {
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							$("#closeModal").click();
							manageServer.loadMysqlContent($("#idServer").val());
						}else{
							manageServer.errorAlert(resp.err);
						}
					}
			});
		},
	diskChanged: function(){
			$.ajax({
				type:'POST',
				data : {
							idServer : $("#idServer").val(),
							idDisk : $("#idDisk").val(),
					},
				url: this.url+'&API=diskChanged',
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
							manageServer.errorAlert(resp.err);
						}
					}
			});
		},
	saveEditUser: function(usernameSSH){
			$.ajax({
				type:'POST',
				data : {
							idServer : $("#idServer").val(),
							idSSH : $("#idSSH").val(),
							usernameSSH : usernameSSH,
							passwordSSH : $("#passwordSSH").val(),
							homeDirectory : $("#homeDirectory").val(),
					},
				url: this.url+'&API=saveEditUser',
					success: function(data) {
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							$("#closeModal").click();
							manageServer.loadSSHContent($("#idServer").val());
						}else{
							manageServer.errorAlert(resp.err);
						}
					}
			});
		},
	saveBackUp: function(databaseName){
		swal({
				title: "Backup Data ?",
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
							data : $("#optionBackup").serialize()+"&idServer="+$("#idServer").val()+"&databaseName="+databaseName+"&idDisk="+$("#idDisk").val()+"&idRelease="+$("#idRelease").val(),
							url: manageServer.url+'&API=saveBackUp',
							timeout: 9000000000000,
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
										manageServer.suksesAlert("Backup Success");
									}else{
										manageServer.errorAlert(resp.err);
									}
								}
						});
		});
		},
	dropDatabase: function(databaseName){
		swal({
				title: "Drop Database ?",
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
							data : $("#optionBackup").serialize()+"&idServer="+$("#idServer").val()+"&databaseName="+databaseName,
							url: manageServer.url+'&API=dropDatabase',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
										manageServer.suksesAlert("Database Droped");
									}else{
										manageServer.errorAlert(resp.err);
									}
								}
						});
		});
		},
	saveEditUserMysql: function(usernameMysql){
			$.ajax({
				type:'POST',
				data : {
							idServer : $("#idServer").val(),
							usernameMysql : usernameMysql,
							passwordMysql : $("#passwordMysql").val(),
					},
				url: this.url+'&API=saveEditUserMysql',
					success: function(data) {
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							$("#closeModal").click();
							manageServer.loadMysqlContent($("#idServer").val());
						}else{
							manageServer.errorAlert(resp.err);
						}
					}
			});
		},
	deleteUserSSH: function(usernameSSH){
		swal({
					title: "Hapus User ?",
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
							data : {
										idServer : $("#idServer").val(),
										usernameSSH : usernameSSH,
								},
							url: manageServer.url+'&API=deleteUserSSH',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
										$("#closeModal").click();
										swal.close();
										manageServer.loadSSHContent($("#idServer").val());
									}else{
										manageServer.errorAlert(resp.err);
									}
								}
						});
				});

		},
	deleteUserMysql: function(usernameMysql){
		swal({
					title: "Hapus User ?",
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
							data : {
										idServer : $("#idServer").val(),
										usernameMysql : usernameMysql,
								},
							url: manageServer.url+'&API=deleteUserMysql',
								success: function(data) {
								var resp = eval('(' + data + ')');
									if(resp.err==''){
										$("#closeModal").click();
										swal.close();
										manageServer.loadMysqlContent($("#idServer").val());
									}else{
										manageServer.errorAlert(resp.err);
									}
								}
						});
				});

		},
	deleteModal: function(){
			$("#modalUserSSH").remove();
			$("#modalUserMysql").remove();
			$("#modalBackup").remove();
	},





});
