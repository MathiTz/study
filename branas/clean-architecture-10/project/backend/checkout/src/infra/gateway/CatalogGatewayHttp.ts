import CatalogGateway from '../../application/gateway/CatalogGateway';
import Product from '../../domain/entity/Product';
import HttpClient from '../http/HttpClient';

export default class CatalogGatewayHttp implements CatalogGateway {
  constructor(readonly httpClient: HttpClient) {}

  async getProducts(): Promise<Product[]> {
    const response = await this.httpClient.get(
      'http://localhost:3003/products'
    );
    const products: Product[] = [];
    for (const productData of response) {
      const product = new Product(
        productData.idProduct,
        productData.description,
        productData.price,
        productData.width,
        productData.height,
        productData.length,
        productData.weight,
        productData.currency
      );
      products.push(product);
    }

    return products;
  }

  async getProduct(idProduct: number): Promise<Product> {
    const response = await this.httpClient.get(
      `http://localhost:3003/products/${idProduct}`
    );

    if (response.message) {
      throw new Error(response.message);
    }

    return new Product(
      response.idProduct,
      response.description,
      response.price,
      response.width,
      response.height,
      response.length,
      response.weight,
      response.currency
    );
  }
}
