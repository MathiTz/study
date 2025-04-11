//Permutations
/**
 * Given an array nums of distinct integers, return all the possible permutations.
 * You can return the answer in any order.
 * - [1,2,3]:
 * [1,2,3], [1,3,2], [2,1,3], [2,3,1], [3,1,2], [3,2,1] -> N = 3
 * - [1]:
 * [[1]] -> N = 1
 *
 */
var permute = function (nums) {
  //Write code here
  let res = [];
  let n = nums.length;
  function helper(i) {
    //base case
    if (i === n - 1) {
      res.push([...nums]);
      return;
    }

    // recursive case
    for (let j = i; j < n; j++) {
      [nums[i], nums[j]] = [nums[j], nums[i]];
      helper(i + 1);
      // backtracking
      [nums[i], nums[j]] = [nums[j], nums[i]]; // reverting changes
    }
  }

  helper(0);
  return res;
};

console.log(permute([1, 2, 3]));
