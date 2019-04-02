class Gallery {
  constructor(element) {
    this.element = element;

    window.addEventListener('load', () => {
      this.setDimensions();
    });
    window.addEventListener('resize', () => {
      this.setDimensions();
    });
    this.setDimensions();
  }

  setDimensions() {
    const { element } = this;
    this.dimensions = {
      top: element.offsetTop,
      height: element.offsetHeight,
    };
  }

  get isInView() {
    const { dimensions } = this;
    if (dimensions) {
      const { scrollTop } = document.scrollingElement;
      console.info(`Gallery “${this.element.id}” is in view.`);
      return scrollTop >= dimensions.top && scrollTop < dimensions.top + dimensions.height;
    }
    return false;
  }
}

export default Gallery;
