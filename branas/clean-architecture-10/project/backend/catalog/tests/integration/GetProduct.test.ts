import GetProduct from '../../src/application/usecase/GetProduct';
import GetProducts from '../../src/application/usecase/GetProducts';
import PgPromise from '../../src/infra/database/PgPromiseAdapter';
import ProductRepositoryDatabase from '../../src/infra/repository/ProductRepositoryDatabase';

test('Deve listar os productos', async function () {
  const connection = new PgPromise();
  const productRepository = new ProductRepositoryDatabase(connection);
  const getproduct = new GetProduct(productRepository);
  const output = await getproduct.execute(1);

  expect(output.idProduct).toBe(1);
  expect(output.description).toBe('A');
  expect(output.volume).toBe(0.03);
  expect(output.density).toBe(100);

  await connection.close();
});
