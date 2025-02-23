/**
 * n = 1, k = 2, O/P = 1
 * n = 2, k = 3, O/P = 2
 * n = 3, k = 3, O/P = 2
 * n = 4, k = 3, O/P = 1
 * @param {number} n
 * @param {number} k
 */
var findTheWinnerApproach1 = function (n, k) {
  // T -> O(n^2) | S -> O (n)
  if (n === 1) return 1;
  let friends = Array.from({ length: n }, (_, index) => index + 1);

  function helper(arr, startIndex) {
    // base case
    if (arr.length === 1) return arr[0];

    let indexToRemove = (startIndex + k - 1) % arr.length;
    arr.splice(indexToRemove, 1);
    return helper(arr, indexToRemove);
  }

  return helper(friends, 0);
};

var findTheWinnerApproach2 = function (n, k) {
  // T -> O(n) | S -> O(n)
  function josephus(n) {
    // Base case
    if (n === 1) return 0;

    // Recursive case
    return [josephus(n - 1) + k] % n;
  }

  return josephus(n) + 1;
};

var findTheWinnerApproach3 = function (n, k) {
  // T -> O(n)| S -> O(1)
  let survivor = 0;
  for (let i = 2; i < n; i++) {
    survivor = (survivor + k) % i;
  }

  return survivor + 1;
};

console.log(findTheWinner(4, 3));
