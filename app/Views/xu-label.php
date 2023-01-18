<script>
	class XuLabel extends XuElement {
		constructor(parent = null, config = {}) {
			super('label', parent, config);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'content'))
				this.setContent(config.content);
		}
	}
</script>
