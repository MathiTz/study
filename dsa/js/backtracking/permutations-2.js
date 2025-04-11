//Permutations
/**
 * Given an array nums of distinct integers, return all the unique possible permutations.
 * You can return the answer in any order.
 * - [1,1,2]:
 * [1,1,2], [1,2,1], [2,1,1]
 *
 */
var permuteUnique = function (nums) {
  //Write code here
  let res = [];
  let n = nums.length;
  function permutations(i) {
    //base case
    if (i === n - 1) {
      res.push([...nums]);
      return;
    }

    // recursive case
    var hash = {};
    for (let j = i; j < n; j++) {
      if (!hash[nums[j]]) {
        hash[nums[j]] = 1;
        [nums[i], nums[j]] = [nums[j], nums[i]];
        permutations(i + 1);
        // backtracking
        [nums[i], nums[j]] = [nums[j], nums[i]]; // reverting changes
      }
    }
  }

  permutations(0);
  return res;
};

console.log(permuteUnique([1, 1, 2]));
