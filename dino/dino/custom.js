	/*******************************************************
	 * custom JavaScript file using jQuery and written in  *
	 * page order, which means, as pages can be accessed.  *
	 *******************************************************/

/* ----- OnLoad / Document Ready - Stuff to always load ----- */
$(function () {
	var currentPageURL = window.location.toString().split('#')[0].split('/');
	var currentPageHref = currentPageURL[(currentPageURL.length-1)].split('&')[0]; // get the "?p=..." stuff in the URL
// --- Sidebar Menu active control --- //
	var activeMenu = $('#sidebar-menu').find('a[href="./'+ currentPageHref +'"]').parent('li');
	if ($('body').hasClass('nav-md')) activeMenu.parent('ul').slideDown();
	if (activeMenu.parent('ul').parent('li').is('.parent'))
		activeMenu.parent('ul').parent('li').addClass('current-page active');
	activeMenu.addClass('current-page');
// --- AJaX default error Set --- //
	$(document).ajaxError(function () { alertPNotify('alert-danger', $('script[src*=custom]').attr('data-error')); });
// --- Sidebar Menu active function --- //
	$('#sidebar-menu').find('li').on('click', function(ev) {
		var link = $('a', this).attr('href');
		if (link) ev.stopPropagation();
		else {
			if ($(this).is('.active')) {
				$(this).removeClass('active');
				$('ul', this).slideUp();
			} else {
				$('#sidebar-menu').find('li').removeClass('active');
				$('#sidebar-menu').find('li ul').slideUp();
				$(this).addClass('active');
				$('ul', this).slideDown();
			}
		}
	});
// --- Menu toggle function (with _SESSION) (WW>991 ? bootstrap @media md : sm|xs) --- //
	ajaxFolder = $('script[src*=custom]').attr('data-ajax-folder');
	$('#menu_toggle').on('click', function() {
		if ($(window).width() > 991) {
			$('body').toggleClass('nav-md').toggleClass('nav-sm');
			var post = 'ajax=1&lm=' + new String(($('body').is('.nav-md'))?('0'):('1'));
			$.ajax({ url: ajaxFolder +'/session_set.php', type: 'POST', data: post });
			if ($('body').is('.nav-sm')) $('#sidebar-menu li').removeClass('active').find('ul').slideUp();
		} else {
			if ($('body').is('.nav-sm')) $('body').toggleClass('nav-md').toggleClass('nav-sm');
			$('.container.body .left_col').toggleClass("small-push");
			$('.container.body .right_col').toggleClass("small-push");
		}
	// Fix table header on resize --- //
		if (typeof listTable != 'undefined') {
			$(".left_col").one('webkitTransitionEnd mozTransitionEnd MSTransitionEnd oTransitionEnd transitionend', function() {
				listTable.columns.adjust();
			});
		}
	});
// --- Dropdown slide effect --- //
	$('.dropdown').on('show.bs.dropdown', function(e) { $(this).find('.dropdown-menu').slideDown(200); });
	$('.dropdown').on('hide.bs.dropdown', function(e) { $(this).find('.dropdown-menu').slideUp(200); });
// --- Language set function (with _SESSION) --- //
	$('#langset a').on('click', function() {
		$('.dropdown .dropdown-menu').hide(0);
		var post = 'ajax=1&lg='+ $(this).attr('label');
		$.ajax({
			url: ajaxFolder +'/session_set.php',
			type: 'POST',
			data: post,
			success: function () { window.location = currentPageHref; }
		});
	});
// --- Right column height  --- //
	$(".right_col").css("min-height", $(window).height());
	$(window).resize(function () {
		$(".right_col").css("min-height", $(window).height());
		if (typeof listTable != 'undefined') listTable.columns.adjust();
	});
// --- Fix table header on resize --- //
	$(window).on("orientationchange", function () {
		if (typeof listTable != 'undefined') {
			$(".left_col").one('webkitTransitionEnd mozTransitionEnd MSTransitionEnd oTransitionEnd transitionend', function() {
				listTable.columns.adjust();
			});
		}
	});
// --- Fix Multiple Modal Windows --- //
	$(document).on('show.bs.modal', '.modal', function () {
	   var zIndex = 1040 + (10 * $('.modal:visible').length);
	   $(this).css('z-index', zIndex);
		if (!$.contains($(".container.body").parent(), $(this))) $(this).detach().appendTo($(".container.body").parent());
		$(".container.body").addClass('blured'); // --- Add some blur effect
	   setTimeout(function() { $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'); }, 0);
	});
	$(document).on('hidden.bs.modal', '.modal', function () { $('.modal:visible').length && $(document.body).addClass('modal-open'); });
	$(document).on('hide.bs.modal', '.modal', function () { $('.modal:visible').length == 1 && $(".container.body").removeClass('blured'); });
// --- Extend Number Object with PAD function --- //
	Number.prototype.pad = function(size) {
		var s = String(this);
		while (s.length < (size || 2)) { s = "0" + s; }
		return s;
	}
// --- Extend Contains Expression with Case Insensitive function --- //
	$.expr[':'].icontains = function (obj, index, meta, stack) {
		return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;
	};
});

/* ----- Define PNotify Alert Function ----- */
function alertPNotify (type, message, customDelay, customStack) {
	var type = (typeof type == 'undefined') ? ('alert-info') : (type);
	var message = (typeof message == 'undefined') ? (' -- ') : (message);
	var customDelay = (typeof customDelay == 'undefined') ? (2500) : (parseInt(customDelay));
	var customStack = (typeof customStack != 'object') ? ($("div.row")) : (customStack);
	var rowStack = { dir1: 'down', dir2: 'left', push: 'top', context: customStack };
	new PNotify({	delay: customDelay,
						stack: rowStack,
						addclass: type,
						buttons: { closer_hover: false, sticker_hover: false },
						animate: { animate: true, in_class: 'slideInDown', out_class: 'slideOutUp' },
						text: message });
	$(".ui-pnotify").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
		$(this).removeClass("slideInDown"); });
}

