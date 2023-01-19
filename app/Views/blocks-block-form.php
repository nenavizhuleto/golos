<div id="blocks-block-form" class="modal" tabindex="-1">
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
			<label class="col-form-label">Номер двора</label>
		  </div>
		  <div class="col-2">
		    <input id="blocks-block-form-num" type="text" class="form-control">
		  </div>
		</div>
		<div class="row">
		  <div class="col-3">
			<label class="col-form-label">Название двора</label>
		  </div>
		  <div class="col-9">
		    <input id="blocks-block-form-name" type="text" class="form-control">
		  </div>
		</div>
	</form>
      </div>
      <div class="modal-footer border-0">
        <button id="blocks-block-form-save" type="button" class="btn btn-success">Сохранить</button>
        <button id="blocks-block-form-cancel" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<script>

	function blocksBlockFormReset(callback = null) {
		$('#blocks-block-form').data('id', null);

		$('#blocks-block-form-num').val('');
		$('#blocks-block-form-name').val('');

		// buildingsBuildingFormGetStreets(null, callback);
		if (callback instanceof Function)
				callback();
	}

	function blocksBlockFormLoad(id, callback = null) {
		$('#blocks-block-form').data('id', id);

		$.get('/blocks/', {
			id: id
		}, (data) => {
			if (!data.result)
				return;

			if (!data.rows.length)
				return;

			//
			let row = data.rows[0];
		
			$('#blocks-block-form-num')
				.val(row.num)
				.prop('disabled', false);
			$('#blocks-block-form-name')
				.val(row.name)
				.prop('disabled', false);

			if (callback instanceof Function)
				callback();
		}, 'json');
	}

	function blocksBlockFormCreate() {
		$('#blocks-block-form .modal-title').html('Новый двор');

		blocksBlockFormReset(() => {
			blocksBlockForm.show();
		});
	}

	function blocksBlockFormEdit(id) {
		$('#blocks-block-form .modal-title').html('Двор #' + id);

		blocksBlockFormLoad(id, () => {
			blocksBlockForm.show();
		});
	}

	function blocksBlockFormValidate() {
		let num = parseInt($('#blocks-block-form-num').val());
		let ok = true;

		if (!num || num <= 0)
			ok = false;

		$('#blocks-block-form-save').prop('disabled', !ok);
	}

	var blocksBlockForm = null;

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		blocksBlockForm = new bootstrap.Modal('#blocks-block-form', {});

		$('#blocks-block-form-num').on('input', function() {
			blocksBlockFormValidate();
		});
		
		$('#blocks-block-form-save').on('click', function() {
			let id = parseInt($('#blocks-block-form').data('id'));
			let num = parseInt($('#blocks-block-form-num').val());
			let name = $('#blocks-block-form-name').val();

			$('#blocks-block-form-name').prop('disabled', true);
			$('#blocks-block-form-num').prop('disabled', true);
			$('#blocks-block-form-save').prop('disabled', true);
			$('#blocks-block-form-cancel').prop('disabled', true);

			let data = {
				num: num,
				name: name
			};

			if (id)
				data.id = id;

			$.post('/blocks/save/', data, (data) => {
				if (!data.result) {
					if (Object.hasOwn(data, 'message'))
						new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 8000 });

					return;
				}

				if (Object.hasOwn(data, 'message'))
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });

				blocksTable.reloadData();
			}, 'json').always(() => {
				$('#blocks-block-form-cancel').prop('disabled', false);

				blocksBlockForm.hide();
			});
		});
	});
</script>
