// Brute force solution
// [-3,1,2,7] -> square each element & sort -> [1,4,9,49]

// Time complexity: O(nlogn)
// Space complexity: O(n)

function sortedSquarredArray(array) {
  const newArray = new Array(array.length).fill(0);
  for (let i = 0; i < array.length; i++) {
    newArray[i] = array[i] * array[i];
  }
  return newArray.sort((a, b) => a - b);
}
