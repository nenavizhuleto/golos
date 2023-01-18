<div id="rooms-room-form" class="modal" tabindex="-1">
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
					  	<label class="col-form-label">Улица</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-room-form-street_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Дом</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-room-form-building_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Подъезд</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-room-form-porch_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">Номер</label>
					  </div>
					  <div class="col-2">
					    <input id="rooms-room-form-num" type="text" class="form-control">
					  </div>
					</div>

					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">SIP-клиент</label>
					  </div>
					  <div class="col-9 d-flex align-items-center">
						  <input id="rooms-room-form-mobile_device" class="form-check-input" type="checkbox">
					  </div>
					</div>

					<div class="row">
					  <div class="col-3 mb-2 rooms-room-form-mobile_device-row">
					  	<label class="col-form-label">SIP-логин</label>
					  </div>
					  <div class="col-3 mb-2 rooms-room-form-mobile_device-row">
					  	<label id="rooms-room-form-mobile_device_username" class="col-form-label"></label>
					  </div>
					  <div class="col-3 mb-2 rooms-room-form-mobile_device-row">
					  	<label class="col-form-label">SIP-пароль</label>
					  </div>
					  <div class="col-3 mb-2 rooms-room-form-mobile_device-row">
							<input id="rooms-room-form-mobile_device_password" type="text" class="form-control" maxlength="8">
					  </div>
					</div>
				</form>
      </div>
      <div class="modal-footer border-0">
        <button id="rooms-room-form-save" type="button" class="btn btn-success">Сохранить</button>
        <button id="rooms-room-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>
	var roomsRoomForm;

	function roomsRoomFormCreate() {
		$('#rooms-room-form').data('id', null);
		$('#rooms-room-form').data('mobile_device_id', null);

		$('#rooms-room-form .modal-title').html('Новая квартира');
		$('#rooms-room-form-street_id')
			.html('<option value="0" selected>- выберите улицу -</option>')
			.trigger('input')
			.prop('disabled', true);

		$.get('/streets/', {
		}, (data) => {
			var html = '<option value="0" selected>- выберите улицу -</option>';
			for (var i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
			}

			$('#rooms-room-form-street_id')
				.html(html)
				.trigger('input')
				.prop('disabled', false);

			roomsRoomForm.show();
		}, 'json');

		$('#rooms-room-form-mobile_device').prop('checked', true);
		$('#rooms-room-form-mobile_device_username').html('');
		$('#rooms-room-form-mobile_device_password').val('');
		$('.rooms-room-form-mobile_device-row').hide();

		roomsRoomForm.show();
	}

	function roomsRoomFormEdit(id) {
		$('#rooms-room-form').data('id', id);

		$.get('/rooms/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			var row = data.rows[0];

			$('#rooms-room-form').data('mobile_device_id', row.mobile_device_id);

			$('#rooms-room-form .modal-title').html('Квартира #' + row.id);
			$('#rooms-room-form-street_id')
				.html('<option value="0" selected>- выберите улицу -</option>')
				.trigger('input')
				.prop('disabled', true);

			$.get('/streets/', {
			}, (data) => {
				var html = '<option value="0" selected>- выберите улицу -</option>';
				for (var i in data.rows) {
					html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
				}

				$('#rooms-room-form-street_id')
					.html(html)
					.val(row.street_id)
					.trigger('input')
					.prop('disabled', false);

				$('#rooms-room-form-building_id')
					.html('<option value="0" selected>- выберите улицу -</option>')
					.trigger('input')
					.prop('disabled', true);

				$.get('/buildings/', {
					street_id: row.street_id
				}, (data) => {
					var html = '<option value="0" selected>- выберите дом -</option>';
					for (var i in data.rows) {
						html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
					}

					$('#rooms-room-form-building_id')
						.html(html)
						.val(row.building_id)
						.trigger('input')
						.prop('disabled', false);

					$('#rooms-room-form-porch_id')
						.html('<option value="0" selected>- выберите подъезд -</option>')
						.trigger('input')
						.prop('disabled', true);

					$.get('/porches/', {
						building_id: row.building_id
					}, (data) => {
						var html = '<option value="0">- выберите подъезд -</option>';
						for (var i in data.rows) {
							html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
						}

						$('#rooms-room-form-porch_id')
							.html(html)
							.val(row.porch_id)
							.prop('disabled', false)
							.trigger('input');

						$('#rooms-room-form-num')
							.val(row.num)
							.prop('disabled', false)
							.trigger('input');

						// have attached mobile device?
						if (null != row.mobile_device_id) {
							$('#rooms-room-form-mobile_device').prop('checked', true);
							$('#rooms-room-form-mobile_device_username').html(row.device_sipusername);
							$('#rooms-room-form-mobile_device_password').val(row.device_sippassword);
							$('.rooms-room-form-mobile_device-row').show();
						} else {
							$('#rooms-room-form-mobile_device').prop('checked', false);
							$('.rooms-room-form-mobile_device-row').hide();
						}

						roomsRoomFormValidate();
						roomsRoomForm.show();
					}, 'json');
				}, 'json');
			}, 'json');
		}, 'json');
	}

	function roomsRoomFormValidate() {
		var num = parseInt($('#rooms-room-form-num').val());
		var mobile_device = $('#rooms-room-form-mobile_device').prop('checked');
		var mobile_device_password = $('#rooms-room-form-mobile_device_password').val();

		var ok = true;

		if (!num || num <= 0)
			ok = false;

		if (null != $('#rooms-room-form').data('mobile_device_id'))
			if (mobile_device)
				if (mobile_device_password.length < 6)
					ok = false;

		$('#rooms-room-form-save').prop('disabled', !ok);
	}

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		roomsRoomForm = new bootstrap.Modal('#rooms-room-form', {});

		$('#rooms-room-form-street_id').on('input', function() {
			var street_id = parseInt($(this).val());
			if (!street_id) {
				var html = '<option value="0">- выберите дом -</option>';
				$('#rooms-room-form-building_id')
					.prop('disabled', true)
					.html(html)
					.val(0)
					.trigger('input');

				return;
			}

			$.get('/buildings/', {
				street_id: street_id
			}, (data) => {
				var html = '<option value="0" selected>- выберите дом -</option>';
				for (var i in data.rows) {
					html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
				}

				$('#rooms-room-form-building_id')
					.html(html)
					.trigger('input')
					.prop('disabled', false);
			}, 'json');
		});

		$('#rooms-room-form-building_id').on('input', function() {
			var building_id = parseInt($(this).val());
			if (!building_id) {
				var html = '<option value="0" selected>- выберите подъезд -</option>';
				$('#rooms-room-form-porch_id')
					.prop('disabled', true)
					.html(html)
					.trigger('input');

				return;
			}

			$.get('/porches/', {
				building_id: building_id
			}, (data) => {
				var html = '<option value="0" selected>- выберите подъезд -</option>';
				for (var i in data.rows) {
					html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
				}

				$('#rooms-room-form-porch_id')
					.html(html)
					.trigger('input')
					.prop('disabled', false);
			}, 'json');
		});

		$('#rooms-room-form-porch_id').on('input', function() {
			var porch_id = parseInt($(this).val());
			if (!porch_id) {
				$('#rooms-room-form-num')
					.val('')
					.prop('disabled', true)
					.trigger('input');

				return;
			}

			$('#rooms-room-form-num')
				.val('')
				.prop('disabled', false)
				.trigger('input');
		});

		$('#rooms-room-form-num').on('input', function() {
			roomsRoomFormValidate();
		});

		$('#rooms-room-form-mobile_device').on('input', function() {
			var id = parseInt($('#rooms-room-form').data('id'));
			var checked = $(this).prop('checked');

			if (id) {
				var mobile_device_id = $('#rooms-room-form').data('mobile_device_id');

				if (null !== mobile_device_id) {
					if (checked) {
						$('.rooms-room-form-mobile_device-row').show();
					} else {
						$('.rooms-room-form-mobile_device-row').hide();
					}
				}
			}

			roomsRoomFormValidate();
		});
		
		$('#rooms-room-form-mobile_device_password').on('input', function() {
			roomsRoomFormValidate();
		});

		$('#rooms-room-form-save').on('click', function() {
			var id = parseInt($('#rooms-room-form').data('id'));
			var porch_id = parseInt($('#rooms-room-form-porch_id').val());
			var num = parseInt($('#rooms-room-form-num').val());
			var mobile_device = $('#rooms-room-form-mobile_device').prop('checked');
			var mobile_device_password = $('#rooms-room-form-mobile_device_password').val();

			$('#rooms-room-form-street_id').prop('disabled', true);
			$('#rooms-room-form-building_id').prop('disabled', true);
			$('#rooms-room-form-porch_id').prop('disabled', true);
			$('#rooms-room-form-num').prop('disabled', true);
			$('#rooms-room-form-mobile_device').prop('disabled', true);
			$('#rooms-room-form-mobile_device_password').prop('disabled', true);
			$('#rooms-room-form-save').prop('disabled', true);
			$('#rooms-room-form-cancel').prop('disabled', true);

			if (id) {
				var data = {
					id: id,
					porch_id: porch_id,
					num: num,
					mobile_device: mobile_device,
					mobile_device_password: mobile_device_password
				};
			} else {
				var data = {
					porch_id: porch_id,
					num: num,
					mobile_device: mobile_device
				};
			}

			$.post('/rooms/save/', data, (response) => {
				if (!response.result) {
					if (Object.hasOwn(response, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: response.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(response, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: response.message });

				roomsRoomForm.hide();
				roomsTable.reloadData();

				// TODO: should we reopen new created entity everywhere?
				if (Object.hasOwn(response,'id')) {
					// was created
					roomsRoomFormEdit(response.id);
				}
			}, 'json').always(() => {
				$('#rooms-room-form-mobile_device').prop('disabled', false);
				$('#rooms-room-form-mobile_device_password').prop('disabled', false);
				$('#rooms-room-form-cancel').prop('disabled', false);
			});
		});
	});
</script>
