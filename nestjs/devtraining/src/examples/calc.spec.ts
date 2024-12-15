export function add(x: number, y: number) {
  return x + y;
}

describe('Initial test', () => {
  test('Add function', () => {
    expect(add(2, 2)).toEqual(4);
  });
});
