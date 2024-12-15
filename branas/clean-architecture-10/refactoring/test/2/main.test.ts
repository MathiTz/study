import { calculateRides } from "../../src/2/main";

test("Deve calcular uma corrida no primeiro dia do mês", function () {
  expect(
    calculateRides([{ distance: 10, date: new Date("2021-03-01T10:00:00") }])
  ).toBe(15);
});

test("Deve calcular uma corrida diurna não domingo normal", function () {
  expect(
    calculateRides([{ distance: 10, date: new Date("2021-03-02T10:00:00") }])
  ).toBe(21);
});

test("Deve calcular uma corrida noturna", function () {
  expect(
    calculateRides([{ distance: 10, date: new Date("2021-03-02T23:00:00") }])
  ).toBe(39);
});

test("Deve calcular uma corrida diurna no domingo", function () {
  expect(
    calculateRides([{ distance: 10, date: new Date("2021-03-07T10:00:00") }])
  ).toBe(29);
});

test("Deve calcular uma corrida noturna no domingo", function () {
  expect(
    calculateRides([{ distance: 10, date: new Date("2021-03-07T23:00:00") }])
  ).toBe(50);
});

test("Não deve calcular uma corrida com distância negativa", function () {
  expect(() =>
    calculateRides([{ distance: -10, date: new Date("2021-03-01T10:00:00") }])
  ).toThrowError("Invalid distance");
});

test("Não deve calcular uma corrida com data inválida", function () {
  expect(() =>
    calculateRides([{ distance: 10, date: new Date("abcdef") }])
  ).toThrowError("Invalid date");
});

test("Deve calcular uma corrida com valor mínimo", function () {
  expect(
    calculateRides([{ distance: 3, date: new Date("2021-03-01T10:00:00") }])
  ).toBe(10);
});
