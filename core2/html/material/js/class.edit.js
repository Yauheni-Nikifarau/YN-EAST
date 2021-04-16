

function lastFocus(f){
	f.elements[f.elements.length - 1].focus();
}

//var isIE = window.navigator.userAgent.indexOf("MSIE")>-1;

var edit = {
	ev: {},
	xfiles: {},
	dateBlur : function(id) {
		var year = document.getElementById(id + '_year').value;
		var month = document.getElementById(id + '_month').value;
		var day = document.getElementById(id + '_day').value;
		document.getElementById('date_' + id).value = year + '-' + month + '-' + day;
		if (document.getElementById(id + '_hour')) {
			var h = document.getElementById(id + '_hour').value;
			var m = document.getElementById(id + '_min').value;
			if (!h) {
				if (m) {
					h = '00';
					document.getElementById(id + '_hour').value = h;
				}
			}
			else if (!m) {
				m = '00';
			}
			if (h && m) {
				document.getElementById('date_' + id).value += ' ' + h + ':' + m;
			}
		}
	},
	radioClick : function(o) {
		o.blur();
		o.focus();
	},
	dateKeyup : function (id, obj) {
		if (obj.id == id + '_day' && Number(obj.value) > 31) {
			obj.value = 31;
			obj.focus();
		}
		if (obj.id == id + '_month' && Number(obj.value) > 12) {
			obj.value = 12;
			obj.focus();
		}
		this.dateBlur(id);
	},
	timeKeyup : function (id, obj) {
		if (obj.id == id + '_hour' && Number(obj.value) > 23) {
			obj.value = 23;
			obj.focus();
		}
		if (obj.id == id + '_min' && Number(obj.value) > 59) {
			obj.value = 59;
			obj.focus();
		}
		this.dateBlur(id);
	},
	onsubmit: function (obj) {
		lastFocus(obj);
		if (typeof PrepareSave == 'function') {
			PrepareSave();
		}
	},
	showMark : function(obj) {
		$(obj).find('.helpMark').show();
	},
	hideMark : function(obj) {
		$(obj).find('.helpMark').hide();
	},
	create_date: function (cal) {
		var t = $('#date_' + cal).val().substr(10);
		var opt = {
			firstDay: 1,
			currentText: 'Сегодня',
			dateFormat: 'yy-mm-dd',
			defaultDate: t,
			buttonImage: 'core2/html/' + coreTheme + '/img/calendar.png',
			buttonImageOnly: true,
			showOn: "button",
			onSelect: function (dateText, inst) {
				$('#date_' + cal).val(dateText + t);
				$('#' + cal + '_day').val(dateText.substr(8, 2));
				$('#' + cal + '_month').val(dateText.substr(5, 2));
				$('#' + cal + '_year').val(dateText.substr(0, 4));
				$('#cal' + cal).datepicker('destroy');
			},
			beforeShow: function (event, ui) {
				setTimeout(function () {
					ui.dpDiv.css({ 'margin-top': '20px', 'margin-left': '-100px'});
				}, 5);
			}
		};
		if (this.ev[cal]) {
			opt['beforeShowDay'] = function (day) {
				var s = "";
				var t = "";
				var v = day.valueOf();
				if (edit.ev[cal][v]) {
					if (edit.ev[cal][v][0]["title"]) {
						t = edit.ev[cal][v][0]["title"];
					}
					if (edit.ev[cal][v][0]["disabled"]) {
						s = "ui-datepicker-unselectable ui-state-disabled";
					}
					if (edit.ev[cal][v][0]["cssclass"]) {
						s += " " + edit.ev[cal][v][0]["cssclass"];
					}
				}
				return [true, s, t];
			}
		}
		$('#date_' + cal).datepicker(opt);
	},
	create_datetime: function (cal) {

			var h = $('#date_' + cal).val().substr(11, 2);
			if (!h) h = '00';
			var m = $('#date_' + cal).val().substr(14, 2);
			if (!m) m = '00';
            var opt = {
                firstDay: 1,
                currentText: 'Сегодня',
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm',
                defaultDate: $('#date_' + cal).val().substr(0, 10),
                hour: h,
                minute: m,
                buttonImage: 'core2/html/' + coreTheme + '/img/calendar.png',
                buttonImageOnly: true,
                showOn: "button",
                onSelect: function (dateText, inst) {
                    //$('#date_' + cal).val(dateText);
                    $('#' + cal + '_day').val(dateText.substr(8, 2));
                    $('#' + cal + '_month').val(dateText.substr(5, 2));
                    $('#' + cal + '_year').val(dateText.substr(0, 4));
                    $('#' + cal + '_hour').val(dateText.substr(11, 2));
                    $('#' + cal + '_min').val(dateText.substr(14, 2));

                },
                beforeShow: function (event, ui) {
                    setTimeout(function () {
                        ui.dpDiv.css({ 'margin-top': '20px', 'margin-left': '-180px'});
                    }, 5);
                }
            };
            if (this.ev[cal]) {
                opt['beforeShowDay'] = function (day) {
                    var s = "";
                    var t = "";
                    var v = day.valueOf();
                    if (block.ev[cal][v]) {
                        if (block.ev[cal][v][0]["title"]) {
                            t = block.ev[cal][v][0]["title"];
                        }
                        if (block.ev[cal][v][0]["disabled"]) {
                            s = "ui-datepicker-unselectable ui-state-disabled";
                        }
                        if (block.ev[cal][v][0]["cssclass"]) {
                            s += " " + block.ev[cal][v][0]["cssclass"];
                        }
                    }
                    return [true, s, t];
                }
            }
			//$('#cal' + cal).datetimepicker(opt);
			$('#date_' + cal).datetimepicker(opt);
	},
	docSize : function (){
		return [
		document.body.scrollWidth > document.body.offsetWidth ? 
			document.body.scrollWidth : document.body.offsetWidth,
		document.body.scrollHeight > document.body.offsetHeight ? 
			document.body.scrollHeight : document.body.offsetHeight
		];
	},
	getClientSize : function (){
		if(document.compatMode=='CSS1Compat')
			return [document.documentElement.clientWidth, document.documentElement.clientHeight];
		else
			return [document.body.clientWidth, document.body.clientHeight];
	},
	getDocumentScroll : function(){
		return [
		self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) 
			|| (document.body && document.body.scrollLeft),
		self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) 
			|| (document.body && document.body.scrollTop)
		];
	},
	getCenter : function (){
		var sizes = this.getClientSize();
		var scrl  = this.getDocumentScroll();
		return [parseInt(sizes[0]/2)+scrl[0], parseInt(sizes[1]/2)+scrl[1]];
	},
	
	changePass : function(id) {
		var obj = document.getElementById(id);
		var obj2 = document.getElementById(id + '2');
		if (obj.disabled) {
			obj.disabled = false;
			obj.value='';
			obj2.disabled=false;
			obj2.value='';
			obj2.nextSibling.value = 'отмена';
		} else {
			obj.disabled = true;
			obj.value='******';
			obj2.disabled = true;
			obj2.value='******';
			obj2.nextSibling.value = 'изменить';
		}
	},
	
	displayError: function(id, text) {
		var obj = document.getElementById('main_' + id + '_error');
		if (obj) {
			obj.innerHTML = text;
			obj.style.display = 'block';
			//for(var i = 0; i < 2; i++) {
			//	$(obj).fadeTo(100, 0.4).fadeTo(100, 1);
			//}
		}
	},
	changeButtonSwitch: function(obj) {
		var i = $(obj).find('img');
		if (document.getElementById(obj.id + 'hid').value == 'Y') {
			document.getElementById(obj.id + 'hid').value = 'N';
			for (var j = 0; j< i.length; j++) {
				if ($(i[j]).data('switch') == 'on') i[j].className = 'hide';
				else i[j].className = 'block';
			}
		} else {
			document.getElementById(obj.id + 'hid').value = 'Y';
			for (var j = 0; j < i.length; j++) {
				if ($(i[j]).data('switch') == 'on') i[j].className = 'block';
				else i[j].className = 'hide';
			}
		}
	},
	modalClear: function(id) {
		document.getElementById(id).value = '';
		document.getElementById(id + '_text').value = '';
	},
	maskMe: function(id, options) {
        //options = $.extend({
        //    allowZero: true,
        //    thousands: ' ',
        //    defaultZero: false,
        //    allowNegative: true,
        //    precision: 2
        //}, options);

        //$('#' + id).maskMoney(options);
        //$('#' + id).maskMoney('mask');

        var options = $.extend({
            numeral: true,
            numeralDecimalMark: '.',
            delimiter: ' ',
            numeralDecimalScale: 2
        }, options);

		new Cleave('#' + id, options);
    },
    modal2: {
        key: '',

		options: [],


		/**
		 * @param theme_src
		 * @param key
		 * @param url
		 * @return {boolean}
		 */
        load: function(theme_src, key, url) {

			if (typeof url === 'function') {
				url = url();
			}

        	if ( ! url) {
        		return false;
			}

            this.key            = key;
            var modal_container = $('#' + this.key + '-modal').appendTo('#main_body');
            var $body_container = $('.modal-dialog>.modal-content>.modal-body', modal_container);

            $body_container.html(
                '<div style="text-align:center">' +
                    '<img src="' + theme_src + '/img/load.gif" alt="loading">' +
                    ' Загрузка' +
                '</div>'
            );

			$body_container.load(url);

            $('#' + this.key + '-modal').modal('show');


			if (this.options[key] && typeof this.options[key].onHidden === 'function') {
				$('#' + this.key + '-modal').on('hidden.bs.modal', this.options[key].onHidden);
			}
        },


		/**
		 * @param key
		 */
        clear: function(key) {

            $('#' + key).val('');
            $('#' + key + '-title').val('');


			if (this.options[key] && typeof this.options[key].onClear === 'function') {
				this.options[key].onClear();
			}
        },


		/**
		 *
		 */
        hide: function() {
            $('#' + this.key + '-modal').modal('hide');
        },


		/**
		 * @param value
		 * @param title
		 */
        choose: function(value, title) {
            $('#' + this.key).val(value);
            $('#' + this.key + '-title').val(title);
            this.hide();

			if (this.options[this.key] && typeof this.options[this.key].onChoose === 'function') {
				this.options[this.key].onChoose(value, title);
			}
		}
    },

    /**
     * @param toggleObject
     */
    toggleGroup: function(toggleObject) {
        $(toggleObject).parent().next().slideToggle('fast');
    },


	/**
	 *
	 */
	multilist2: {

		data: {},

		/**
		 * @param itemContainer
		 */
		deleteItem: function (itemContainer) {
			$(itemContainer).hide('fast', function () {
				$(this).remove();
			});
		},


		/**
		 * @param fieldId
		 * @param field
		 * @param attributes
		 * @param themePath
		 */
		addItem: function (fieldId, field, attributes, themePath) {

			var tpl =
				'<div class="multilist2-item" id="multilist2-item-[ID]" style="display: none">' +
				    '<select id="[ID]" name="control[[FIELD]][]" [ATTRIBUTES]>[OPTIONS]</select> ' +
				    '<img src="[THEME_PATH]/img/delete.png" alt="X" class="multilist2-delete"' +
				         'onclick="edit.multilist2.deleteItem($(\'#multilist2-item-[ID]\'))">' +
				'</div>';

			var options = [];

			if (typeof edit.multilist2.data[fieldId] !== "undefined") {
				$.each(edit.multilist2.data[fieldId], function (id, title) {
					if (typeof title === 'object') {
						options.push('<optgroup label="' + id + '">');

						$.each(title, function (grp_id, grp_title) {
							options.push('<option value="' + grp_id + '">' + grp_title + '</option>');
						});

						options.push('</optgroup>');

					} else {
						options.push('<option value="' + id + '">' + title + '</option>');
					}
				});
			}


			attributes = attributes.replace(/\!\:\:/g, '"');
			attributes = attributes.replace(/\!\:/g, "'");

			var id = this.keygen();

			tpl = tpl.replace(/\[ID\]/g, 		 id);
			tpl = tpl.replace(/\[FIELD\]/g,      field);
			tpl = tpl.replace(/\[ATTRIBUTES\]/g, attributes);
			tpl = tpl.replace(/\[OPTIONS\]/g,    options.join(''));
			tpl = tpl.replace(/\[THEME_PATH\]/g, themePath);

			$('#multilist2-' + fieldId + ' .multilist2-items').append(tpl);

			$('#multilist2-item-' + id + ' select').select2({
				language: 'ru',
				theme: 'bootstrap',
			});

			$('#multilist2-item-' + id).show('fast');
		},


		/**
		 * Генератор случайного ключа
		 * @param extInt
		 * @returns {*}
		 */
		keygen : function(extInt) {
			var d = new Date();
			var v1 = d.getTime();
			var v2 = d.getMilliseconds();
			var v3 = Math.floor((Math.random() * 1000) + 1);
			var v4 = extInt ? extInt : 0;

			return 'A' + v1 + v2 + v3 + v4;
		}
	},


	/**
	 *
	 */
	fieldDataset: {

		data: {},

		/**
		 * @param itemContainer
		 */
		deleteItem: function (itemContainer) {
			$(itemContainer).hide('fast', function () {
				$(this).remove();
			});
		},


		/**
		 * @param fieldId
		 * @param fieldName
		 * @param themePath
		 */
		addItem: function (fieldId, fieldName, themePath) {

			var tpl =
				'<tr class="field-dataset-item" id="field-dataset-item-[ID]" style="display: none">' +
				'[FIELDS] ' +
				'<td><img src="[THEME_PATH]/img/delete.png" alt="X" class="field-dataset-delete"' +
						 'onclick="edit.fieldDataset.deleteItem($(\'#field-dataset-item-[ID]\'))"></td>' +
				'</tr>';

			var tplField = '<td>' +
						       '<input type="text" class="form-control input-sm" name="control[[FIELD]][[NUM]][[CODE]]" value="[VALUE]" [ATTRIBUTES]>' +
						   '</td>';

			var fields = [];
			var key    = this.keygen();

			if (typeof edit.fieldDataset.data[fieldId] !== "undefined") {
				$.each(edit.fieldDataset.data[fieldId], function (id, field) {
					var tplFieldCustom = tplField;

					if (field['code']) {
						tplFieldCustom = tplFieldCustom.replace(/\[FIELD\]/g,      fieldName);
						tplFieldCustom = tplFieldCustom.replace(/\[NUM\]/g,        key);
						tplFieldCustom = tplFieldCustom.replace(/\[CODE\]/g,       field['code']);
						tplFieldCustom = tplFieldCustom.replace(/\[VALUE\]/g,      '');
						tplFieldCustom = tplFieldCustom.replace(/\[ATTRIBUTES\]/g, field['attributes'] || '');

						fields.push(tplFieldCustom);
					}
				});
			}


			var id = fieldId + '-' + key;

			tpl = tpl.replace(/\[ID\]/g, 		 id);
			tpl = tpl.replace(/\[FIELDS\]/g,     fields.join(''));
			tpl = tpl.replace(/\[THEME_PATH\]/g, themePath);

			$('#field-dataset-' + fieldId + ' .field-dataset-items').append(tpl);
			$('#field-dataset-item-' + id).show('fast');
		},


		/**
		 * Генератор случайного ключа
		 * @param extInt
		 * @returns {*}
		 */
		keygen : function(extInt) {
			var d = new Date();
			var v1 = d.getTime();
			var v2 = d.getMilliseconds();
			var v3 = Math.floor((Math.random() * 1000) + 1);
			var v4 = extInt ? extInt : 0;

			return 'A' + v1 + v2 + v3 + v4;
		}
	},


	/**
	 * @param container
	 */
	switchToggle: function (container) {

		var isActiveControl = $(container).find(':checked').hasClass('core-switch-active');

		if (isActiveControl) {
			$(container).find('.core-switch-active').prop('checked', false);
			$(container).find('.core-switch-inactive').prop('checked', true);

		} else {
			$(container).find('.core-switch-active').prop('checked', true);
			$(container).find('.core-switch-inactive').prop('checked', false);
		}
	}
};


/**
 * @param id
 * @param opt
 */
function mceSetup(id, opt) {

    var options = {
		selector : '#' + id,
        language : 'ru',
        theme : 'modern',
        skin : 'light',
        forced_root_block : false,
        force_br_newlines : true,
        force_p_newlines : false,
        verify_html : true,
        convert_urls : false,
        relative_urls : false,
        plugins: [
			"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"save table contextmenu directionality emoticons template paste textcolor"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
		theme_advanced_resizing : true
       };
    for (k in opt) {
        options[k] = opt[k];
    }
	tinymce.init(options);
}
