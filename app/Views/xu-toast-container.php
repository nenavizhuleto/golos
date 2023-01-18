<script>
		class XuToastContainer extends XuDiv {
			body = null;

			constructor(parent = null, config = {}) {
				super(parent, config);

				this.addClass('toast-container');
			}
		}
</script>
