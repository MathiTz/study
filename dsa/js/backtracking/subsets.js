/**
 * Power Set - Given an integer array of unique elements, return all possible subsets (the power set).
 * The solution set must not contain duplicate subsets. Return the solution in any order.
 *
 * @param {number[]} nums
 */
var subsets = function (nums) {
  //Write code here
  const result = [];

  function helper(nums, i, subset) {
    if (i === nums.length) {
      result.push(subset.slice());
      return;
    }

    // dont add
    helper(nums, i + 1, subset);

    // add
    subset.push(nums[i]);
    helper(nums, i + 1, subset);
    subset.pop();
  }

  helper(nums, 0, []);

  return result;
};

console.table(subsets([1, 2, 3]));
