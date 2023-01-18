<div id="streets-street-form" class="modal" tabindex="-1">
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
					    <label class="col-form-label">Код</label>
					  </div>
					  <div class="col-2">
					    <input id="streets-street-form-code" type="text" class="form-control" maxlength="3">
					  </div>
					</div>
					<div class="row">
					  <div class="col-3">
					    <label class="col-form-label">Название</label>
					  </div>
					  <div class="col-9">
					    <input id="streets-street-form-title" type="text" class="form-control">
					  </div>
					</div>
				</form>
      </div>
      <div class="modal-footer border-0">
        <button id="streets-street-form-save" type="button" class="btn btn-success">Сохранить</button>
        <button id="streets-street-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>
	function streetsStreetFormReset(callback = null) {
		$('#streets-street-form').data('id', null);

		$('#streets-street-form-code')
			.val('')
			.trigger('input');

		$('#streets-street-form-title')
			.val('');

		if (callback instanceof Function)
			callback();
	}

	function streetsStreetFormLoad(id, callback = null) {
		$('#streets-street-form').data('id', id);

		$.get('/streets/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			let row = data.rows[0];
		
			$('#streets-street-form-code')
				.val(row.code);

			$('#streets-street-form-title')
				.val(row.title);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function streetsStreetFormCreate() {
		$('#streets-street-form .modal-title').html('Новая улица');

		streetsStreetFormReset(() => {
			streetsStreetForm.show();
		});
	}

	function streetsStreetFormEdit(id) {
		$('#streets-street-form .modal-title').html('Улица #' + id);

		streetsStreetFormLoad(id, () => {
			streetsStreetForm.show();
		});
	}

	function streetsStreetFormValidate() {
		let code = parseInt($('#streets-street-form-code').val().trim());
		let title = $('#streets-street-form-title').val().trim();
		let ok = true;

		if (!code || code < 0)
			ok = false;

		if (title.length < 1)
			ok = false;

		$('#streets-street-form-save').prop('disabled', !ok);
	}

	var streetsStreetForm = null;

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		streetsStreetForm = new bootstrap.Modal('#streets-street-form', {});

		$('#streets-street-form-code').on('input', function() {
			streetsStreetFormValidate();
		});
		
		$('#streets-street-form-title').on('input', function() {
			streetsStreetFormValidate();
		});

		$('#streets-street-form-save').on('click', function() {
			let id = parseInt($('#streets-street-form').data('id'));
			let code = parseInt($('#streets-street-form-code').val().trim());
			let title = $('#streets-street-form-title').val().trim();

			if (!code || code < 0)
				return;

			if (title.length < 1)
				return;

			code = code.toString();
			while (code.length < 3)
				code = '0' + code;

			$('#streets-street-form-code').prop('disabled', true);
			$('#streets-street-form-title').prop('disabled', true);
			$('#streets-street-form-save').prop('disabled', true);
			$('#streets-street-form-cancel').prop('disabled', true);

			let data = {
				code: code,
				title: title
			};

			if (id)
				data.id = id;

			$.post('/streets/save/', data, (data) => {				
				if (!data.result) {
					if (Object.hasOwn(data, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(data, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });

				streetsTable.reloadData();
			}, 'json').always(() => {
				$('#streets-street-form-code').prop('disabled', false);
				$('#streets-street-form-title').prop('disabled', false);
				$('#streets-street-form-cancel').prop('disabled', false);

				streetsStreetForm.hide();
			});
		});
	});
</script>
