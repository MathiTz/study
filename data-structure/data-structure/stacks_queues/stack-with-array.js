const stack = [];

// O(1) time complexity for push and pop operations
stack.push(1);
stack.push(2);
stack.push(3);

stack.pop(); // 3
stack.pop(); // 2
stack.pop(); // 1

// O(n) time complexity for shift and unshift operations - Because you need to re-index all elements in the array
stack.unshift(1);
stack.unshift(2);
stack.unshift(3);

stack.shift(); // 3
stack.shift(); // 2
stack.shift(); // 1
