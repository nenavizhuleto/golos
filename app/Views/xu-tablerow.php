<style>
	.xu-tablerow-link {
		cursor:pointer;
	}
</style>

<script>
	class XuTableRow extends XuElement {
		constructor(parent = null, config = {}) {
			super('tr', parent, config);
		}
	}
</script>