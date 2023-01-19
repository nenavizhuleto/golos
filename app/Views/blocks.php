<?php echo view('blocks-block-form'); ?>
<h4>Дворы</h4>
<hr>
<div id="blocks-toolbar-container"></div>
<div id="blocks-table-container"></div>

<script>
	// Toolbar functions
	function blocksToolbarCreateButtonOnClick() {
		// new XuToast(toastContainer, { classes: ['text-bg-primary'], text: 'Создание дворов временно недоступно.', timeout: 8000 });
		blocksBlockFormCreate();
	}
	
	function blocksToolbarEditButtonOnClick() {
		// new XuToast(toastContainer, { classes: ['text-bg-primary'], text: 'Редактирование дворов временно недоступно.', timeout: 8000 });
		let keys = blocksTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		blocksBlockFormEdit(keys[0]);
	}

	function blocksToolbarDeleteButtonOnClick() {
		// new XuToast(toastContainer, { classes: ['text-bg-primary'], text: 'Удаление дворов временно недоступно.', timeout: 8000 });
		// return;
		let keys = blocksTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено дворов: ' + keys.length + '. Вы уверены?'))
			return;

		blocksTable.dataPage = 1;
		$.post('/blocks/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			blocksTable.reloadData();
		}, 'json');

		// TODO: delete me after debug
		blocksTable.reloadData();
	}

	function blocksToolbarUpdate() {
		let checkedCount = blocksTable.getCheckedKeys().length;
		blocksToolbarEditButton.setEnabled(1 == checkedCount);
		blocksToolbarDeleteButton.setEnabled(checkedCount);
	}

	// Table functions
	function blocksTableOnDataLoaded(data) {
		blocksToolbarUpdate();
	}

	function blocksTableOnCheckedKeysChanged() {
		blocksToolbarUpdate();
	}

	function blocksTableOnRowClick(key) {
		// new XuToast(toastContainer, { classes: ['text-bg-primary'], text: 'Редактирование дворов временно недоступно.', timeout: 8000 });
		blocksBlockFormEdit(key);
	}

	// Toolbar
	const blocksToolbar = new XuToolbar(null);
	const blocksToolbarButtonGroup1 = new XuButtonGroup(blocksToolbar);

	const blocksToolbarCreateButton = new XuButton(blocksToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать',
	}).on('click', blocksToolbarCreateButtonOnClick);

	const blocksToolbarEditButton = new XuButton(blocksToolbarButtonGroup1, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', blocksToolbarEditButtonOnClick);

	const blocksToolbarDeleteButton = new XuButton(blocksToolbarButtonGroup1, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', blocksToolbarDeleteButtonOnClick);

	// Table
	const blocksTable = new XuDataTable(null, {
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
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				name: 'name',
				title: 'Название',
				classes: ['text-nowrap'],
				cellClasses: ['text-nowrap']
			},
			{
				name: 'buildings_string',
				title: 'Дома',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				classes: ['w-100'],
				name: '',
				title: ''
			}
		],
		dataSource: '/blocks/',
		dataMethod: 'GET',
		dataKey: 'id',
		onDataLoaded: (data) => blocksTableOnDataLoaded(data),
		onCheckedKeysChanged: () => blocksTableOnCheckedKeysChanged(),
		onRowClick: (key) => blocksTableOnRowClick(key)
	});

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('blocks-toolbar-container').appendChild(blocksToolbar.element);
		document.getElementById('blocks-table-container').appendChild(blocksTable.element);
	});
</script>
