<script>
	class XuOption extends XuElement {
		constructor(parent = null, config = {}) {
			super('option', parent, config);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'value'))
				this.setValue(config.value);

			if (Object.hasOwn(config, 'content'))
				this.setContent(config.content);
		}

		setValue(value) {
			if (!(this.element instanceof Node))
				return this;

			this.element.setAttribute('value', value);

			return this;
		}
	}
</script>
