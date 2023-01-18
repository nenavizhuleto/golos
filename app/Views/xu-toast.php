<script>
		class XuToast extends XuDiv {
			body = null;
			timeout = 5000;

			constructor(parent = null, config = {}) {
				super(parent, config);

				this.addClass('toast');
				this.addClass('d-block');
				this.addClass('mb-2');

				this.body = new XuDiv(this, { classes: ['toast-body'] });

				if (!(config instanceof Object))
					return;

				if (Object.hasOwn(config, 'text'))
					this.body.setContent(config.text);

				if (Object.hasOwn(config, 'timeout'))
					this.timeout = config.timeout;

				this.on('click', () => {
					this.element.remove();
				},);

				if (this.timeout > 0)
					setTimeout(function(context) {
						context.element.remove();
					}, this.timeout, this);
			}
		}
</script>