/* ----- Define Timeout Function Checker ----- */
function checkTimeout () {
	$.ajax({
		url: ajaxFolder +'/check_timeout.php',
		type: 'POST',
		data: 'ajax=1',
		success: function (response) {
			var responseTime = (isNaN(parseInt(response))) ? (0) : (parseInt(response));
			var tempo = new Date(responseTime * 1000);
			if (tempo.getTime() == 0) window.location = "./?p=off";
			else {
				var minutes = (tempo.getMinutes() < 10) ? ("0"+ tempo.getMinutes()) : (tempo.getMinutes());
				var seconds = (tempo.getSeconds() < 10) ? ("0"+ tempo.getSeconds()) : (tempo.getSeconds());
				$("#timeout_counter").html(minutes +":"+ seconds);
				$("#timeout_counter").attr("value", (tempo.getTime() / 1000));
			}
		}
	});
}

/* ----- Define AJaX request for List Users ----- */
function list_doit (typ, sch) {
	if (typeof typ == 'undefined') var typ = '';
	if (typeof sch == 'undefined') var sch = '';
	$.ajax({
		url: ajaxFolder+'/lst_search.php',
		type: 'POST',
		data: 'ajax=1&type='+ typ +'&search='+ sch,
		success: function (htrs) {
			if (htrs[0]!='[') $("#pnsc").html(htrs);
			else {
				$("#pnsc").html('');
				var rows_content = JSON.parse(htrs);
				$("#otpt tbody").html("");
				$("#total_result").html(rows_content.length.pad());
				for (var i=0; i<rows_content.length; i++) {
					$("#otpt tbody").append("<tr>");
					for (var j=0; j<rows_content[i].length; j++) $("#otpt tbody tr:last").append("<td>"+ rows_content[i][j] +"</td>");
				}
				$("#otpt tbody tr:has(a.dsbld) td").addClass('dsbld');
			}
		}
	});
}

