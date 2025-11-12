import AField from './components/a-field';

const aFields = [];

document.querySelectorAll('.a-field', (element) => {
	aFields.push(new AField(element));
});
