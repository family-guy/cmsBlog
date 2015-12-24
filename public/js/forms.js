/*
Copyright (C) 2015  Guy R. King

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

/**
* Functions for creating forms and dropdown menus
*/

/**
* Adds an input and a label to a form
* @param form element <code>form</code>, string <code>inputName</code>, string <code>inputType</code>, string <code>labelText</code>
*/
function addInput(form, inputName, inputType, labelText) {
	var label = document.createElement('label');
	var labelTextNode = document.createTextNode(labelText);
	label.appendChild(labelTextNode);
	label.for = inputName;
	var div = document.createElement('div');
	var input = document.createElement('input');
	input.type = inputType;
	input.name = inputName;
	input.id = inputName;
	div.appendChild(input);
	form.appendChild(label);
	form.appendChild(div);
}
/**
* Creates a form with inputs, labels, and submit button
* @param string <code>formMethod</code>, string <code>formAction</code>, array of strings <code>inputNames</code>, array of strings <code>inputTypes</code>, array of strings <code>labelTexts</code>, string <code>submitTextButton</code>
* @return form element
*/
function makeForm(formMethod, formAction, inputNames, inputTypes, labelTexts, submitButtonText) {
	var result = document.createElement('form');
	result.method = formMethod;
	result.action = formAction;
	for (var i = 0; i < inputNames.length; i++) {
		addInput(result, inputNames[i], inputTypes[i], labelTexts[i]);
	}
	var div = document.createElement('div');
	var submit = document.createElement('input');
	submit.type = 'submit';
	submit.value = submitButtonText;
	div.appendChild(submit);
	result.appendChild(div);
	return result;
}