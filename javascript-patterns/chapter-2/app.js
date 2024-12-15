/**
 * Minha aplicação Javascript
 *
 * @module myapp
 */

var MYAPP = {};

/**
 * Um utilitário de matemática
 * @namespace MYAPP
 * @class math_stuff
 */
MYAPP.math_stuff = {
	/**
	 * Soma dois números
	 *
	 * @method sum
	 * @param {Number} a Primeiro número
	 * @param {Number} b Segundo número
	 * @return {Number} Resultado da soma
	 */
	sum: function (a, b) {
		return a + b;
	},

	/**
	 * Multiplica dois números
	 *
	 * @method multi
	 * @param {Number} a Primeiro número
	 * @param {Number} b Segundo número
	 * @return {Number} Resultado da multiplicação
	 */
	multi: function (a, b) {
		return a * b;
	},
};

/**
 * Constrói objetos Person
 * @namespace MYAPP
 * @class Person
 * @constructor
 * @param {String} first Primeiro nome
 * @param {String} last  Último nome
 */
MYAPP.Person = function (first, last) {
	/**
	 * Nome da pessoa
	 * @property first_name
	 * @type String
	 */
	this.first_name = first;

	/**
	 * Último sobrenome (de família) da pessoa
	 * @property last_name
	 * @type String
	 */
	this.last_name = last;
};

/**
 * Retorna o nome do objeto da pessoa
 *
 * @method getName
 * @return {String} O nome da pessoa
 */
MYAPP.Person.prototype.getName = function () {
	return this.first_name + " " + this.last_name;
};
