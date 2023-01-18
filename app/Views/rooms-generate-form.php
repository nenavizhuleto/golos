<div id="rooms-generate-form" class="modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Генерация квартир</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
      	<form>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Улица</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-generate-form-street_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Дом</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-generate-form-building_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Подъезд</label>
					  </div>
					  <div class="col-9">
							<select id="rooms-generate-form-porch_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">Квартиры</label>
					  </div>
					  <div class="col-2">
					    <input id="rooms-generate-form-rooms_from" type="text" class="form-control">
					  </div>
					  <div class="col-1">
					  	<label class="col-form-label">&mdash;</label>
					  </div>
					  <div class="col-2">
					    <input id="rooms-generate-form-rooms_to" type="text" class="form-control">
					  </div>
					</div>
					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">SIP-клиент</label>
					  </div>
					  <div class="col-9 d-flex align-items-center">
						  <input id="rooms-generate-form-mobile_device" class="form-check-input" type="checkbox">
					  </div>
					</div>
				</form>
      </div>
      <div class="modal-footer border-0">
        <button id="rooms-generate-form-submit" type="button" class="btn btn-success">Генерировать</button>
        <button id="rooms-generate-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>
	var roomsGenerateForm;

	function roomsGenerateFormShow() {
		var html = '<option value="0" selected>- выберите улицу -</option>';
		$('#rooms-generate-form-street_id')
			.prop('disabled', true)
			.html(html)
			.trigger('input');

		$.get('/streets/', {
		}, (data) => {
			var html = '<option value="0" selected>- выберите улицу -</option>';
			for (var i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
			}

			$('#rooms-generate-form-street_id')
				.html(html)
				.trigger('input')
				.prop('disabled', false);

			$('#rooms-generate-form-mobile_device')
				.prop('checked', true)
				.prop('disabled', false);

			roomsGenerateForm.show();
		}, 'json');
	}

	function roomsGenerateFormValidate() {
		var rooms_from = parseInt($('#rooms-generate-form-rooms_from').val());
		var rooms_to = parseInt($('#rooms-generate-form-rooms_to').val());
		var ok = true;

		if (!rooms_from || rooms_from <= 0)
			ok = false;

		if (!rooms_to || rooms_to <= 0)
			ok = false;

		if (rooms_from > rooms_to)
			ok = false;

		$('#rooms-generate-form-submit').prop('disabled', !ok);
	}

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		roomsGenerateForm = new bootstrap.Modal('#rooms-generate-form', {});

		$('#rooms-generate-form-street_id').on('input', function() {
			var street_id = parseInt($(this).val());
			if (!street_id) {
				var html = '<option value="0" selected>- выберите дом -</option>';
				$('#rooms-generate-form-building_id')
					.prop('disabled', true)
					.html(html)
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

				$('#rooms-generate-form-building_id')
					.html(html)
					.prop('disabled', false);
			}, 'json');
		});

		$('#rooms-generate-form-building_id').on('input', function() {
			var building_id = parseInt($(this).val());
			if (!building_id) {
				var html = '<option value="0" selected>- выберите подъезд -</option>';
				$('#rooms-generate-form-porch_id')
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

				$('#rooms-generate-form-porch_id')
					.html(html)
					.prop('disabled', false);
			}, 'json');
		});

		$('#rooms-generate-form-porch_id').on('input', function() {
			var porch_id = parseInt($(this).val());
			if (!porch_id) {
				$('#rooms-generate-form-rooms_from')
					.prop('disabled', true)
					.val('')
					.trigger('input');

				$('#rooms-generate-form-rooms_to')
					.prop('disabled', true)
					.val('')
					.trigger('input');

				return;
			}

			$('#rooms-generate-form-rooms_from')
				.prop('disabled', false)
				.trigger('input')
				.focus();

			$('#rooms-generate-form-rooms_to')
				.prop('disabled', false)
				.trigger('input');
		});

		$('#rooms-generate-form-rooms_from').on('input', function() {
			roomsGenerateFormValidate();
		});

		$('#rooms-generate-form-rooms_to').on('input', function() {
			roomsGenerateFormValidate();
		});

		$('#rooms-generate-form-submit').on('click', function() {
			var rooms_from = parseInt($('#rooms-generate-form-rooms_from').val());
			var rooms_to = parseInt($('#rooms-generate-form-rooms_to').val());
			var porch_id = parseInt($('#rooms-generate-form-porch_id').val());
			var mobile_device = $('#rooms-generate-form-mobile_device').prop('checked');
			var count = rooms_to - rooms_from + 1;

			if (!confirm('Будет создано квартир: ' + count + '. Вы уверены?'))
				return;

			$('#rooms-generate-form-street_id').prop('disabled', true);
			$('#rooms-generate-form-building_id').prop('disabled', true);
			$('#rooms-generate-form-porch_id').prop('disabled', true);
			$('#rooms-generate-form-rooms_from').prop('disabled', true);
			$('#rooms-generate-form-rooms_to').prop('disabled', true);
			$('#rooms-generate-form-mobile_device').prop('disabled', true);
			$('#rooms-generate-form-submit').prop('disabled', true);
			$('#rooms-generate-form-cancel').prop('disabled', true);

			$.post('/rooms/generate/', {
				porch_id: porch_id,
				rooms_from: rooms_from,
				rooms_to: rooms_to,
				mobile_device: mobile_device
			}, (data) => {
			}, 'json').always(() => {
				$('#rooms-generate-form-cancel').prop('disabled', false);

				roomsGenerateForm.hide();
				roomsTable.reloadData();
			});
		});
	});
</script>
