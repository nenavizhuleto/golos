<script>
	class XuSelect extends XuElement {
		options = null;

		constructor(parent = null, config = {}) {
			super('select', parent, config);

			this.setClasses(['form-select']);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'enabled'))
				this.setEnabled(config.enabled);

			if (Object.hasOwn(config, 'options'))
				this.setOptions(config.options);
		}

		setEnabled(enabled) {
			if (!(this.element instanceof Node))
				return this;

			this.element.disabled = !enabled;

			return this;
		}

		setOptions(options) {
			if (!(this.element instanceof Node))
				return this;

			if (!(options instanceof Array))
				return this;

			// Removing old options
			this.removeChilds();

			// Appending new options
			options.forEach((option) => {
				if (!(option instanceof Object))
					return;

				let config = {};

				if (Object.hasOwn(option, 'value'))
					config.value = option.value;
				
				if (Object.hasOwn(option, 'content'))
					config.content = option.content;

				if (Object.hasOwn(option, 'selected'))
					config.selected = option.selected;

				new XuOption(this, config);
			}, this);

			this.options = options;

			return this;
		}

		setValue(value) {
			if (!(this.element instanceof Node))
				return this;

			this.element.value = value;

			return this;
		}

		getValue() {
			if (!(this.element instanceof Node))
				return null;

			return this.element.value;
		}
	}
</script>
