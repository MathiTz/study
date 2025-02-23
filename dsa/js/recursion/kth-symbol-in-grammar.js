/**
 * Clarifying Questions
 * 1. Is it possible that N is given as 0? No,  n >= 1
 * 2. Can k be out of bound ? For eg. If n = 3, there will be 4 numbers, can be given as 5? No, 1 <= k <= 2ˆ(n-1)
 *
 * Test Cases -> can form together with interviewer
 *
 * n = 1, k = 1 -> 0
 * n = 4, k = 1 -> 0
 * n = 4, k = 8 -> 1
 * 0, 0 1, 0 1 1 0, 0 1 1 0 1 0 0 1
 */
var kthGrammar = function (n, k) {
  //Write code here
  //Write code here
  const table = [];
  let counter = 0;

  while (table.length !== n) {
    const row = table[counter];
    console.log(row);
    if (!row || row === undefined) {
      table.push([0]);
      continue;
    }

    const rowNumbers = [];

    for (let i = 0; i < row.length; i++) {
      if (row[i] === 0) {
        rowNumbers.push(0);
        rowNumbers.push(1);
      }

      if (row[i] === 1) {
        rowNumbers.push(1);
        rowNumbers.push(0);
      }
    }

    table.push(rowNumbers);
    counter++;
  }

  return table[table.length - 1][k - 1];
};

kthGrammar(3, 1);
