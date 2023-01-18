<script>
	class XuPager extends XuDiv {
		limitsSelect = null;
		pagesButtonGroup = null;
		totalPages = null;
		page = 1;
		neighbours = 3;
		onLimitChanged = null;
		onPageChanged = null;

		constructor(parent = null, config = {}) {
			super(parent, config);

			this.setClasses(['row', 'g-2', 'mb-2']);

			let col1 = new XuDiv(this, { classes: ['col-auto'] });
			let col1Label = new XuLabel(col1, { classes: ['col-form-label'], content: 'Результатов на страницу:' });
			let col2 = new XuDiv(this, { classes: ['col-auto'] });
			this.limitsSelect = new XuSelect(col2);
			let col3 = new XuDiv(this, { classes: ['col-auto'] });
			this.pagesButtonGroup = new XuButtonGroup(col3);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'limits'))
				this.setLimits(config.limits);

			if (Object.hasOwn(config, 'limit'))
				this.setLimit(config.limit);
			else
				this.setLimit(0);

			if (Object.hasOwn(config, 'totalPages'))
				this.setTotalPages(config.totalPages);

			if (Object.hasOwn(config, 'page'))
				this.setPage(config.page);

			if (Object.hasOwn(config, 'neighbours'))
				this.setNeighbours(config.neighbours);

			if (Object.hasOwn(config, 'onLimitChanged')) {
				if (config.onLimitChanged instanceof Function) {
					this.limitsSelect.element.addEventListener('input', (event) => config.onLimitChanged(this.getLimit()));
					this.onLimitChanged = config.onLimitChanged;
				}
			}

			if (Object.hasOwn(config, 'onPageChanged'))
				this.onPageChanged = config.onPageChanged;
		}

		setLimits(limits) {
			if (!(this.limitsSelect instanceof XuSelect))
				return this;

			if (!(limits instanceof Array))
				return this;

			let options = [];
			limits.forEach((limit) => {
				if ('number' != typeof(limit))
					return;

				options.push({ value: limit, content: limit });
			}, this);

			this.limitsSelect.setOptions(options);

			return this;
		}

		setLimit(limit) {
			if (!(this.limitsSelect instanceof XuSelect))
				return this;

			if ('number'!= typeof(limit))
				return this;

			this.limitsSelect.setValue(limit);

			return this;
		}

		getLimit() {
			if (!(this.limitsSelect instanceof XuSelect))
				return null;

			return parseInt(this.limitsSelect.getValue());
		}

		setTotalPages(totalPages) {
			if ('number' != typeof(totalPages))
				return this;

			this.totalPages = totalPages;
			this.rebuildPages();

			return this;
		}

		setPage(page) {
			if ('number' != typeof(page))
				return this;

			this.page = page;
			this.rebuildPages();

			return this;
		}

		getPage() {
			return this.page;
		}

		setNeighbours(neighbours) {
			if ('number' != typeof(neighbours))
				return this;

			this.neighbours = neighbours;
			this.rebuildPages();

			return this;
		}

		rebuildPages() {
			if (!(this.pagesButtonGroup instanceof XuButtonGroup))
				return this;

			if ('number' != typeof(this.neighbours))
				return this;

			if ('number' != typeof(this.totalPages))
				return this;

			if ('number' != typeof(this.page))
				return this;

			// Removing old page buttons
			this.pagesButtonGroup.removeChilds();

			// Appending new page buttons
			if (!this.getLimit())
				return this;

			for (let i = 1; i <= this.totalPages; i++) {
				// page
				let visible = true;
				if (this.neighbours > 0) {
					if ((i > this.neighbours) && (i < this.page - this.neighbours))
						visible = false;

					if ((i <= this.totalPages - this.neighbours) && (i > this.page + this.neighbours))
						visible = false;
				}

				if (visible) {
					let page_id = 'page-' + Math.random().toString();
					let config = {};
					if (this.page == i)
						config.checked = true;

					let radio = new XuRadio(this.pagesButtonGroup, config);
					radio.element.setAttribute('id', page_id);
					radio.element.addEventListener('click', (event) => {
						this.page = i;
						this.rebuildPages();

						if (this.onPageChanged instanceof Function)
							this.onPageChanged(this.getPage());
					});

					let label = new XuLabel(this.pagesButtonGroup, { classes: ['btn', 'btn-outline-primary'], content: i });
					label.element.setAttribute('for', page_id);
				}

				// placeholder
				var placeholder = false;
				if (this.neighbours > 0) {
					if ((i > this.neighbours) && (i < this.page - this.neighbours))
						if (i == this.neighbours + 1)
							placeholder = true;

					if ((i <= this.totalPages - this.neighbours) && (i > this.page + this.neighbours))
						if (i == this.totalPages - this.neighbours)
							placeholder = true;
				}

				if (placeholder) {
					new XuRadio(this.pagesButtonGroup, { enabled: false });
					new XuLabel(this.pagesButtonGroup, { classes: ['btn', 'btn-outline-secondary'], content: '...' });
				}
			}

			return this;
		}
	}
</script>
