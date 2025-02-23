1. What is the need for complexity analysis?

- Q. Algorithm -> 1/P = N (e.g.: N = 5, 4 + 3 + 2 + 1 = 10)
- Approach 1: (N - 1) x N / 2 | Approach 2: iterate from (N - 1) to 1 and keep adding
  - A. Which approach is better?
    - count the number of simples operations computer has to perform
    - Approach 1: 3 simple operations (N - 1, N / 2, x), if N = 1000, it'll be 3 operations
    - Approach 2: 2n - 3 simple operations (N - 1, 2, x, -), if N = 1000, it'll be 1997 operations
  - B. Why care about identifying the better approach? Huge amount of data significantly impacts the performance
  - C. What does better mean? Faster (Time complexity) and less memory (Space complexity)
  - D. As N or 1/P gorws => In what proportion does number of operations grow?
    - Approach 1: grows constantly (1)
    - Approach 2: grows linear (N)

2. What is Time complexity?

- How the running time of an algorithm grows as the size of the input grows

3. Asymptotic Analysis

- Asymptotic Notation: Big O, Big Omega, Big Theta
- Notation used to express asymptotic analysis
- For 1/P size N -> # operations f(N)
  - Big O: upper bound, worst case
  - Big Omega: lower bound, best case
  - Big Theta: both upper and lower bound, average case
- E.g. f(n) = N + 3, O(N), Omega(N), Theta(N) | 3 becomes insignificant as N grows
  - O(N) -> as 1/P grows, the number of operations grows linearly
- E.g. f(n) = N^2 + 3N, O(N^2), Omega(N^2), Theta(N^2) | 3N becomes insignificant as N grows
  - O(N^2) -> as 1/P grows, the number of operations grows quadratically

4. What is Big O?

- f(n) = 5N + 3 -> O(N)
  - O(N) -> # operations is bounded by a multiple of N, trend x details

5. Common Complexities

- O(N) -> # operations is bounded by a multiple of N. As 1/P size grows, time taken grows linearly
- O(1) -> # operations is constant, regardless of the size of the input
- O(log N) -> binary search algorithm
- O(N) -> traverse elements of array & add them
- O(N log N) -> merge sort, quick sort
- O(N^2) -> [1,2,3] => [[1,1], [1,2], [1,3], [2,1], [2,2], [2,3], [3,1], [3,2], [3,3]]
- O(2^N) -> fibonacci series
- O(N!) -> permutation of a set of N elements

6. Space Complexity

- Is expressed in Big O notation
- How much auxiliary memory is used by the algorithm as the size of the input grows
  - Space required by only the algorithm, not the input
  - Some solutions we can get a better Time Complexity by using more Space Complexity
  - Some solutions make a new array, string
  - numbers, booleans, null, undefined -> constant space

7. Techiniques to simplifying Big O Expressions

- Drop constants
  - O(2N) -> O(N)
- Drop insignificant terms
  - O(N^2 + N) -> O(N^2)
- Different input parameters
  - O(N + M) -> O(N + M)

8. Logarithms -> O (logN) is a very efficient time complexity

- ln coding log is base 2
- log2(8) = 3, 2^3 = 8
- log2(16) = 4, 2^4 = 16
- N = 2^10 = log2(N) = 10
- N = 2^20 = log2(N) = 20

- Algorithm that cuts 1/P in half at every step is O(logN)
  - If you double 1/P => only 1 extra operation
- 8 -> 4 -> 2 -> 1
