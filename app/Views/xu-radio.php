<script>
	class XuRadio extends XuElement {
		constructor(parent = null, config = {}) {
			super('input', parent, config);

			this.element.setAttribute('type', 'radio');
			this.setClasses(['btn-check']);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'enabled'))
				this.setEnabled(config.enabled);

			if (Object.hasOwn(config, 'checked'))
				this.setChecked(config.checked);

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

		setValue(value) {
			if (!(this.element instanceof Node))
				return this;

			this.element.setAttribute('value', value);

			return this;
		}
	}
</script>
