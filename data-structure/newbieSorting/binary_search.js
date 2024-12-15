/**
 *
 * @param {number[]} arr - It needs to be sorted
 * @param {number} value - Value to be found
 */
function binarySearch(arr, value) {
  let left = 0,
    right = arr.length;

  for (let i = 0, max = arr.length; i < max; i++) {
    const mid = Math.floor((left + right) / 2);

    if (arr[mid] === value) {
      return mid;
    }

    if (arr[mid] > value) {
      right = mid;
    }

    if (arr[mid] < value) {
      left = mid;
    }
  }

  return -1;
}

// console.log(binarySearch([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 10));