/* ----- Define AJaX modal for List Users ----- */
function list_details (usid) {
	$(document).focus();
	$.ajaxSetup({ global: false });
	$.ajax({
		url: ajaxFolder+'/lst_search.php',
		type: 'POST',
		data: 'ajax=1&type='+ $('#type').val() +'&usid='+ usid,
		success: function (response) {
			$('#modal_details .modal-body').html(response);
			$('#modal_details #details_ticket').on("click", function () { window.location = './?p=33&cid='+ usid; });
			$("#modal_details").modal("show");
			$('#modal_details .modal-body .selectpicker').selectpicker();
		},
		complete: function () {
			$.ajaxSetup({ global: true });
		}
	});
}

/* ----- Define Modal Details Edit Button Function ----- */
function list_detailsEdit (element) {
	$("#modal_details .modal-body>div>div>span").toggleClass("fadeIn animated");
	$("#modal_details .modal-footer").toggleClass("edit").find(".form-edit").toggleClass("fadeIn animated");
	$("#modal_details .modal-body").toggleClass("edit").find(".form-edit").toggleClass("fadeIn animated");
	$(element).toggleClass("active");
}

/* ----- Update user data from modal list details ----- */
function list_detailsEditSend () {
	// aqui vai o codigo em ajax para atualizar os dados do cliente
	var hasChanged = false;
	var chenge[];
	$("#modal_details .modal-body input").each(function (index, element) {
		if ($(element).prop('defaultValue') != $(element).val()) {
			hasChanged = true
			chenge[]=$(element).val();
		};
	});
	$("#modal_details .modal-body select").each(function (index, element) {
		$(element).find('option').each(function (idx, elm) {
			if ($(elm).prop('defaultSelected') && !$(elm).prop('selected')) hasChanged = true;
		});
	});
	if (hasChanged) alert('Ainda nao implementado');
}

/* ----- Form Send Equipment Authentication Information ----- */
function form_seai () {
	if (isNaN($("#port").val())) return form_alert('#port');
	if ($("#usrnm").val().length < 4) return form_alert('#usrnm');
	if ($("#pass").val().length < 4) return form_alert('#pass');
	var psit = 'ajax=1&usnm='+ $("#usrnm").val() +'&pass='+ $("#pass").val();
	psit += '&srvc='+ $("#srvc").val() +'&port='+ $("#port").val();
	$.ajax({url: ajaxFolder +'/reg_info.php',
					type: 'POST',
					data: psit,
					success: function (htrs) { $('.x_content').html(htrs); }
	});
}

/* ----- Load Form to Register ----- */
function form_load (skip) {
	$.ajax({url: ajaxFolder +'/reg_form.php',
					type: 'POST',
					data: 'ajax=1&skip='+skip,
					success: function (htrs) { $('.x_content').html(htrs); }
	});
}

/* ----- Form wizard navigation ----- */
function form_goto (obj) {
	var actl = parseInt($(".wizard_steps .selected").attr('href').toString().split('#')[1]);
	if (obj.tagName.toString().toLowerCase()=='a')
		var dest = parseInt(obj.getAttribute('href').toString().split('#')[1]);
	else var dest = ($(obj).hasClass('btn-info')) ? (actl - 1) : (actl + 1);
	if (dest == actl) return false;
	if ((dest-actl) > 1 && !$(".wizard_steps a[href='#"+dest+"']").hasClass("done")) return false;
	if (dest > actl && !form_submit(obj)) return false;
	form_wizard(actl, dest);
	return true;
}

