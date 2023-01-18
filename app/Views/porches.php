<?php echo view('porches-porch-form'); ?>
<h4>Подъезды</h4>
<hr>
<div id="porches-toolbar-container"></div>
<div id="porches-table-container"></div>

<script>
	// Toolbar functions
	function porchesToolbarCreateButtonOnClick() {
		porchesPorchFormCreate();
	}
	
	function porchesToolbarEditButtonOnClick() {
		let keys = porchesTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		porchesPorchFormEdit(keys[0]);
	}

	function porchesToolbarDeleteButtonOnClick() {
		let keys = porchesTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено подъездов: ' + keys.length + '. Вы уверены?'))
			return;

		porchesTable.dataPage = 1;
		$.post('/porches/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			porchesTable.reloadData();
		}, 'json');
	}

	function porchesToolbarUpdate() {
		let checkedCount = porchesTable.getCheckedKeys().length;
		porchesToolbarEditButton.setEnabled(1 == checkedCount);
		porchesToolbarDeleteButton.setEnabled(checkedCount);
	}

	// Table functions
	function porchesTableOnDataLoaded(data) {
		porchesToolbarUpdate();
	}

	function porchesTableOnCheckedKeysChanged() {
		porchesToolbarUpdate();
	}

	function porchesTableOnRowClick(key) {
		porchesPorchFormEdit(key);
	}

	// Toolbar
	const porchesToolbar = new XuToolbar(null);
	const porchesToolbarButtonGroup1 = new XuButtonGroup(porchesToolbar);

	const porchesToolbarCreateButton = new XuButton(porchesToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать',
	}).on('click', porchesToolbarCreateButtonOnClick);

	const porchesToolbarEditButton = new XuButton(porchesToolbarButtonGroup1, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', porchesToolbarEditButtonOnClick);

	const porchesToolbarDeleteButton = new XuButton(porchesToolbarButtonGroup1, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', porchesToolbarDeleteButtonOnClick);

	// Table
	const porchesTable = new XuDataTable(null, {
		classes: ['table-sm', 'table-hover'],
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
				name: 'building_num',
				title: 'Дом',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap', 'text-end']
			}, {
				classes: ['w-100'],
				name: '',
				title: ''
			}
		],
		dataSource: '/porches/',
		dataMethod: 'GET',
		dataKey: 'id',
		onDataLoaded: (data) => porchesTableOnDataLoaded(data),
		onCheckedKeysChanged: () => porchesTableOnCheckedKeysChanged(),
		onRowClick: (key) => porchesTableOnRowClick(key)
	});

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('porches-toolbar-container').appendChild(porchesToolbar.element);
		document.getElementById('porches-table-container').appendChild(porchesTable.element);
	});
</script>
