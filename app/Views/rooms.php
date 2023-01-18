<?php echo view('rooms-room-form'); ?>
<?php echo view('rooms-generate-form'); ?>
<h4>Квартиры</h4>
<hr>
<div id="rooms-toolbar-container"></div>
<div id="rooms-pager-container"></div>
<div id="rooms-table-container"></div>

<script>
	// Toolbar functions
	function roomsToolbarUpdate() {
		let checkedCount = roomsTable.getCheckedKeys().length;
		roomsToolbarEditButton.setEnabled(1 == checkedCount);
		roomsToolbarDeleteButton.setEnabled(checkedCount);
	}

	function roomsToolbarGenerateButtonOnClick() {
		roomsGenerateFormShow();
	}

	function roomsToolbarCreateButtonOnClick() {
		roomsRoomFormCreate();
	}

	function roomsToolbarEditButtonOnClick() {
		let keys = roomsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		roomsRoomFormEdit(keys[0]);
	}

	function roomsToolbarDeleteButtonOnClick() {
		let keys = roomsTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено квартир: ' + keys.length + '. Вы уверены?'))
			return;

		roomsTable.dataPage = 1;
		$.post('/rooms/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			roomsTable.reloadData();
		}, 'json');
	}

	// Pager function
	function roomsPagerOnLimitChanged(limit) {
		roomsTable.dataLimit = limit;
		roomsTable.dataPage = 1;
		roomsTable.reloadData();
	}

	function roomsPagerOnPageChanged(page) {
		roomsTable.dataPage = page;
		roomsTable.reloadData();
	}

	// Table functions
	function roomsTableOnDataLoaded(data) {
		roomsToolbarUpdate();
		roomsPager.setTotalPages(data.pageCount);
		roomsPager.setPage(data.page);
	}

	function roomsTableOnCheckedKeysChanged() {
		roomsToolbarUpdate();
	}

	function roomsTableOnRowClick(key) {
		roomsRoomFormEdit(key);
	}

	// Toolbar
	const roomsToolbar = new XuToolbar(null);
	const roomsToolbarButtonGroup1 = new XuButtonGroup(roomsToolbar);

	const roomsToolbarGenerateButton = new XuButton(roomsToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Генерировать'
	}).on('click', roomsToolbarGenerateButtonOnClick);

	const roomsToolbarButtonGroup2 = new XuButtonGroup(roomsToolbar);

	const roomsToolbarCreateButton = new XuButton(roomsToolbarButtonGroup2, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать'
	}).on('click', roomsToolbarCreateButtonOnClick);

	const roomsToolbarEditButton = new XuButton(roomsToolbarButtonGroup2, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', roomsToolbarEditButtonOnClick);

	const roomsToolbarDeleteButton = new XuButton(roomsToolbarButtonGroup2, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', roomsToolbarDeleteButtonOnClick);

	// Pager
	const roomsPager = new XuPager(null, {
		limits: [ 10, 25, 50, 100 ],
		limit: 10,
		neighbours: 2,
		onLimitChanged: (limit) => roomsPagerOnLimitChanged(limit),
		onPageChanged: (page) => roomsPagerOnPageChanged(page)
	});

	// Table
	const roomsTable = new XuDataTable(null, {
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
				name: 'porch_num',
				title: 'Подъезд',
				cellClasses: ['text-end']
			}, {
				classes: ['w-100'],
				name: '',
				title: ''
			}
		],
		dataSource: '/rooms/',
		dataMethod: 'GET',
		dataKey: 'id',
		dataLimit: 10,
		onDataLoaded: (data) => roomsTableOnDataLoaded(data),
		onCheckedKeysChanged: () => roomsTableOnCheckedKeysChanged(),
		onRowClick: (key) => roomsTableOnRowClick(key)
	});

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('rooms-toolbar-container').appendChild(roomsToolbar.element);
		document.getElementById('rooms-pager-container').appendChild(roomsPager.element);
		document.getElementById('rooms-table-container').appendChild(roomsTable.element);
	});
</script>
