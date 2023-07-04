import pgp from "pg-promise";
import CouponRepository from "./CouponRepository";
import Coupon from "./domain/entity/Coupon";

export default class CouponRepositoryDatabase implements CouponRepository {
  async getCoupon(code: string): Promise<Coupon> {
    const connection = pgp()("postgres://matheusalves:@localhost:5432/app");
    const [couponData] = await connection.query(
      "SELECT * FROM cccat10.coupon where code = $1",
      [code]
    );
    await connection.$pool.end();
    return new Coupon(
      couponData.code,
      parseFloat(couponData.percentage),
      couponData.expire_date
    );
  }
}
