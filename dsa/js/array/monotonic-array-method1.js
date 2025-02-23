// monotonic increasing array -> [1, 2, 2, 3, 4, 5]
// monotonic decreasing array -> [5, 4, 3, 2, 2, 1]
const checkMonotonic = function (array) {
  //write code here to return either true or false
  if (array.length === 0 || array.length === 1) return true;
  let isMonotonic = true;
  let isMonotonicI = false;
  let isMonotonicD = false;
  let count = 1;

  while (isMonotonic && count < array.length) {
    const actualValue = array[count];
    const previousValue = array[count - 1];

    if (count === 1) {
      if (actualValue < previousValue) {
        isMonotonicD = true;
      }

      if (actualValue > previousValue) {
        isMonotonicI = true;
      }
    }

    if (isMonotonicD) {
      if (actualValue <= previousValue) {
        count++;
        continue;
      }

      isMonotonic = false;
      break;
    }

    if (isMonotonicI) {
      if (actualValue >= previousValue) {
        count++;
        continue;
      }

      isMonotonic = false;
      break;
    }
  }

  return isMonotonic;
};

console.log(checkMonotonic([1, 3, 2, 4, 5]));