/* ----- Form wizard pagination ----- */
function form_wizard (actl, dest) {
	$("#"+actl).hide("slide");
	$(".wizard_steps .selected").removeClass("selected").addClass("done");
	if ($("#pass").attr('type') != 'password') $("#btnad").click();
	$("#"+dest).show("slide");
	$(".wizard_steps a[href='#"+dest+"']").removeClass("done").removeClass("disabled").addClass("selected");
}

/* ----- Form Submit Function ----- */
function form_submit (obj) {
	var ojtn = obj.tagName.toString().toLowerCase();
	var actl = parseInt($(".wizard_steps .selected").attr('href').toString().split('#')[1]);
	switch(actl) {
		case 1:
			if (!$("#maca").val().match(/(([\da-f]{2}\:){5}[\da-f]{2})/i)) return form_alert('#maca');
			if ($("#name").val().split(" ").length < 2 || $("#name").val().length < 6) return form_alert('#name');
			if ($("#pass").val().length < 6) return form_alert('#pass');
			if (ojtn == 'form') {
				form_wizard(1, 2);
				return false;
			}
		break;
		case 2:
			var ipts = $("#2 input[data-verify]");
			for (var i=0; i<ipts.length; i++) {
				var rgxt = new RegExp(ipts[i].getAttribute('data-verify'));
				if (!ipts[i].value.match(rgxt)) return form_alert('#'+ipts[i].id);
			} var ipts = $("#2 input").filter(function() { return !this.value; });
			if (ipts.length) {
				$("#modal_confirm").modal("toggle");
				return false;
			} else {
				if (ojtn == 'form') {
					form_wizard(2, 3);
					return false;
				}
			}
		break;
		default:
			if (!$("#cnfr").is(":checked")) return form_alert('#cnfr');
			$("#maca").attr('disabled', false);
			$("#conn").attr('disabled', false);
			$("#usnm").attr('disabled', false);
		break;
	} return true;
}

/* ----- Form Alert Function ----- */
function form_alert (obj_id) {
	var pnsc = { dir1: 'down', dir2: 'left', push: 'top', context: $("#pnsc") };
	var opts = { type: 'error', stack: pnsc, addclass: 'slideInUp animated', nonblock: { nonblock: true, nonblock_opacity: 0.3 }};
	opts.text = $(obj_id).attr("data-error");
	new PNotify(opts);
	$(".ui-pnotify").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
		$(this).removeClass("slideInUp animated"); });
	$(obj_id).addClass("shake animated").focus();
	$(obj_id).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
		$(this).removeClass("shake animated"); });
	return false;
}

/* ----- Form generate username ----- */
function form_tip (prsn) {
	if (prsn.length<4) return false;
	var pnms = prsn.split(' ');
	var prtt = pnms[0].toLowerCase()+'.'+pnms[(pnms.length-1)].toLowerCase()+'@';
	prtt += document.domain.replace(/^www\./,''); // what about another subdomain ???
	var cnt = 'çÇàèìòùâêîôûäëïöüáéíóúãõÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÁÉÍÓÚÃÕ';
	var ctg = 'cCaeiouaeiouaeiouaeiouaoAEIOUAEIOUAEIOUAEIOUAO';
	for (var i=0; i<cnt.length; i++) prtt = prtt.replace(cnt[i], ctg[i]);
	return prtt;
}

