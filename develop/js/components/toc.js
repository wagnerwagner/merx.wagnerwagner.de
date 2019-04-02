const tocElement = document.querySelector('.toc');

if (tocElement) {
  const buttonToggleElement = tocElement.querySelector('.button-toggle');

  if (buttonToggleElement) {
    buttonToggleElement.addEventListener('click', () => {
      tocElement.classList.toggle('is-advanced');
      buttonToggleElement.classList.toggle('is-active');
    });
  }
}
