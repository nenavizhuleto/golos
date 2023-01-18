<?php echo view('streets-street-form'); ?>
<h4>Улицы</h4>
<hr>
<div id="streets-toolbar-container"></div>
<div id="streets-table-container"></div>

<script>
	// Toolbar functions
	function streetsToolbarCreateButtonOnClick() {
		streetsStreetFormCreate();
	}
	
	function streetsToolbarEditButtonOnClick() {
		let keys = streetsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		streetsStreetFormEdit(keys[0]);
	}

	function streetsToolbarDeleteButtonOnClick() {
		let keys = streetsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено улиц: ' + keys.length + '. Вы уверены?'))
			return;

		streetsTable.dataPage = 1;
		$.post('/streets/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			streetsTable.reloadData();
		}, 'json');
	}

	function streetsToolbarUpdate() {
		let checkedCount = streetsTable.getCheckedKeys().length;
		streetsToolbarEditButton.setEnabled(1 == checkedCount);
		streetsToolbarDeleteButton.setEnabled(checkedCount);
	}

	// Table functions
	function streetsTableOnDataLoaded(data) {
		streetsToolbarUpdate();
	}

	function streetsTableOnCheckedKeysChanged() {
		streetsToolbarUpdate();
	}

	function streetsTableOnRowClick(key) {
		streetsStreetFormEdit(key);
	}

	// Toolbar
	const streetsToolbar = new XuToolbar(null);
	const streetsToolbarButtonGroup1 = new XuButtonGroup(streetsToolbar);

	const streetsToolbarCreateButton = new XuButton(streetsToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать',
	}).on('click', streetsToolbarCreateButtonOnClick);

	const streetsToolbarEditButton = new XuButton(streetsToolbarButtonGroup1, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', streetsToolbarEditButtonOnClick);

	const streetsToolbarDeleteButton = new XuButton(streetsToolbarButtonGroup1, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', streetsToolbarDeleteButtonOnClick);

	// Table
	const streetsTable = new XuDataTable(null, {
		classes: ['table-sm', 'table-hover'],
		checkboxes: true,
		columns: [
			{
				name: 'code',
				title: 'Код',
				cellClasses: ['text-end']
			}, {
				name: 'title',
				title: 'Название',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				classes: ['w-100'],
				name: '',
				title: ''
			}
		],
		dataSource: '/streets/',
		dataMethod: 'GET',
		dataKey: 'id',
		onDataLoaded: (data) => streetsTableOnDataLoaded(data),
		onCheckedKeysChanged: () => streetsTableOnCheckedKeysChanged(),
		onRowClick: (key) => streetsTableOnRowClick(key)
	});

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('streets-toolbar-container').appendChild(streetsToolbar.element);
		document.getElementById('streets-table-container').appendChild(streetsTable.element);
	});
</script>
