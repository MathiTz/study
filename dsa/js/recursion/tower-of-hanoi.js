function toh(N, fromm, to, aux) {
  //Write code here
  // sample print statement below
  //console.log("move disk " + 1 + " from rod " + 1 + " to rod " + 2);
  //in the above statement consider we are moving disk 1 from rod 1 to rod 2
  //remember to return number of moves as well
  let count = 0;

  function helper(N, fromm, to, aux) {
    if (N === 1) {
      count++;
      console.log('move disk ' + N + ' from rod ' + fromm + ' to rod ' + to);
      return;
    }

    helper(N - 1, fromm, to, aux);
    count++;
    console.log('move disk ' + N + ' from rod ' + fromm + ' to rod ' + to);
    helper(N - 1, aux, to, fromm);
  }

  helper(N, fromm, to, aux);
  return count;
}

console.log(toh(4, '1', '3', '2'));

// For Input (N = 2)
// Output:
// move disk n - 1 from rod 1 to rod 2
// move disk n from rod 1 to rod 3
// move disk n - 1 from rod 2 to rod 3
// 3

// For Input (N = 3)
// Output:
// move disk n - 2 from rod 1 to rod 3
// move disk n - 1 from rod 1 to rod 2
// move disk n - 2 from rod 3 to rod 2
// move disk n from rod 1 to rod 3
// move disk n - 2 from rod 2 to rod 1
// move disk n - 1 from rod 2 to rod 3
// move disk n - 2 from rod 1 to rod 3
// 7

// For Input (N = 4)
// Output:
// move disk n - 3 from rod 1 to rod 3
// move disk n - 2 from rod 1 to rod 3
// move disk n - 1 from rod 1 to rod 2
// move disk n - 2 from rod 3 to rod 2
// move disk n - 3 from rod 3 to rod 2
// move disk n from rod 1 to rod 3
// move disk n - 3 from rod 2 to rod 1
// move disk n - 2 from rod 2 to rod 3
// move disk n - 1 from rod 2 to rod 3
// move disk n - 2 from rod 1 to rod 3
// move disk n - 3 from rod 1 to rod 3
// 15