/* ----- Add ticket tag and tooltips everytime the calaendar is refreshed ----- */
function tools_ticketCalendar () {
	$('.form-ticket .tooltip[role="tooltip"]').remove(); // --- Fix Fanthom Tooltips
	for (var k in ticketCalendar) {
		var ticketYear = k.split('-')[0];
		var ticketMonth = parseInt(k.split('-')[1]) - 1;
		var ticketDay = k.split('-')[2];
		var ticketSelector = ".xdsoft_calendar .xdsoft_date[data-date="+ ticketDay +"][data-month=";
		ticketSelector += ticketMonth +"][data-year="+ ticketYear +"]";
		if($(ticketSelector).length) $(ticketSelector).addClass("has-tickets"); // --- Add date tag
	} // --- Get calendar selected date
	var ticketsCurrentYear = $(".xdsoft_calendar .xdsoft_date.xdsoft_current").attr("data-year");
	var ticketsCurrentMonth = parseInt($(".xdsoft_calendar .xdsoft_date.xdsoft_current").attr("data-month")) + 1;
	var ticketsCurrentDay = $(".xdsoft_calendar .xdsoft_date.xdsoft_current").attr("data-date");
	var ticketsCurrentDate = ticketsCurrentYear +'-'+ ticketsCurrentMonth +'-'+ ticketsCurrentDay;
	if (typeof ticketCalendar[ticketsCurrentDate] == 'object') {
		for (var k in ticketCalendar[ticketsCurrentDate]) {
			var ticketHour = parseInt(k.split('-')[0]);
			var ticketMinutes = parseInt(k.split('-')[1]);
			var ticketSelector = ".xdsoft_timepicker .xdsoft_time[data-hour="+ ticketHour +"][data-minute="+ ticketMinutes +"]";
			if($(ticketSelector).length) $(ticketSelector).addClass("has-tickets").attr("title", ticketCalendar[ticketsCurrentDate][k]);
		} $(".xdsoft_timepicker .xdsoft_time.has-tickets").tooltip({ container: ".form-ticket", html: true }); // --- Add hour tag with HTML tooltip
	}
}

