import AxiosAdapter from '../../src/infra/http/AxiosAdapter';

test('Deve retornar os produtos', async function () {
  const httpClient = new AxiosAdapter();

  const response = await httpClient.get('http://localhost:3003/products');

  expect(response).toHaveLength(3);
});

test('Deve retornar o produto 1', async function () {
  const httpClient = new AxiosAdapter();

  const response = await httpClient.get('http://localhost:3003/products/1');

  expect(response.idProduct).toBe(1);
  expect(response.description).toBe('A');
  expect(response.volume).toBe(0.03);
  expect(response.density).toBe(100);
});

test('Deve retornar o produto 4', async function () {
  const httpClient = new AxiosAdapter();

  expect(() =>
    httpClient.get('http://localhost:3003/products/4')
  ).rejects.toThrow(new Error('Invalid dimension'));
});
