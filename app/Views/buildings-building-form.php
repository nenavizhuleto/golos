<div id="buildings-building-form" class="modal" tabindex="-1">
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
				<label class="col-form-label">Двор</label>
			  </div>
			  <div class="col-9">
					<select id="buildings-building-form-block_id" class="form-select"></select>      	
			  </div>
			</div>
			<div class="row">
			  <div class="col-3">
				<label class="col-form-label">Улица</label>
			  </div>
			  <div class="col-9">
					<select id="buildings-building-form-street_id" class="form-select"></select>      	
			  </div>
			</div>
			<div class="row">
			  <div class="col-3">
			    <label class="col-form-label">Номер</label>
			  </div>
			  <div class="col-2">
			    <input id="buildings-building-form-num" type="text" class="form-control">
			  </div>
			</div>
		</form>
      </div>
      <div class="modal-footer border-0">
        <button id="buildings-building-form-save" type="button" class="btn btn-success">Сохранить</button>
        <button id="buildings-building-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>
	function buildingsBuildingFormGetStreets(street_id = null, callback = null) {
		$('#buildings-building-form-street_id')
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

			let el = $('#buildings-building-form-street_id');
			el.append(html);

			if (street_id)
				el.val(street_id);

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function buildingsBuildingFormGetBlocks(block_id = null, callback = null) {
		$('#buildings-building-form-block_id')
			.prop('disabled', true)
			.html('<option value="0">- выберите двор -</option>')
			.val(0)
			.trigger('input');

		$.get('/blocks/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].num + '</option>';
			}

			let el = $('#buildings-building-form-block_id');
			el.append(html);

			if (block_id)
				el.val(block_id);

			el.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function buildingsBuildingFormGetExtra(building = null, callback = null) {
		var block_id_element = $('#buildings-building-form-block_id');
		var street_id_element = $('#buildings-building-form-street_id');
		block_id_element
			.prop('disabled', true)
			.html('<option value="0">- выберите двор -</option>')
			.val(0)
			.trigger('input');
		street_id_element
			.prop('disabled', true)
			.html('<option value="0">- выберите улицу -</option>')
			.val(0)
			.trigger('input');

		$.get('/blocks/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				const block_name = data.rows[i].name ? data.rows[i].name : "Двор"
				html += '<option value="' + data.rows[i].id + '">' + "№" + data.rows[i].num + " " + block_name +'</option>';
			}

			block_id_element.append(html);

			if (building)
				block_id_element.val(building.block_id);

			block_id_element.prop('disabled', false);
		}, 'json');

		$.get('/streets/', {
		}, (data) => {
			let html = '';
			for (let i in data.rows) {
				html += '<option value="' + data.rows[i].id + '">' + data.rows[i].title + '</option>';
			}

			let el = $('#buildings-building-form-street_id');
			street_id_element.append(html);

			if (building)
				street_id_element.val(building.street_id);

			street_id_element.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');

	}

	function buildingsBuildingFormReset(callback = null) {
		$('#buildings-building-form').data('id', null);
		buildingsBuildingFormGetExtra(null, callback);
	}

	function buildingsBuildingFormLoad(id, callback = null) {
		$('#buildings-building-form').data('id', id);

		$.get('/buildings/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			let row = data.rows[0];
			console.log(row);
		
			buildingsBuildingFormGetExtra(row, () => {
				$('#buildings-building-form-num')
					.val(row.num)
					.prop('disabled', false);

				if (callback instanceof Function)
					callback();
			});
		}, 'json');
	}

	function buildingsBuildingFormCreate() {
		$('#buildings-building-form .modal-title').html('Новый дом');

		buildingsBuildingFormReset(() => {
			buildingsBuildingForm.show();
		});
	}

	function buildingsBuildingFormEdit(id) {
		$('#buildings-building-form .modal-title').html('Дом #' + id);

		buildingsBuildingFormLoad(id, () => {
			buildingsBuildingForm.show();
		});
	}

	function buildingsBuildingFormValidate() {
		let num = parseInt($('#buildings-building-form-num').val());
		let ok = true;

		console.log(num)

		if (!num || num <= 0)
			ok = false;

		$('#buildings-building-form-save').prop('disabled', !ok);
	}

	var buildingsBuildingForm = null;

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		buildingsBuildingForm = new bootstrap.Modal('#buildings-building-form', {});

		$('#buildings-building-form-street_id').on('input', function() {
			let street_id = parseInt($(this).val());
			if (!street_id) {
				$('#buildings-building-form-num')
					.prop('disabled', true)
					.val('')
					.trigger('input');

				return;
			}

			$('#buildings-building-form-num')
				.val('')
				.prop('disabled', false)
				.trigger('input');
		});

		$('#buildings-building-form-num').on('input', function() {
			buildingsBuildingFormValidate();
		});
		
		$('#buildings-building-form-save').on('click', function() {
			let id = parseInt($('#buildings-building-form').data('id'));
			let street_id = parseInt($('#buildings-building-form-street_id').val());
			let block_id = parseInt($('#buildings-building-form-block_id').val());
			let num = parseInt($('#buildings-building-form-num').val());

			$('#buildings-building-form-street_id').prop('disabled', true);
			$('#buildings-building-form-num').prop('disabled', true);
			$('#buildings-building-form-save').prop('disabled', true);
			$('#buildings-building-form-cancel').prop('disabled', true);

			let data = {
				street_id: street_id,
				num: num,
				block_id: block_id
			};

			if (id)
				data.id = id;

			$.post('/buildings/save/', data, (data) => {
				if (!data.result) {
					if (Object.hasOwn(data, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(data, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });

				buildingsTable.reloadData();
			}, 'json').always(() => {
				$('#buildings-building-form-cancel').prop('disabled', false);

				buildingsBuildingForm.hide();
			});
		});
	});
</script>
