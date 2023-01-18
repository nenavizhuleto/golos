<script>
	class XuCheckbox extends XuElement {
		constructor(parent = null, config = {}) {
			super('input', parent, config);

			this.element.setAttribute('type', 'checkbox');
			this.setClasses(['form-check-input']);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'enabled'))
				this.setEnabled(config.enabled);

			if (Object.hasOwn(config, 'checked'))
				this.setChecked(config.checked);

			if (Object.hasOwn(config, 'indeterminate'))
				this.setIndeterminate(config.indeterminate);

			if (Object.hasOwn(config, 'value'))
				this.setValue(config.value);
		}

		setEnabled(enabled) {
			if (!(this.element instanceof Node))
				return this;

			this.element.disabled = !enabled;

			return this;
		}

		setChecked(checked) {
			if (!(this.element instanceof Node))
				return this;

			this.element.checked = checked;

			return this;
		}

		setIndeterminate(indeterminate) {
			if (!(this.element instanceof Node))
				return this;

			this.element.indeterminate = indeterminate;

			return this;
		}

		setValue(value) {
			if (!(this.element instanceof Node))
				return this;

			this.element.setAttribute('value', value);

			return this;
		}
	}
</script>
