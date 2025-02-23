/**
 * 1) nth row first half is same as previous row
 * 2) nth row second half is not of previous row
 * Pseudocode - (4, 7): !(n-1, 3); length = 8, mid = 4 -> !(2, 1) -> !(1, 1) -> 0
 * Pseudocode: function kgram(inputs) { if n = 1 -> return 0; L = 2^n-1; mid = L/2; if k <= mid -> return kgram(n-1, k); else return !kgram(n-1, k-mid); }
 * T - O(n)
 * S - O(n)
 *
 * @param {integer} n
 * @param {integer} k
 */
var kthGrammar = function (n, k) {
  // Base case
  if (n === 1) return 0;

  // recursive case
  let length = Math.pow(2, n - 1);
  let mid = length / 2;

  if (k <= mid) {
    return kthGrammar(n - 1, k);
  } else {
    return 1 - kthGrammar(n - 1, k - mid);
  }
};

kthGrammar(3, 1);
