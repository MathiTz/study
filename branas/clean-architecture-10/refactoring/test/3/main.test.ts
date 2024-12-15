import NormalFareCalculatorHandler from "../../src/3/NormalFareCalculatorHandler";
import OvernightFareCalculatorHandler from "../../src/3/OvernightFareCalculatorHandler";
import Ride from "../../src/3/Ride";
import SpecialDayFareCalculatorHandler from "../../src/3/SpecialDayFareCalculatorHandler";
import SundayFareCalculatorHandler from "../../src/3/SundayFareCalculatorHandler";

let ride: Ride;

beforeEach(() => {
  const normalFareCalculatorHandler = new NormalFareCalculatorHandler();
  const overnightFareCalculator = new OvernightFareCalculatorHandler(
    normalFareCalculatorHandler
  );
  const overnightSundayFareCalculator = new OvernightFareCalculatorHandler(
    overnightFareCalculator
  );
  const sundayFareCalculator = new SundayFareCalculatorHandler(
    overnightSundayFareCalculator
  );
  const specialDayFareCalculator = new SpecialDayFareCalculatorHandler(
    sundayFareCalculator
  );
  ride = new Ride(specialDayFareCalculator);
});

test("Deve calcular uma corrida no primeiro dia do mês", function () {
  ride.addSegment(10, new Date("2021-03-01T10:00:00"));

  expect(ride.calculateFare()).toBe(15);
});

test("Deve calcular uma corrida normal", function () {
  ride.addSegment(10, new Date("2021-03-02T10:00:00"));

  expect(ride.calculateFare()).toBe(21);
});

test("Deve calcular uma corrida noturna", function () {
  ride.addSegment(10, new Date("2021-03-02T23:00:00"));

  expect(ride.calculateFare()).toBe(39);
});
