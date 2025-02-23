Recursion - very important concept in programming

Parent of:

- Backtracking -> Guided form of recursion
- Dynamic programming -> Top down / Memoization is recursive. We will use this to come up with Bottom up/Tabulation (iterative) approach
- Greedy algorithms -> Recursion where you try to optimize next step (short term not long term)
- Divide and conquer -> Divide into subproblems, solve them and combine them to solve the original problem

If you are using a tre to solve a question consider using recursion. Similar subproblems -> recursion

## Complexity ANalysis of Recursive Solutions

- If you are working with a tree:
  - T -> [# nodes] x [work done per node]
  - S -> [# nodes] x [space used per node] (depth of the tree) | Recursion call stack

1. ## What is recursion?

- A function calling itself until base condition/termination condition is met.
  - E.g print numbers 1 to 5 -> pseudocode `(print 1, print 2, print 3, print 4, print 5)`
    - problems:
      - need to know the number of times to print
      - need to know the start and end point
  - E.g print numbers 1 to n -> pseudocode `(function (n) { if n > 5 -> return; print n; function(n-1);})` using recursion
    - if no base condition, it will go into infinite loop (stack overflow)

2. ## When to use recursion?

- You need to solve a problem -> can be divided into smaller problems (similar to the original problem) - smaller subproblems: solve, use these solutions to constant sol. to the original problem
  - pseudocode: `function(n) { if n is equal to 1 -> return 1; function(n-1); } -> function(5)`

3. ## Visualization: Recursion Tree, Recursion Call Stack

   - When a function is called memory has to be allocated to remember
     - local variables
     - function arguments
     - return address
   - Pseudocode: `function (n) { if n is equal to 1 -> return 1; return function(n-1) x n; } -> function(5)`
   - Recursion Tree: `function(5) -> function(4) -> function(3) -> function(2) -> function(1)`
     - `function(1) -> return 1 -> function(2) -> return 2 x 1 -> function(3) -> return 3 x 2 -> function(4) -> return 4 x 6 -> function(5) -> return 5 x 24`
   - Recursion Call Stack (LIFO): `function(5) -> function(4) -> function(3) -> function(2) -> function(1) -> return 1 -> return 2 x 1 -> return 3 x 2 -> return 4 x 6 -> return 5 x 24`

4. ## Recursion vs Iteration

- Does not use recursive call stack space [better space complexity] -> Iteration
- Things done recursively can be done iteratively
- Pseudocode | Recursion : `function(n) { if n is equal to 1 -> return 1; return function(n-1) x n; } -> function(5)`
- Pseudocode | Iteration : `function(n) { let result = 1; for (let i = 1; i <= n; i++) { result = result x i; } return result; } -> function(5)`
- Recursion -> ascending phase (calling phase) -> descending phase (returning phase)
- Iteration -> only ascending phase

5. ## Ways to write Base condition

- When to stop recursively calling itself and start returning
  - Last valid input
  - First invalid input

6. ## Recursive Leap of Faith

- how to use recursion
- Steps:
  1. Understand the problem -> print 3 2 1 1 2 3
  2. Identity subproblem -> print 2 1 1 2
  3. Trust | Faith
     - You have to trust that the recursive call will correctly solve a smaller version of your problem.
     - You don't try to mentally simulate or "unroll" the recursive calls.
     - Instead, you assume that if your function works for simpler/smaller problems, it will work for the current problem.
  4. Link 1 & 2
  5. Base condition -> for 0, return
     - Pseudocode: `function seq(n) { if n = 0 -> return, print n seq(n-1) -> inductive step; print n }`
     - Inductive step: if it works for n, it will work for n-1

7. ## Recurrence Relation

- Expresses the solution of a problem as a function of the solutions to smaller instances of the same problem.
- E.g: Pseudocode: `F(N) = n x F(N-1)`
- E.g: Pseudocode: `print n F(n - 1) print n`

8. ## 0 to n and n to 0

- You can write recursive solutions in two ways

  - 0 to n
  - n to 0

- Q. Given n, find 1+2+3...n
  - 1: 0 to n
    - Pseudocode: `function sum(curr, n) { if curr = n -> return n; return curr + sum(curr + 1, n); } -> sum(0, 5)`
    - Recursive tree:
      - sum(0,5) -> 0 + sum(1, 5) -> 1 + sum(2, 5) -> 2 + sum(3, 5) -> 3 + sum(4, 5) -> 4 + sum(5, 5) -> 5 -> 15
  - 2: n to 0
    - Pseudocode: `function sum(n) { if n = 0 -> return 0; return n + sum(n - 1); } -> sum(5)`
    - Recursive tree:
      - sum(5) -> 5 + sum(4) -> 4 + sum(3) -> 3 + sum(2) -> 2 + sum(1) -> 1 + sum(0) -> 0 -> 15

9. ## Solving Recursion Questions

- If you can draw the recursion tree then you can easily solve any recursion question
- Pratice drawing a recursion tree
  - F(n) = F(n-1) + F(n-2) -> Fibonacci series
  - F(0) = 0
  - F(1) = 1
  - 0 (0), 1 (1), 1(2), 2(3), 3(4), 5(5), 8(6), 13(7), 21(8), 34(9), 55(10)
- F(3) -> F(1) F(2) [F(1) F(0)]
- Base condition: n <= 1 -> return n
