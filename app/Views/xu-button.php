<script>
	class XuButton extends XuElement {
		constructor(parent = null, config = {}) {
			super('button', parent, config);

			this.setClasses(['btn']);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'content'))
				this.setContent(config.content);

			if (Object.hasOwn(config, 'enabled'))
				this.setEnabled(config.enabled);
		}

		setEnabled(enabled) {
			if (!(this.element instanceof Node))
				return this;

			this.element.disabled = !enabled;

			return this;
		}
	}
</script>
