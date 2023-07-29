import pgp from 'pg-promise';
import ProductRepository from './ProductRepository';
import Product from './domain/entity/Product';
import Connection from './Connection';

export default class ProductRepositoryDatabase implements ProductRepository {
  constructor(readonly connection: Connection) {}

  async getProduct(idProduct: number): Promise<Product> {
    const [product] = await this.connection.query(
      'SELECT * FROM cccat10.product where id_product = $1',
      [idProduct]
    );

    return new Product(
      product.id_product,
      product.description,
      parseFloat(product.price),
      product.width,
      product.height,
      product.length,
      parseFloat(product.weight),
      product.currency
    );
  }
}
