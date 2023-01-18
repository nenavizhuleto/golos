<script>
	class XuDataTable extends XuElement {
		head = null;
		headRow = null;
		headRowCheckbox = null;
		body = null;
		checkboxes = false;
		columns = [];
		rows = [];
		dataSource = null;
		dataMethod = null;
		dataKey = null;
		dataLimit = null;
		dataPage = null;

		filters = {};

		onDataLoaded = null;
		onCheckedKeysChanged = null;
		onRowClick = null;

		constructor(parent = null, config = {}) {
			super('table', parent, config);

			this.setClasses(['table', 'my-2' ]);

			this.head = new XuTableHead(this, { classes: ['table-secondary'] });
			this.headRow = new XuTableRow(this.head);
			this.body = new XuTableBody(this);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'checkboxes'))
				this.setCheckboxes(config.checkboxes);

			if (Object.hasOwn(config, 'columns'))
				this.setColumns(config.columns);

			if (Object.hasOwn(config, 'dataSource'))
				this.dataSource = config.dataSource;

			if (Object.hasOwn(config, 'dataMethod'))
				this.dataMethod = config.dataMethod;

			if (Object.hasOwn(config, 'dataKey'))
				this.dataKey = config.dataKey;

			if (Object.hasOwn(config, 'dataLimit')) {
				this.dataLimit = config.dataLimit;

				if (Object.hasOwn(config, 'dataPage'))
					this.dataPage = config.dataPage;
				else
					this.dataPage = 1;
			}

			if (Object.hasOwn(config, 'onDataLoaded'))
				this.onDataLoaded = config.onDataLoaded;

			if (Object.hasOwn(config, 'onCheckedKeysChanged'))
				this.onCheckedKeysChanged = config.onCheckedKeysChanged;

			if (Object.hasOwn(config, 'onRowClick'))
				this.onRowClick = config.onRowClick;
		}

		setCheckboxes(checkboxes) {
			this.checkboxes = checkboxes;
			this.rebuildColumns();

			return this;
		}

		setColumns(columns) {
			this.columns = columns;
			this.rebuildColumns();

			return this;
		}

		rebuildColumns() {
			if (!(this.headRow instanceof XuElement) || !(this.columns instanceof Array))
				return this;

			// Removing old childs
			this.headRow.removeChilds();

			// Appending new childs
			if (this.checkboxes) {
				let th = new XuTableHeadCell(this.headRow);
				this.headRowCheckbox = new XuCheckbox(th);

				// Updating body's row checkboxes
				this.headRowCheckbox.on('input', (event) => {
					this.body.childs.forEach((child) => {
						child.childs[0].childs[0].element.checked = this.headRowCheckbox.element.checked;
					}, this);

					if (this.onCheckedKeysChanged instanceof Function)
						this.onCheckedKeysChanged();
				});
			}

			this.columns.forEach((column) => {
				if (!(column instanceof Object))
					return;

				let config = {};
				if (Object.hasOwn(column, 'classes'))
					if (column.classes instanceof Array)
						config.classes = column.classes;

				if (Object.hasOwn(column, 'title'))
					if ('string' == typeof column.title)
						config.content = column.title;

				new XuTableHeadCell(this.headRow, config);
			}, this);

			this.rebuildRows();

			return this;
		}

		rebuildRows() {
			if (!(this.rows instanceof Array))
				return this;

			// Resetting headRowCheckbox flags
			if (this.checkboxes) {
				this.headRowCheckbox.setChecked(false);
				this.headRowCheckbox.setIndeterminate(false);
			}

			// Removing old rows
			this.body.removeChilds();

			// Appending new rows
			this.rows.forEach((row) => {
				if (!(row instanceof Object))
					return;

				var tr = new XuTableRow(this.body, { classes: ['xu-tablerow-link'] });

				if ('string' == typeof this.dataKey)
					if (Object.hasOwn(row, this.dataKey))
						tr.element.setAttribute('data-key', row[this.dataKey]);

				if (this.checkboxes) {
					let td = new XuTableCell(tr);
					let checkbox = new XuCheckbox(td);

					checkbox.on('click', (event) => {
						event.stopPropagation();
					});

					checkbox.on('input', (event) => {
						let key = null;
						if ('string' == typeof this.dataKey)
							if (Object.hasOwn(row, this.dataKey))
								key = row[this.dataKey];

						// Updating headRowCheckbox
						let total = 0;
						let checked = 0;
						this.body.childs.forEach((_row) => {
							total++;
							if (_row.childs[0].childs[0].element.checked)
								checked++;
						}, this);

						this.headRowCheckbox.setChecked((checked > 0) && (total == checked));
						this.headRowCheckbox.setIndeterminate((checked > 0) && (total != checked));

						//
						if (this.onCheckedKeysChanged instanceof Function)
							this.onCheckedKeysChanged();
					});
				}

				this.columns.forEach((column) => {
					let td = new XuTableCell(tr);
					if (Object.hasOwn(column, 'cellClasses'))
						if (column.cellClasses instanceof Array)
							td.setClasses(column.cellClasses);

					if (Object.hasOwn(column, 'name'))
						if ('string' == typeof column.name)
							if (Object.hasOwn(row, column.name))
								td.setContent(row[column.name]);
				});

				if (this.onRowClick instanceof Function)
					tr.on('click', (event) => {
						let key = null;
						if ('string' == typeof this.dataKey)
							if (Object.hasOwn(row, this.dataKey))
								key = row[this.dataKey];

						this.onRowClick(key);
					});
			}, this);

			return this;
		}

		reloadData() {
			if ('string' != typeof this.dataMethod)
				return this;

			if ('string' != typeof this.dataSource)
				return this;

			let method = this.dataMethod.toUpperCase();
			if (!['GET', 'POST'].includes(method))
				return this;

			let data = {
				filters: this.filters
			};

			if (this.dataLimit) {
				data.limit = this.dataLimit;
				data.page = this.dataPage;
			}

			$.ajax({
				url: this.dataSource,
				method: method,
				dataType: 'json',
				data: data,
				context: this
			}).done((data) => {
				if (!data.rows instanceof Array)
					return;

				this.rows = data.rows;
				this.rebuildRows();

				if (this.onDataLoaded instanceof Function)
					this.onDataLoaded(data);
			});
		}

		getCheckedKeys() {
			if (!this.checkboxes)
				return null;

			if ('string' != typeof this.dataKey)
				return null;

			if (!(this.body instanceof XuElement))
				return null;

			let result = [];
			this.body.childs.forEach((row) => {
				if (!row.childs[0].childs[0].element.checked)
					return;

				result.push(row.element.getAttribute('data-key'));
			}, this);

			return result;
		}
	}
</script>
