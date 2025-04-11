var subsetsWithDup = function (nums) {
  let res = [];
  nums.sort((a, b) => a - b);

  function subsets(index, curr) {
    // base case
    if (index === nums.length) {
      res.push([...curr]);
      return;
    }

    // recursive case
    // Include
    curr.push(nums[index]);
    subsets(index + 1, curr);
    curr.pop(); // backtracking
    // Exclude
    while (index < nums.length - 1 && nums[index] === nums[index + 1]) {
      index++;
    }
    subsets(index + 1, curr);
  }

  subsets(0, []);

  return res;
};

console.log(subsetsWithDup([1, 2, 2]));
