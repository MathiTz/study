var combinationSum2 = function (candidates, target) {
  //Write code here
  let res = [];
  candidates.sort((a, b) => a - b);

  function combinationSumRecursive(index, curr, currSum) {
    if (currSum === target) {
      res.push([...curr]);
      return;
    }

    if (currSum > target || index > candidates.length - 1) {
      return;
    }

    const candidatesMap = {};
    for (let j = index; j < candidates.length; j++) {
      if (!candidatesMap[candidates[j]]) {
        candidatesMap[candidates[j]] = true;
        curr.push(candidates[j]);
        combinationSumRecursive(j + 1, curr, currSum + candidates[j]);
        curr.pop(); // Backtracking
      }
    }
  }

  combinationSumRecursive(0, [], 0);

  return res;
};

console.log(combinationSum2([3, 5, 2, 1, 3], 7));
