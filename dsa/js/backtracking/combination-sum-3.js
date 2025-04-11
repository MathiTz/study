var combinationSum3 = function (k, n) {
  //Write code here
  let res = [];

  function backtrackSum(index, curr, currSum) {
    if (currSum === n && curr.length === k) {
      return res.push([...curr]);
    }

    if (currSum > n) {
      return;
    }

    for (let j = index; j < 10; j++) {
      curr.push(j);
      backtrackSum(j + 1, curr, currSum + j);
      curr.pop();
    }
  }

  backtrackSum(1, [], 0);

  return res;
};

console.log(combinationSum3(3, 14));

// [[1,4,9],[1,5,8],[1,6,7],[2,3,9],[2,4,8],[2,5,7],[3,4,7],[3,5,6]]
// [[1,2,11],[1,3,10],[1,4,9],[1,5,8],[1,6,7],[2,3,9],[2,4,8],[2,5,7],[3,4,7],[3,5,6]]
