<div id="devices-device-form" class="modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
      	<form>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Тип объекта</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-object_type" class="form-select">
								<option value="">- выберите тип объекта -</option>
								<option value="block">Двор</option>
								<option value="porch">Подъезд</option>
								<option value="room">Квартира</option>
							</select>      	
					  </div>
					</div>
					<div class="devices-device-form-block_device_type-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Устройство</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-block_device_type" class="form-select">
								<option value="0">- выберите тип устройства -</option>
								<option value="2">Домофон калитки, многоаб. панель</option>
								<option value="3">Домофон ворот, одноаб. панель</option>
							</select>      	
					  </div>
					</div>
					<div class="devices-device-form-porch_device_type-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Устройство</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-porch_device_type" class="form-select">
								<option value="0">- выберите тип устройства -</option>
								<option value="1">Домофон подъездный, многоаб. панель</option>
								<option value="5">Абонентское устройство, консьерж</option>
							</select>      	
					  </div>
					</div>
					<div class="devices-device-form-room_device_type-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Устройство</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-room_device_type" class="form-select">
								<option value="0">- выберите тип устройства -</option>
								<option value="6">Абонентское устройство, трубка</option>
								<option value="7">Абонентское устройство, видеомонитор</option>
								<option value="8">Абонентское устройство, вызывная панель</option>
							</select>      	
					  </div>
					</div>
					<div class="devices-device-form-block_id-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Двор</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-block_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="devices-device-form-street_id-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Улица</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-street_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row">
					  <div class="col-3 mb-2 devices-device-form-building_id-row">
					  	<label class="col-form-label">Дом</label>
					  </div>
					  <div class="col-3 mb-2 devices-device-form-building_id-row">
							<select id="devices-device-form-building_id" class="form-select"></select>      	
					  </div>
					  <div class="col-3 mb-2 devices-device-form-porch_id-row">
					  	<label class="col-form-label">Подъезд</label>
					  </div>
					  <div class="col-3 mb-2 devices-device-form-porch_id-row">
							<select id="devices-device-form-porch_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="devices-device-form-room_id-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Квартира</label>
					  </div>
					  <div class="col-9">
							<select id="devices-device-form-room_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row">
					  <div class="col-3 mb-2 devices-device-form-sipusername-row">
					  	<label class="col-form-label">SIP-логин</label>
					  </div>
					  <div class="col-3 mb-2 devices-device-form-sipusername-row">
					  	<label id="devices-device-form-sipusername" class="col-form-label"></label>
					  </div>
					  <div class="col-3 mb-2 devices-device-form-sippassword-row">
					  	<label class="col-form-label">SIP-пароль</label>
					  </div>
					  <div class="col-3 mb-2 devices-device-form-sippassword-row">
							<input id="devices-device-form-sippassword" type="text" class="form-control" maxlength="8">
					  </div>
					</div>

					<div class="devices-device-form-comment-row row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Комментарий</label>
					  </div>
					  <div class="col-9">
							<textarea id="devices-device-form-comment" class="form-control" rows="3"></textarea>      	
					  </div>
					</div>
				</form>
      </div>
      <div class="modal-footer border-0">
        <button id="devices-device-form-save" type="button" class="btn btn-success"></button>
        <button id="devices-device-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal"></button>
      </div>
    </div>
  </div>
</div>

