<?php echo view('devices-device-form'); ?>
<h4>Устройства</h4>
<hr>
<div id="devices-toolbar-container"></div>
<div id="devices-pager-container"></div>
<div id="devices-table-container"></div>

<style>
	#devices-table-container thead th {
		vertical-align:top;
	}
</style>

<script>
	// TODO: sip prune peer
	// TODO: dialplan reload

	// Toolbar functions
	function devicesToolbarCreateButtonOnClick() {
		// new XuToast(toastContainer, { classes: ['text-bg-primary'], text: 'Создание устройств временно недоступно.', timeout: 8000 });
		devicesDeviceFormCreate();
	}
	
	function devicesToolbarEditButtonOnClick() {
		let keys = devicesTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		devicesDeviceFormEdit(keys[0]);
	}

	function devicesToolbarDeleteButtonOnClick() {
		let keys = devicesTable.getCheckedKeys();
		if (keys.length < 1)
			return;

		if (!confirm('Будет удалено устройств: ' + keys.length + '. Вы уверены?'))
			return;

		devicesTable.dataPage = 1;
		$.post('/devices/delete/', {
			ids: keys
		}, (data) => {
			if (Object.hasOwn(data, 'message'))
				if (data.result)
					new XuToast(toastContainer, { classes: ['text-bg-success'], text: data.message });
				else
					new XuToast(toastContainer, { classes: ['text-bg-danger'], text: data.message, timeout: 6000 });

			devicesTable.reloadData();
		}, 'json');
	}

	function devicesToolbarUpdate() {
		let checkedCount = devicesTable.getCheckedKeys().length;
		devicesToolbarEditButton.setEnabled(1 == checkedCount);
		devicesToolbarDeleteButton.setEnabled(checkedCount);
	}

	// Pager function
	function devicesPagerOnLimitChanged(limit) {
		devicesTable.dataLimit = limit;
		devicesTable.dataPage = 1;
		devicesTable.reloadData();
	}

	function devicesPagerOnPageChanged(page) {
		devicesTable.dataPage = page;
		devicesTable.reloadData();
	}

	// Table functions
	function devicesTableOnDataLoaded(data) {
		devicesToolbarUpdate();
		devicesPager.setTotalPages(data.pageCount);
		devicesPager.setPage(data.page);
	}

	function devicesTableOnCheckedKeysChanged() {
		devicesToolbarUpdate();
	}

	function devicesTableOnRowClick(key) {
		devicesDeviceFormEdit(key);
	}

	// Toolbar
	const devicesToolbar = new XuToolbar(null);
	const devicesToolbarButtonGroup1 = new XuButtonGroup(devicesToolbar);

	const devicesToolbarCreateButton = new XuButton(devicesToolbarButtonGroup1, {
		classes: ['btn-success', 'btn-sm'],
		content: 'Создать',
	}).on('click', devicesToolbarCreateButtonOnClick);

	const devicesToolbarEditButton = new XuButton(devicesToolbarButtonGroup1, {
		classes: ['btn-primary', 'btn-sm'],
		content: 'Изменить',
		enabled: false
	}).on('click', devicesToolbarEditButtonOnClick);

	const devicesToolbarDeleteButton = new XuButton(devicesToolbarButtonGroup1, {
		classes: ['btn-danger', 'btn-sm'],
		content: 'Удалить',
		enabled: false
	}).on('click', devicesToolbarDeleteButtonOnClick);

	// Pager
	const devicesPager = new XuPager(null, {
		limits: [ 10, 25, 50, 100 ],
		limit: 10,
		neighbours: 2,
		onLimitChanged: (limit) => devicesPagerOnLimitChanged(limit),
		onPageChanged: (page) => devicesPagerOnPageChanged(page)
	});

	// Table
	const devicesTable = new XuDataTable(null, {
		classes: ['table-sm', 'table-hover'],
		checkboxes: true,
		columns: [
			{
				name: 'phone',
				title: 'Полный номер',
				classes:  ['text-nowrap'],
				cellClasses: ['text-noerap']
			}, {
				name: 'type_name',
				title: 'Тип',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				name: 'num',
				title: '№',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap', 'text-end']
			}, {
				name: 'objtype',
				title: 'Тип объекта',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				name: 'obj',
				title: 'Объект',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				name: 'sipusername',
				title: 'Пользователь SIP',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				name: 'sippassword',
				title: 'Пароль SIP',
				classes:  ['text-nowrap'],
				cellClasses: ['text-nowrap']
			}, {
				classes: ['w-100'],
				name: 'comment',
				title: 'Комментарий'
			}
		],
		dataSource: '/devices/',
		dataMethod: 'GET',
		dataKey: 'id',
		dataLimit: 10,

		onDataLoaded: (data) => devicesTableOnDataLoaded(data),
		onCheckedKeysChanged: () => devicesTableOnCheckedKeysChanged(),
		onRowClick: (key) => devicesTableOnRowClick(key)
	});

	// DOMContentLoaded
	window.addEventListener('DOMContentLoaded', (event) => {
		document.getElementById('devices-toolbar-container').appendChild(devicesToolbar.element);
		document.getElementById('devices-pager-container').appendChild(devicesPager.element);
		document.getElementById('devices-table-container').appendChild(devicesTable.element);

		// altering devicesTable
		let devicesTableFilterComment = document.createElement('input');
		devicesTableFilterComment.setAttribute('id', 'devices-table-filter-comment');
		devicesTableFilterComment.setAttribute('type', 'text');
		devicesTableFilterComment.classList.add('form-control');
		devicesTableFilterComment.addEventListener('input', (event) => {
			if (0 == event.target.value.length)
				devicesTable.filters.comment = null;
			else
				devicesTable.filters.comment = event.target.value;

			devicesTable.reloadData();
		});

		devicesTable.childs[0].childs[0].childs[8].element.appendChild(devicesTableFilterComment);
	});
</script>
