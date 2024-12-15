var ObjectMaker = function () {
  this.name = "This is it";

  var that = {};
  that.name = "And that is it";

  return that;
};

var o = new ObjectMaker();
console.log(o.name);

function Waffle() {
  if (!(this instanceof Waffle)) {
    return new Waffle();
  }

  this.tastes = "yummy";
}

function WaffleMaker() {
  if (!(this instanceof arguments.callee)) {
    return new arguments.callee();
  }

  this.waffles = [];
}

var white = new Array(256).join(" ");

console.log(white.length);

function getRe() {
  var re = /[a-z]/;
  re.foo = "bar";
  return re;
}

var reg = getRe(),
  re2 = getRe();

console.log(reg === re2);
reg.foo = "baz";
console.log(re2.foo);

function throwError() {
  try {
    // algo aconteceu, gere um erro
    throw {
      name: "MyErrorType",
      message: "ooops",
      extra: "This was rather embarrasing",
      remedy: "genericErrorHandler", // quem deve tratá-lo
    };
  } catch (error) {
    // informe o usuário
    console.log(error.message);

    console.log(error.remedy);
  }
}

throwError();