/* ----- Edit Ticket Messages and send to the Database ----- */
function tools_ticketEdit (ticketID) {
	if (typeof ticketID == 'undefined') return false;
	if (!$(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]").length) return false;
	if ($(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]>textarea").length) {
		var initialString = $(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]>textarea").attr("data-initial");
		$(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]").html(initialString);
		return false;
	} $(".list-group-item[data-id] .list-group-item-text>span[data-message]>textarea").each(function (index, element) {
		var initialString = $(element).attr("data-initial");
		$(element).parent("span").html(initialString);
	});
	var ticketMessageObject = $(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]");
	var ticketMessageString = $(ticketMessageObject).html();
	$(ticketMessageObject).prev("span").css("display", "inline-block");
	var ticketMessageSubtractWidth = $(ticketMessageObject).prev("span").width() + 4;
	if (ticketMessageSubtractWidth < 70) ticketMessageSubtractWidth = 70; // Minimum width to fit the Save button
	$(ticketMessageObject).prev("span").css("display", "initial").css("vertical-align", "top");
	$(ticketMessageObject).html($("<textarea>", {"class": "form-control", "data-initial": ticketMessageString}));
	$(ticketMessageObject).find("textarea").css("width", "calc(100% - "+ ticketMessageSubtractWidth +"px)");
	$(ticketMessageObject).find("textarea").css("resize", "none").css("display", "inline-block").html(ticketMessageString).focus();
	$(ticketMessageObject).append($("<button>", {"class": "btn btn-info ticket-edit"}));
	$(ticketMessageObject).find("button").html($(".form-ticket button[type='submit']:last").html());
	$(ticketMessageObject).find("button").on("click", function () {
		if ($(ticketMessageObject).find("textarea").val() == $(ticketMessageObject).find("textarea").attr("data-initial")) {
			$(ticketMessageObject).html($(ticketMessageObject).find("textarea").attr("data-initial"));
		} else {
			$.ajax({
				url: ajaxFolder +'/tools_ticket_edit.php',
				type: 'POST',
				data: 'ajax=1&id='+ ticketID +'&message='+ $(ticketMessageObject).find("textarea").val(),
				success: function (response) { $(ticketMessageObject).html(response); }
			});
		}
	});
}

/* ----- Delete Ticket Messages and sync to the Database ----- */
function tools_ticketDelete (ticketID, confirmation) {
	if (typeof ticketID == 'undefined') return false;
	if (!$(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]").length) return false;
	$(".list-group-item[data-id] .list-group-item-text>span[data-message]>textarea").each(function (index, element) {
		var initialString = $(element).attr("data-initial");
		$(element).parent("span").html(initialString);
	});
	if (typeof confirmation == 'undefined') confirmation = false;
	if (confirmation) {
		$.ajax({
			url: ajaxFolder +'/tools_ticket_edit.php',
			type: 'POST',
			data: 'ajax=1&id='+ ticketID +'&delete=true',
			success: function (response) {
				if (response == 'DELETED') {
					$(".list-group-item[data-id="+ ticketID +"]").remove();
					if (!$(".list-group-item").length) $(".x_panel>.x_title>button").click();
				} else alertPNotify('alert-danger', response);
			}
		});
	} else {
		var ticketMessageString = $(".list-group-item[data-id="+ ticketID +"] .list-group-item-text>span[data-message]").html();
		$("#modal_ticket_message_delete .modal-body p>span").html(ticketMessageString);
		$("#modal_ticket_message_delete .modal-body input").val(ticketID);
		$("#modal_ticket_message_delete").modal("show");
	}
}

/* ----- Plans Get ----- */
function plans_get (iddr) {
	if (plns == undefined) return false;
	if (plns[iddr] == undefined) return false;
	else var pldt = JSON.parse(plns[iddr].value);
	var psti = '&id='+ pldt['id'] +'&plan='+ pldt['plan'] +'&type='+ pldt['type'];
	psti += '&upld='+ pldt['upld'] +'&down='+ pldt['down'] +'&minm='+ pldt['minm'];
	$.ajax({url: ajaxFolder +'/pln_form.php',
					type: 'POST',
					data: 'ajax=1'+ psti,
					success: function (htrs) {
						$('.x_content').append(htrs);
						$("#type_"+ pldt['id']).selectpicker();
						plans_get(++iddr);
					}
				});
}

/* ----- Define AJaX modal for Plans Add ----- */
function plans_add () {
	$.ajax({
		url: ajaxFolder+'/pln_form.php',
		type: 'POST',
		data: 'ajax=1&id=0&plan=&type=&upld=&down=&minm=',
		success: function (htrs) {
			$('#modal_plnadd').html(htrs);
			$("#type_0").selectpicker();
			$("#modal_plnadd").modal("toggle");
			$("#plan_0").focus();
		}
	});
}

/* ----- Settings Get DRACs ----- */
function set_dracGet (iddr) {
	if (drcs == undefined) return false;
	if (drcs[iddr] != undefined) var drdt = JSON.parse(drcs[iddr].value);
	else var drdt = { 'id': 0, 'conf': '', 'data': '' };
	$.ajax({url: ajaxFolder +'/set_drac-get.php',
					type: 'POST',
					data: 'ajax=1'+'&id='+ drdt['id'] +'&conf='+ drdt['conf'] +'&data='+ drdt['data'],
					success: function (htrs) {
						$('#tab_installation').append(htrs);
						$("#srvc_"+ drdt['id']).selectpicker();
						if (drdt['id'] != 0) set_dracGet(++iddr);
					}
				});
}

/* ----- Settings Get MNICs ----- */
function set_mnicGet (iddr) {
	if (ffcs == undefined) return false;
	if (ffcs[iddr] != undefined) var ffdt = JSON.parse(ffcs[iddr].value);
	else var ffdt = { 'id': 0, 'conf': '', 'data': '' };
	$.ajax({url: ajaxFolder +'/set_mnic-get.php',
					type: 'POST',
					data: 'ajax=1'+'&id='+ ffdt['id'] +'&conf='+ ffdt['conf'] +'&data='+ ffdt['data'],
					success: function (htrs) {
						$('#tab_formfields').append(htrs);
						if (ffdt['id'] != 0) set_mnicGet(++iddr);
					}
				});
}
