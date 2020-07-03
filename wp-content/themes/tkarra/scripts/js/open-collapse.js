jQuery(document).ready(function() {
    class openCollapse {
        constructor(el) {
            this.el = el;
            this.isOpen = false;
            this.container = null;
            this.content = null;
            this.contentHeight = null;
        }

        init() {
            this.content = this.getDomContent();
            this.contentHeight = this.getDomContentHeight();
            this.container = this.getDomContainer();
            this.bindEvents();
        }

        bindEvents() {
            this.el.addEventListener('click', () => this.toggleDrawer());
            jQuery(window).resize(() => {
                this.contentHeight = this.getDomContentHeight();
                if (this.isOpen)
                    this.setDomContentHeight();
            });
        }

        getDomContent() {
            return this.el.querySelector('.open-collapse__drawer__content-container .open-collapse__drawer__content');
        }

        getDomContentHeight() {
            return this.content.offsetHeight;
        }

        setDomContentHeight() {
            this.container.style.height = `${this.contentHeight}px`;
        }

        getDomContainer() {
            return this.el.querySelector('.open-collapse__drawer__content-container');
        }
        
        toggleDrawer() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.el.classList.add('is-open');

                if (this.container === null) return;
                this.container.style.height = `${this.contentHeight}px`;
            } else {
                this.el.classList.remove('is-open');

                if (this.container === null) return;
                this.container.style.height = '0px';
            }
        }
    }

    const elems = document.querySelectorAll('[data-module="openCollapse"]');
    
    for (let i = 0; i < elems.length; i++) {
        const elem = new openCollapse(elems[i]);
        elem.init();
    }
});