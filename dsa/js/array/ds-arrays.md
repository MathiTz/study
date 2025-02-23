Data Strucutres: Arrays

- JS, Python only have dynamic arrays -> which means allow O(1) access, set, and insert at the end
  - OS allocates almost 2 times as much memory as needed
- Static arrays are fixed in size, can't be resized and next memory slot may not be available
  - C, C++ have static arrays

Big O of common Array operations:

1. Access -> S, T = O(1)
2. Set -> S, T = O(1)
3. Traverse/Search -> T = O(n), S = O(1) (traverse means to go through all the elements)
4. Copy -> T = O(n), S = O(n)
5. Inserting

- At the beginning -> T = O(n), S = O(1)
  - Copy array & make a new array with required # of memory slots
- At the end -> T = O(1), S = O(1)
  - If memory is available, just insert
- Somewhere in between -> T = O(n), S = O(1)
  - Copy array & make a new array with required # of memory slots

6. Remove

- At the beginning -> T = O(n), S = O(1)
  - Copy array & make a new array with required # of memory slots
- At the end -> T = O(1), S = O(1)
  - If memory is available, just remove
- Somewhere in between -> T = O(n), S = O(1)
  - Copy array & make a new array with required # of memory slots
