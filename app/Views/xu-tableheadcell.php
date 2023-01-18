<script>
	class XuTableHeadCell extends XuElement {
		content = null;

		constructor(parent = null, config = {}) {
			super('th', parent, config);

			this.setClasses(['px-2']);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'content'))
				this.setContent(config.content);
		}
	}
</script>
