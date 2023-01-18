<style>
	#config-sip_custom, #config-extensions_custom {
		font-family: 'Courier New';
		font-size:12px;
		font-weight:bold;
		white-space:nowrap;
	}
</style>

<h5>Конфигурация</h5>
<hr>
<div class="container-fluid px-1">
	<div class="row">
		<div class="col-6">
			<label class="col-form-label pt-0">sip_custom.conf</label>
			<textarea id="config-sip_custom" class="form-control" rows="8"></textarea>
		</div>
		<div class="col-6">
			<label class="col-form-label pt-0">extensions_custom.conf</label>
			<textarea id="config-extensions_custom" class="form-control" rows="8"></textarea>
		</div>
		<div class="col-12 text-end">
			<button id="config-save-button" class="btn btn-primary mt-2">Сохранить</button>
		</div>
	</div>
</div>

<script>
	function configSave() {
		let peers = $('#config-sip_custom').val();
		let extensions = $('#config-extensions_custom').val();

		$('#config-save-button').prop('disabled', true);
		$('#config-sip_custom').prop('disabled', true);
		$('#config-extensions_custom').prop('disabled', true);

		$.post('/config/save/', {
			peers: peers,
			extensions: extensions
		}, (response) => {
			if (Object.hasOwn(response, 'message'))
				if (response.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: response.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: response.message, timeout: 6000 });
		}, 'json').always(() => {
			$('#config-sip_custom').prop('disabled', false);
			$('#config-extensions_custom').prop('disabled', false);
		});
	}

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		$('#config-save-button').prop('disabled', true);

		$.get('/config/', {}, (response) => {
			$('#config-sip_custom').val(response.peers);
			$('#config-extensions_custom').val(response.extensions);
		}, 'json');

		$('#config-sip_custom').on('input', () => {
			$('#config-save-button').prop('disabled', false);
		});

		$('#config-extensions_custom').on('input', () => {
			$('#config-save-button').prop('disabled', false);
		});

		$('#config-save-button').on('click', () => {
			configSave();
		});
	});

</script>
