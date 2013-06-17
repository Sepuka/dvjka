




/*
     FILE ARCHIVED ON 9:55:43 апр 27, 2013 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 6:07:05 июн 17, 2013.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
var dvjk = {
	msg: function(msg, subclass, timeout) {
		alert(msg);
		return false;
	},
	emsg: function(msg, timeout) {
		return dvjk.msg(msg, 'error', timeout);
	},
	confirm: function(msg) {
		if(confirm(msg)) {
			return true;
		} else {
			return false;
		}
	},
	redirect: function(url) {
		try { window.location = url; }
		catch (e) { document.location = url; }
		// dvjk.msg('Если Вас автоматически не перебросит на другую страницу в течение 15 секунд - <a href="'+url+'">нажмите сюда</a>.', false, 120000);
		return false;
	},
	jok: function(d) {
		if(!d.status) {
			dvjk.emsg('Неверный ответ сервера');
			return false;
		}
		else if(d.status == 'e' || d.status == 'err' || d.status == 'error') {
			dvjk.emsg(d.data);
			return false;
		}
		else if(d.status == 'msg') {
			dvjk.msg(d.data);
			return false;
		}
		else if(d.status == 'redirect') {
			dvjk.redirect(d.data);
			return false;
		}
		else if(d.status == 'eval') {
			eval(d.data);
			return d;
		}
		else {
			return d;
		};
	},
	slider: function(el) {
		jQuery(el).slideToggle(200);
		return false;
	}

};

dvjk.start = {
	proc: false,
	login: function(form) {
		if(dvjk.start.proc) return false;
		var f = {};
		f.l = jQuery('#auth_l').val();
		if(!f.l) return dvjk.emsg('Введите QIWI Кошелек');
		f.l = f.l.replace(/[^0-9\+]/g, '');
		if(!f.l.match(/^\+(7|9955|922|370|996|380|994|374|371|998|77)([0-9]{5,})$/i)) return dvjk.emsg('Неверный формат QIWI Кошелька');
		f.p = jQuery('#auth_p').val();
		if(!f.p) return dvjk.emsg('Введите пароль');
		if(jQuery('#auth_s').prop('checked')) f.s = 'on';
		f.rh = dvjk_rh;
		dvjk.start.proc = true;
		jQuery.post('/auth/login', f, function(d){
			dvjk.jok(d);
			dvjk.start.proc = false;
		}, 'json');
		return false;
	},
	getpass: function() {
		if(dvjk.start.proc) return false;
		var n = jQuery('#auth_l').val();
		if(!n) return dvjk.emsg('Введите QIWI Кошелек');
		n = n.replace(/[^0-9\+]/g, '');
		if(!n.match(/^\+(7|9955|922|370|996|380|994|374|371|998|77)([0-9]{5,})$/i)) return dvjk.emsg('Неверный формат QIWI Кошелька');
		dvjk.start.proc = true;
		jQuery.post('/auth/getpass', {rh: dvjk_rh, n: n}, function(d){
			if(dvjk.jok(d)) {
				jQuery('#getpass_keybox').html('<b>'+d.data+'</b>');
			}
			dvjk.start.proc = false;
		}, 'json');
		return false;
	}
}

