function sum(x, y) {
	// Antipattern
	result = x + y;
	return result;
}

// console.log(sum(1, 2));
// console.log(result);

var global = (function () {
	return this;
})();

// console.log(global);
var man = {
	hands: 2,
	legs: 2,
	heads: 1,
};

if (typeof Object.prototype.clone === "undefined") {
	Object.prototype.clone = function () {};
}

// 1. Padrão filtro para propriedades apenas do objeto instanciado
for (var i in man) {
	if (man.hasOwnProperty(i)) {
		console.log(i, ":", man[i]);
	}
}

// 2. Antipadrão sem filtro
for (var i in man) {
	console.log(i, ":", man[i]);
}

// 3. Outro padrão onde chamamos a propriedade a partir do prototype
for (var i in man) {
	if (Object.prototype.hasOwnProperty.call(man, i)) {
		console.log(i, ":", man[i]);
	}
}

// 4. Outro padrão onde você utiliza variáveis para alocar
var i,
	hasOwn = Object.prototype.hasOwnProperty;

for (i in man) {
	if (hasOwn.call(man, i)) {
		console.log(i, ":", man[i]);
	}
}
