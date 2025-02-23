// monotonic increasing array -> [1, 2, 2, 3, 4, 5]
// monotonic decreasing array -> [5, 4, 3, 2, 2, 1]
const checkMonotonic = function (array) {
  //write code here to return either true or false
  if (array.length === 0 || array.length === 1) return true;
  const firstValue = array[0];
  const lastValue = array[array.length - 1];
  let isMonotonicI = firstValue < lastValue;

  if (firstValue === lastValue) {
    for (let i = 0; i < array.length - 1; i++) {
      if (array[i + 1] !== array[i]) return false;
    }
  } else if (isMonotonicI) {
    for (let i = 0; i < array.length - 1; i++) {
      if (array[i + 1] < array[i]) return false;
    }
  } else {
    for (let i = 0; i < array.length - 1; i++) {
      if (array[i + 1] > array[i]) return false;
    }
  }

  return true;
};

console.log(checkMonotonic([1, 3, 2, 4, 5]));
