import pgp from "pg-promise";
import ProductRepository from "./ProductRepository";
import Product from "./domain/entity/Product";

export default class ProductRepositoryDatabase implements ProductRepository {
  async getProduct(idProduct: number): Promise<Product> {
    const connection = pgp()("postgres://matheusalves:@localhost:5432/app");
    const [product] = await connection.query(
      "SELECT * FROM cccat10.product where id_product = $1",
      [idProduct]
    );
    await connection.$pool.end();
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
