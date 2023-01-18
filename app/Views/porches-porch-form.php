<div id="porches-porch-form" class="modal" tabindex="-1">
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
							<select id="porches-porch-form-street_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row mb-2">
					  <div class="col-3">
					  	<label class="col-form-label">Дом</label>
					  </div>
					  <div class="col-9">
							<select id="porches-porch-form-building_id" class="form-select"></select>      	
					  </div>
					</div>
					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">Номер</label>
					  </div>
					  <div class="col-2">
					    <input id="porches-porch-form-num" type="text" class="form-control">
					  </div>
					</div>
				</form>
      </div>
      <div class="modal-footer border-0">
        <button id="porches-porch-form-save" type="button" class="btn btn-success">Сохранить</button>
        <button id="porches-porch-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>
	function porchesPorchFormGetStreets(street_id = null, callback = null) {
		$('#porches-porch-form-street_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите улицу -</option>')
			.val(0)
			.trigger('input');

		$.get('/streets/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
			}

			let el = $('#porches-porch-form-street_id');
			el.append(html);

			if (street_id)
				el.val(street_id);

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function porchesPorchFormGetBuildings(street_id = 0, building_id = null, callback = null) {
		$('#porches-porch-form-building_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите дом -</option>')
			.val(0)
			.trigger('input');

		if (!street_id) {
			if (callback instanceof Function)
				callback();

			return;
		}

		$.get('/buildings/', {
			street_id: street_id
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
			}

			let el = $('#porches-porch-form-building_id');
			el.append(html);

			if (building_id)
				el.val(building_id)

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function porchesPorchFormReset(callback = null) {
		$('#porches-porch-form').data('id', null);
		$('#porches-porch-form-title').val('');

		porchesPorchFormGetStreets(null, callback);
	}

	function porchesPorchFormLoad(id, callback = null) {
		$('#porches-porch-form').data('id', id);

		$.get('/porches/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			let row = data.rows[0];
		
			porchesPorchFormGetStreets(row.street_id, () => {
				porchesPorchFormGetBuildings(row.street_id, row.building_id, () => {
					$('#porches-porch-form-num')
						.val(row.num)
						.prop('disabled', false);

					if (callback instanceof Function)
						callback();
				});
			});
		}, 'json');
	}

	function porchesPorchFormCreate() {
		$('#porches-porch-form .modal-title').html('Новый подъезд');

		porchesPorchFormReset(() => {
			porchesPorchForm.show();
		});
	}

	function porchesPorchFormEdit(id) {
		$('#porches-porch-form .modal-title').html('Подъезд #' + id);

		porchesPorchFormLoad(id, () => {
			porchesPorchForm.show();
		});
	}

	function porchesPorchFormValidate() {
		let num = parseInt($('#porches-porch-form-num').val());
		let ok = true;

		if (!num || num <= 0)
			ok = false;

		$('#porches-porch-form-save').prop('disabled', !ok);
	}

	var porchesPorchForm = null;
	
	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		porchesPorchForm = new bootstrap.Modal('#porches-porch-form', {});

		$('#porches-porch-form-street_id').on('input', function() {
			let street_id = parseInt($(this).val());
			porchesPorchFormGetBuildings(street_id);
		});

		$('#porches-porch-form-building_id').on('input', function() {
			let building_id = parseInt($(this).val());
			if (!building_id) {
				$('#porches-porch-form-num')
					.prop('disabled', true)
					.val('')
					.trigger('input');

				return;
			}

			$('#porches-porch-form-num')
				.val('')
				.prop('disabled', false)
				.trigger('input');
		});

		$('#porches-porch-form-num').on('input', function() {
			porchesPorchFormValidate();
		});
		
		$('#porches-porch-form-save').on('click', function() {
			let id = parseInt($('#porches-porch-form').data('id'));
			let building_id = parseInt($('#porches-porch-form-building_id').val());
			let num = parseInt($('#porches-porch-form-num').val());

			$('#porches-porch-form-street_id').prop('disabled', true);
			$('#porches-porch-form-building_id').prop('disabled', true);
			$('#porches-porch-form-num').prop('disabled', true);
			$('#porches-porch-form-save').prop('disabled', true);
			$('#porches-porch-form-cancel').prop('disabled', true);

			let data = {
				building_id: building_id,
				num: num
			};

			if (id)
				data.id = id;

			$.post('/porches/save/', data, (data) => {
				if (!data.result) {
					if (Object.hasOwn(data, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(data, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });

				porchesTable.reloadData();
			}, 'json').always(() => {
				$('#porches-porch-form-cancel').prop('disabled', false);

				porchesPorchForm.hide();
			});
		});
	});
</script>
