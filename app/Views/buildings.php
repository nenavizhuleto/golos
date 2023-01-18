<?php echo view('buildings-building-form'); ?>
<h4>Дома</h4>
<hr>
<div id="buildings-toolbar-container"></div>
<div id="buildings-table-container"></div>

<script>
	// Toolbar functions
	function buildingsToolbarCreateButtonOnClick() {
		buildingsBuildingFormCreate();
	}
	
	function buildingsToolbarEditButtonOnClick() {
		let keys = buildingsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		buildingsBuildingFormEdit(keys[0]);
	}

	function buildingsToolbarDeleteButtonOnClick() {
		let keys = buildingsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено улиц: ' + keys.length + '. Вы уверены?'))
			return;

		buildingsTable.dataPage = 1;
		$.post('/buildings/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			buildingsTable.reloadData();
		}, 'json');
	}

	function buildingsToolbarExportExcelButtonOnClick() {
		let keys = buildingsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		location.href = '/buildings/export/?' + $.param({
			format: 'excel',
			ids: keys
		});
	}

	function buildingsToolbarExportCSVButtonOnClick() {
		let keys = buildingsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		location.href = '/buildings/export/?' + $.param({
			format: 'csv',
			ids: keys
		});
	}

	function buildingsToolbarUpdate() {
		let checkedCount = buildingsTable.getCheckedKeys().length;
		buildingsToolbarEditButton.setEnabled(1 == checkedCount);
		buildingsToolbarDeleteButton.setEnabled(checkedCount);
		buildingsToolbarExportExcelButton.setEnabled(checkedCount);
		buildingsToolbarExportCSVButton.setEnabled(checkedCount);
	}

	// Table functions
	function buildingsTableOnDataLoaded(data) {
		buildingsToolbarUpdate();
	}

	function buildingsTableOnCheckedKeysChanged() {
		buildingsToolbarUpdate();
	}

	function buildingsTableOnRowClick(key) {
		buildingsBuildingFormEdit(key);
	}

	// Toolbar
	const buildingsToolbar = new XuToolbar(null, { 'classes': ['mb-2'] });
	const buildingsToolbarButtonGroup1 = new XuButtonGroup(buildingsToolbar, { classes: ['me-2'] });
	const buildingsToolbarButtonGroup2 = new XuButtonGroup(buildingsToolbar, { classes: ['me-2'] });

	const buildingsToolbarCreateButton = new XuButton(buildingsToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать',
	}).on('click', buildingsToolbarCreateButtonOnClick);

	const buildingsToolbarEditButton = new XuButton(buildingsToolbarButtonGroup1, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', buildingsToolbarEditButtonOnClick);

	const buildingsToolbarDeleteButton = new XuButton(buildingsToolbarButtonGroup1, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', buildingsToolbarDeleteButtonOnClick);

	// Group2
	const buildingsToolbarExportExcelButton = new XuButton(buildingsToolbarButtonGroup2, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Экспорт в Excel',
		enabled: false
	}).on('click', buildingsToolbarExportExcelButtonOnClick);

	const buildingsToolbarExportCSVButton = new XuButton(buildingsToolbarButtonGroup2, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Экспорт в CSV',
		enabled: false
	}).on('click', buildingsToolbarExportCSVButtonOnClick);

	// Table
	const buildingsTable = new XuDataTable(null, {
		classes: ['table-sm', 'table-hover', 'my-2'],
		checkboxes: true,
		columns: [
			{
				name: 'code',
				title: 'Код',
				cellClasses: ['text-end']
			}, {
				name: 'num',
				title: '№',
				cellClasses: ['text-end']
			}, {
				name: 'street_title',
				title: 'Улица',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				classes: ['w-100'],
				name: '',
				title: ''
			}
		],
		dataSource: '/buildings/',
		dataMethod: 'GET',
		dataKey: 'id',
		dataLimit: 10,
		onDataLoaded: (data) => buildingsTableOnDataLoaded(data),
		onCheckedKeysChanged: () => buildingsTableOnCheckedKeysChanged(),
		onRowClick: (key) => buildingsTableOnRowClick(key)

	});


	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('buildings-toolbar-container').appendChild(buildingsToolbar.element);
		document.getElementById('buildings-table-container').appendChild(buildingsTable.element);
	});
</script>