<script>
	function devicesDeviceFormGetBlocks(block_id = 0, user_inited = true, callback = null) {
		let el = $('#devices-device-form-block_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите двор -</option>')
			.val(0);

		if (user_inited)
			el.trigger('input');

		return $.get('/blocks/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">Двор №' + data.rows[i].num + ' (' + data.rows[i].buildings_string  + ')</option>';
			}

			let el = $('#devices-device-form-block_id');
			el.append(html);

			if (block_id) {
				el.val(block_id);

				if (user_inited)
					el.trigger('input');
			}

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function devicesDeviceFormGetStreets(street_id = 0, user_inited = true, callback = null) {
		let el = $('#devices-device-form-street_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите улицу -</option>')
			.val(0);

		if (user_inited)
			el.trigger('input');

		return $.get('/streets/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
			}

			let el = $('#devices-device-form-street_id');
			el.append(html);

			if (street_id) {
				el.val(street_id);

				if (user_inited)
					el.trigger('input');
			}

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function devicesDeviceFormGetBuildings(street_id = 0, building_id = 0, user_inited = true, callback = null) {
		let el = $('#devices-device-form-building_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите дом -</option>')
			.val(0);

		if (!street_id) {
			if (user_inited)
				el.trigger('input');

			return null;
		}

		return $.get('/buildings/', {
			street_id: street_id
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num +'</option>';
			}

			let el = $('#devices-device-form-building_id');
			el.append(html);

			if (building_id) {
				el.val(building_id);
				
				if (user_inited)
					el.trigger('input');
			}

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function devicesDeviceFormGetPorches(building_id = 0, porch_id = 0, user_inited = true, callback = null) {
		let el = $('#devices-device-form-porch_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите подъезд -</option>')
			.val(0);

		if (!building_id) {
			if (user_inited)
				el.trigger('input');

			return null;
		}

		return $.get('/porches/', {
			building_id: building_id
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
			}

			let el = $('#devices-device-form-porch_id');
			el.append(html);

			if (porch_id) {
				el.val(porch_id)

				if (user_inited)
					el.trigger('input');
			}

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function devicesDeviceFormGetRooms(porch_id = 0, room_id = 0, user_inited = true, callback = null) {
		let el = $('#devices-device-form-room_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите квартиру -</option>')
			.val(0);

		if (!porch_id) {
			if (user_inited)
				el.trigger('input');

			return null;
		}

		return $.get('/rooms/', {
			porch_id: porch_id
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
			}

			let el = $('#devices-device-form-room_id');
			el.append(html);

			if (room_id) {
				el.val(room_id);

				if (user_inited)
					el.trigger('input');
			}

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function devicesDeviceFormReset(callback = null) {
		devicesDeviceForm._config.id = null;

		// object_type
		$('#devices-device-form-object_type')
			.val('')
			.trigger('input', [ false ]);

		// common fields
		$('.devices-device-form-sipusername-row').hide();
		$('#devices-device-form-sipusername').html('');
		$('.devices-device-form-sippassword-row').hide();
		$('#devices-device-form-sippassword').val('');
		$('.devices-device-form-comment-row').hide();
		$('#devices-device-form-comment').val('');

		// validate
		devicesDeviceFormValidate();

		// callback
		if (callback instanceof Function)
			callback();
	}

	function devicesDeviceFormLoad(id, callback = null) {
		devicesDeviceForm._config.id = null;

		$.get('/devices/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			let row = data.rows[0];
			devicesDeviceForm._config.id = row.id;

			// common fields
			$('#devices-device-form-sipusername').html(row.sipusername);
			$('.devices-device-form-sipusername-row').show();
			$('#devices-device-form-sippassword').val(row.sippassword);
			$('.devices-device-form-sippassword-row').show();
			$('#devices-device-form-comment').val(row.comment);
			$('.devices-device-form-comment-row').show();

			// block device
			if (null != row.block_id) {
				$('#devices-device-form-object_type')
					.val('block')
					.trigger('input', [ false ]);

				$('#devices-device-form-block_device_type')
					.val(row.type)
					.trigger('input', [ false ]);

				$.when(
					devicesDeviceFormGetBlocks(row.block_id, false)
				).done(() => {
					devicesDeviceFormValidate();

					if (callback instanceof Function) {
						callback();
					}
				});

				return;
			}

			// porch device
			if (null != row.porch_id) {
				$('#devices-device-form-object_type')
					.val('porch')
					.trigger('input', [ false ]);

				$('#devices-device-form-porch_device_type')
					.val(row.type)
					.trigger('input', [ false ]);

				$.when(
					devicesDeviceFormGetStreets(row.porch_street_id, false),
					devicesDeviceFormGetBuildings(row.porch_street_id, row.porch_building_id, false),
					devicesDeviceFormGetPorches(row.porch_building_id, row.porch_id, false)
				).done(() => {
					devicesDeviceFormValidate();

					if (callback instanceof Function) {
						callback();
					}
				});

				return;
			}

			// room device
			if (null != row.room_id) {
				$('#devices-device-form-object_type')
					.val('room')
					.trigger('input', [ false ]);

				$('#devices-device-form-room_device_type')
					.val(row.type)
					.trigger('input', [ false ]);

				$.when(
					devicesDeviceFormGetStreets(row.room_street_id, false),
					devicesDeviceFormGetBuildings(row.room_street_id, row.room_building_id, false),
					devicesDeviceFormGetPorches(row.room_building_id, row.room_porch_id, false),
					devicesDeviceFormGetRooms(row.room_porch_id, row.room_id, false)
				).done(() => {
					devicesDeviceFormValidate();

					if (callback instanceof Function) {
						callback();
					}
				});

				return;
			}

			$('#devices-device-form-object_type')
				.val('')
				.trigger('input', [ false ]);

			if (callback instanceof Function) {
				callback();
			}
		}, 'json');
	}

	function devicesDeviceFormCreate() {
		$('#devices-device-form .modal-title').html('Новое устройство');
		$('#devices-device-form-save')
			.removeClass('btn-primary')
			.addClass('btn-success')
			.html('Создать');
		$('#devices-device-form-cancel').html('Отмена');

		devicesDeviceFormReset(() => {
			devicesDeviceForm.show();
		});
	}

	function devicesDeviceFormEdit(id) {
		$('#devices-device-form .modal-title').html('Устройство #' + id);
		$('#devices-device-form-save')
			.removeClass('btn-success')
			.addClass('btn-primary')
			.html('Сохранить');
		$('#devices-device-form-cancel').html('Закрыть');

		devicesDeviceFormLoad(id, () => {
			devicesDeviceForm.show();
		});
	}

	function devicesDeviceFormValidate() {
		let device_type;
		let porch_id;
		let room_id;
		let ok = true;

		let object_type = $('#devices-device-form-object_type').val();
		switch (object_type) {
		case 'block':
			device_type = parseInt($('#devices-device-form-block_device_type').val());
			if (!device_type)
				ok = false;

			block_id = parseInt($('#devices-device-form-block_id').val());
			if (!block_id)
				ok = false;

			break;

		case 'porch':
			device_type = parseInt($('#devices-device-form-porch_device_type').val());
			if (!device_type)
				ok = false;

			porch_id = parseInt($('#devices-device-form-porch_id').val());
			if (!porch_id)
				ok = false;

			break;

		case 'room':
			device_type = parseInt($('#devices-device-form-room_device_type').val());
			if (!device_type)
				ok = false;

			room_id = parseInt($('#devices-device-form-room_id').val());
			if (!room_id)
				ok = false;

			break;
				
		default:
			ok = false;
		}

		if (null != devicesDeviceForm._config.id) {
			// sippassword
			let sippassword = $('#devices-device-form-sippassword').val();
			if (!sippassword.length)
				ok = false;
		}

		$('#devices-device-form-save').prop('disabled', !ok);
	}

	var devicesDeviceForm = null;

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		devicesDeviceForm = new bootstrap.Modal('#devices-device-form', { id: null });

		$('#devices-device-form-object_type').on('input', function(event, user_inited = true) {
			let el;

			$('.devices-device-form-block_device_type-row').hide();
			$('.devices-device-form-porch_device_type-row').hide();
			$('.devices-device-form-room_device_type-row').hide();
			$('.devices-device-form-block_id-row').hide();
			$('.devices-device-form-street_id-row').hide();
			$('.devices-device-form-building_id-row').hide();
			$('.devices-device-form-porch_id-row').hide();
			$('.devices-device-form-room_id-row').hide();

			let object_type = $(this).val();
			switch (object_type) {
			case 'block':
				$('.devices-device-form-block_device_type-row').show();
				$('.devices-device-form-block_id-row').show();

				el = $('#devices-device-form-block_device_type');
				el.val(0);

				if (user_inited) {
					el.trigger('input');
					devicesDeviceFormGetBlocks();
				}

				break;

			case 'porch':
				$('.devices-device-form-porch_device_type-row').show();
				$('.devices-device-form-street_id-row').show();
				$('.devices-device-form-building_id-row').show();
				$('.devices-device-form-porch_id-row').show();

				el = $('#devices-device-form-porch_device_type');
				el.val(0);

				if (user_inited) {
					el.trigger('input');
					devicesDeviceFormGetStreets();
				}

				break;

			case 'room':
				$('.devices-device-form-room_device_type-row').show();
				$('.devices-device-form-street_id-row').show();
				$('.devices-device-form-building_id-row').show();
				$('.devices-device-form-porch_id-row').show();
				$('.devices-device-form-room_id-row').show();

				el = $('#devices-device-form-room_device_type');
				el.val(0);

				if (user_inited) {
					el.trigger('input');
					devicesDeviceFormGetStreets();
				}

				break;

			default:
				break;
			}

			if (user_inited)
				devicesDeviceFormValidate();
		});

		$('#devices-device-form-block_device_type').on('input', function(event, user_inited = true) {
			if (user_inited)
				devicesDeviceFormValidate();
		});

		$('#devices-device-form-porch_device_type').on('input', function(event, user_inited = true) {
			devicesDeviceFormValidate();
		});

		$('#devices-device-form-room_device_type').on('input', function(event, user_inited = true) {
			devicesDeviceFormValidate();
		});

		$('#devices-device-form-block_id').on('input', function(event, user_inited = true) {
			devicesDeviceFormValidate();
		});

		$('#devices-device-form-street_id').on('input', function(event, user_inited = true) {
			if (user_inited) {
				let street_id = parseInt($(this).val());
				devicesDeviceFormGetBuildings(street_id, 0);
			}
		});

		$('#devices-device-form-building_id').on('input', function(event, user_inited = true) {
			if (user_inited) {
				let building_id = parseInt($(this).val());
				devicesDeviceFormGetPorches(building_id, 0);
			}
		});

		$('#devices-device-form-porch_id').on('input', function(event, user_inited = true) {
			let object_type = $('#devices-device-form-object_type').val();
			if ('porch' == object_type) {
				devicesDeviceFormValidate();

				return;
			}

			if ('room' == object_type)
				if (user_inited) {
					let porch_id = parseInt($(this).val());
					devicesDeviceFormGetRooms(porch_id, 0);
				}
		});

		$('#devices-device-form-room_id').on('input', function() {
			devicesDeviceFormValidate();
		});

		$('#devices-device-form-sippassword').on('input', function() {
			devicesDeviceFormValidate();
		});

		$('#devices-device-form-save').on('click', function() {
			let data = {};

			// id
			let id = devicesDeviceForm._config.id;
			if (null != id)
				data.id = id;

			// type, (block_id / porch_id / room_id)
			let object_type = $('#devices-device-form-object_type').val();
			switch(object_type) {
			case 'block':
				data.type = parseInt($('#devices-device-form-block_device_type').val());
				data.block_id = parseInt($('#devices-device-form-block_id').val());
				break;

			case 'porch':
				data.type = parseInt($('#devices-device-form-porch_device_type').val());
				data.porch_id = parseInt($('#devices-device-form-porch_id').val());
				break;

			case 'room':
				data.type = parseInt($('#devices-device-form-room_device_type').val());
				data.room_id = parseInt($('#devices-device-form-room_id').val());
				break;

			default:
				return;
			}

			if (null != id) {
				// sipassword
				data.sippassword = $('#devices-device-form-sippassword').val();

				// comment
				data.comment = $('#devices-device-form-comment').val();
			}

			// disabling inputs while saving
			let el = $('#devices-device-form-object_type');
			el.prop('disabled', true);

			switch (el.val()) {
			case 'block':
				$('#devices-device-form-block_device_type').prop('disabled', true);
				$('#devices-device-form-block_id').prop('disabled', true);
				break;

			case 'porch':
				$('#devices-device-form-porch_device_type').prop('disabled', true);
				$('#devices-device-form-street_id').prop('disabled', true);
				$('#devices-device-form-building_id').prop('disabled', true);
				$('#devices-device-form-porch_id').prop('disabled', true);
				break;	

			case 'room':
				$('#devices-device-form-room_device_type').prop('disabled', true);
				$('#devices-device-form-street_id').prop('disabled', true);
				$('#devices-device-form-building_id').prop('disabled', true);
				$('#devices-device-form-porch_id').prop('disabled', true);
				$('#devices-device-form-room_id').prop('disabled', true);
				break;
			}

			if (data.id) {
				$('#devices-device-form-sippassword').prop('disabled', true);
				$('#devices-device-form-comment').prop('disabled', true);
			}

			$('#devices-device-form-save').prop('disabled', true);

			// saving
			// TODO: rename first closure's param of .post(.get) to "response" everywhere!
			// TODO: because of ambiguity with second param
			$.post('/devices/save/', data, (response) => {
				if (!response.result) {
					if (Object.hasOwn(response, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: response.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(response, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: response.message });

				devicesDeviceForm.hide();
				devicesTable.reloadData();

				// TODO: should we reopen new created entity everywhere?
				if (Object.hasOwn(response,'id')) {
					// was created
					devicesDeviceFormEdit(response.id);
				}
			}, 'json').always(() => {
				// restoring inputs after save
				let el = $('#devices-device-form-object_type');
				el.prop('disabled', false);

				switch (el.val()) {
				case 'block':
					$('#devices-device-form-block_device_type').prop('disabled', false);
					$('#devices-device-form-block_id').prop('disabled', false);
					break;

				case 'porch':
					$('#devices-device-form-porch_device_type').prop('disabled', false);
					$('#devices-device-form-street_id').prop('disabled', false);
					$('#devices-device-form-building_id').prop('disabled', false);
					$('#devices-device-form-porch_id').prop('disabled', false);
					break;	

				case 'room':
					$('#devices-device-form-room_device_type').prop('disabled', false);
					$('#devices-device-form-street_id').prop('disabled', false);
					$('#devices-device-form-building_id').prop('disabled', false);
					$('#devices-device-form-porch_id').prop('disabled', false);
					$('#devices-device-form-room_id').prop('disabled', false);
					break;
				}

				if (data.id) {
					$('#devices-device-form-sippassword').prop('disabled', false);
					$('#devices-device-form-comment').prop('disabled', false);
				}

				$('#devices-device-form-save').prop('disabled', false);
			});
		});
	});
</script>
