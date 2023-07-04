// anti padrão
// fins demonstrativos
var add = new Function("a", "b", "return a + b");
console.log(add(1, 2));

// expressão de funcão nomeada
var add = function add(a, b) {
  return a + b;
};

// expressão de função anônima
var add = function (a, b) {
  return a + b;
};

// declaração de função
function foo() {
  // corpo da função
}

// página 78
