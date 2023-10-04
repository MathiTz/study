import GetProducts from '../../src/application/usecase/GetProducts';
import PgPromise from '../../src/infra/database/PgPromiseAdapter';
import ProductRepositoryDatabase from '../../src/infra/repository/ProductRepositoryDatabase';

test('Deve listar os productos', async function () {
  const connection = new PgPromise();
  const productRepository = new ProductRepositoryDatabase(connection);
  const getproducts = new GetProducts(productRepository);
  const output = await getproducts.execute();

  expect(output).toHaveLength(3);

  await connection.close();
});
