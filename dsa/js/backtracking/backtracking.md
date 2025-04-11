Backtracking

1. ## What is Backtracking?

- Backtracking is a general algorithm for finding all (or some) solutions to some computational problems, notably constraint satisfaction problems, that incrementally builds candidates to the solutions, and abandons a candidate ("backtracks") as soon as it determines that the candidate cannot possibly be completed to a valid solution.
- Known as controlled recursion.
- It is a brute force search algorithm that systematically searches for a solution to a problem among all available options.
- It is a recursive algorithm that tries to generate all possible solutions to a problem.
- Make changes in place and revert back to original state.

2. ## How is it Different from Recursion?

- Fn. calls itself until base condition is met
- Controlled recursion (abandon paths that doesn't led to the solution) + modify state of the problem in place (pass by reference)

3. ## How does Backtracking Work?

   - Explore one option
   - Keep building the solution with recursion (DFS) until violates condition
   - backtrack, explore different path
   - If it doesn't work, backtrack and try another option
   - Valid solution is found, return it
   - Continues only if conditions are favorable different from brute force which generates all possible solutions

4. ## Pass by reference / Change inplace

- In backtracking, we pass the state of the problem by reference and make changes in place.
- This is done to avoid creating a new copy of the state for each recursive call.
- E.g: Permutations of a string `'abc'`
  - `abc` -> `acb` -> `bac` -> `bca` -> `cab` -> `cba`
  - `abc` -> `acb` -> `abc` -> `acb` -> `bac` -> `bca` -> `bac` -> `bca` -> `cab` -> `cba` -> `cab` -> `cba`
  - Two approachs:
    - Create new array/string and all permutations
    - backtracking -> in place ( need to store copies in the result)

5. ## Blueprint to solve questions using Backtracking

- Controlled (abandon what does not lead to solution) recursion + modify state of the problem in place (pass by reference)
- Pseudocode:
  ```
  Function helper() {
    if solved -> save the soluition/print -> print -> Base condition
    for choice in choices {
      if isValid (choice) {
        choice
        helper()
        revert choice
      }
    }
  }; helper()
  ```

6. ## Identify / When to use Backtracking

- If a problem requires every possible path; there are multiple solutions and you want all of them
- Not for optimization problems (e.g. shortest path, maximum sum, etc.)
