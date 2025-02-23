// Make use of the fact that given Array is sorted in ascending order
// [-3, 1, 2, 7] -> [0, 0, 0,0] -> initialize a new array with 0s
// One of the values from the edges will always be the largest
// -3 * -3 = 9, 7 * 7 = 49 -> meaning the largest value will be at the end
// Using two pointers, we can compare the values at the edges
// If the left value is smaller, we square it and move the pointer to the right
// If the right value is smaller, we square it and move the pointer to the left
// We fill the new array from the end to the beginning
// Complexity: O(n) time | O(n) space

function sortedSquarredArray(array) {
  const newArray = new Array(array.length).fill(0);
  let pointerLeft = 0;
  let pointerRight = array.length - 1;

  while (pointerLeft <= pointerRight) {
    const leftValue = Math.pow(array[pointerLeft], 2);
    const rightValue = Math.pow(array[pointerRight], 2);

    if (leftValue > rightValue) {
      newArray[i] = leftValue;
      pointerLeft++;
    }

    if (rightValue > leftValue) {
      newArray[i] = rightValue;
      pointerRight--;
    }
  }

  return newArray;
}
