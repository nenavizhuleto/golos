<script>
	class XuElement {
		type = null;
		element = null;
		parent = null; // XuElement
		childs = []; // XuElement[]
		id = null;
		classes = [];
		
		constructor(type, parent = null, config = {}) {
			if ('string' != typeof type)
				return;

			this.type = type;
			this.element = document.createElement(this.type);

			if (!(config instanceof Object))
				return;

			if (Object.hasOwn(config, 'id'))
				if ('string' == typeof(config.id)) {
					this.element.setAttribute('id', config.id);
					this.id = config.id;
				}

			if (Object.hasOwn(config, 'classes'))
				this.setClasses(config.classes);

			if (parent instanceof XuElement)
				this.setParent(parent);
		}

		setParent(parent) {
			if (!(this.element instanceof Node))
				return this;

			if (!(parent instanceof XuElement))
				return this;

			// Detaching from old parent if it exists
			if (this.parent instanceof XuElement)
				this.parent.removeChild(this);

			parent.addChild(this);
			this.parent = parent;

			return this;
		}

		addChild(child) {
			if (!(this.element instanceof Node))
				return this;

			if (!(child instanceof XuElement))
				return this;

			if (!(child.element instanceof Node))
				return this;

			this.element.appendChild(child.element);
			this.childs.push(child);
		}

		removeChild(child) {
			if (!(this.element instanceof Node))
				return this;

			if (!(child instanceof XuElement))
				return this;

			if (!(child.element instanceof Node))
				return this;

			// Childs of child
			child.removeChilds();

			this.element.removeChild(child.element);
			if (this.childs.indexOf(child) >= 0)
				this.childs.splice(this.childs.indexOf(child), 1);

			return this;
		}

		removeChilds() {
			while (this.childs.length > 0)
				this.removeChild(this.childs[this.childs.length - 1]);
		}

		addClass(className) {
			if (!(this.element instanceof Node))
				return this;

			if ('string' != typeof className)
				return this;

			if (!(this.classes instanceof Array))
				return this;

			if (this.classes.includes(className))
				return this;

			this.element.classList.add(className);
			this.classes.push(className);

			return this;
		}

		removeClass(className) {
			if (!(this.element instanceof Node))
				return this;

			if ('string' != typeof className)
				return this;

			if (!(this.classes instanceof Array))
				return this;

			if (!this.classes.includes(className))
				return this;

			this.element.classList.remove(className);
			this.classes.splice(this.classes.findIndex((element) => { element == className }), 1);

			return this;
		}

		setClasses(classes) {
			if (!(this.element instanceof Node))
				return this;

			if (!(classes instanceof Array))
				return this;

			if (!(this.classes instanceof Array))
				return this;

			this.element.classList.remove(...this.element.classList.entries());
			this.classes = [];

			classes.forEach((className) => {
				if ('string' != typeof className)
					return;

				this.element.classList.add(className);
				this.classes.push(className);
			}, this);

			return this;
		}

		setContent(content) {
			if (!(this.element instanceof Node))
				return this;

			this.element.textContent = content;

			return this;
		}

		getContent() {
			if (!(this.element instanceof Node))
				return null;

			return this.element.textContent;
		}

		on(type, listener) {
			if (!(this.element instanceof Node))
				return this;

			this.element.addEventListener(type, listener);

			return this;
		}
	}
</script>
