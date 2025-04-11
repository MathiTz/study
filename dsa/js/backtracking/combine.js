/**
 *
 * @param {number} n
 * @param {number} k
 * constraints: 1 <= k <= n
 * e.g. n = 4, k = 2
 * output: [ [1,2], [1,3], [1, 4], [2, 3], [2, 4], [3, 4] ]
 */
var combine = function (n, k) {
  //Write Code here
  let res = [];
  function helper(start, curr) {
    // base case
    if (curr.length === k) {
      return res.push([...curr]);
    }

    // recursive case
    // optimized
    let need = k - curr.length;
    for (let j = start; j <= n - need + 1; j++) {
      curr.push(j);
      helper(j + 1, curr);
      curr.pop();
    }
  }

  helper(1, []);
  return res;
};
