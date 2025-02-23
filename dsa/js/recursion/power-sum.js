/**
 *
 * @param {number[]} array
 * @param {number} power
 * @returns
 */
function powerSum(array, power = 1) {
  //write code here
  return array.reduce((acc, curr) => {
    if (Array.isArray(curr)) {
      acc += Math.pow(powerSum(curr, power + 1), power + 1);
    } else {
      acc += curr;
    }

    return acc;
  }, 0);
}

// console.log(powerSum([1, 2, 3])); // 6
// console.log(powerSum([1, 2, 3, [3]])); // 15
// console.log(powerSum([1, 2, 3, [3, 1]])); // 22
// console.log(powerSum([2, 3, [4, 1, 2]])); // 54
console.log(powerSum([1, 2, [7, [3, 4], 2]]));
