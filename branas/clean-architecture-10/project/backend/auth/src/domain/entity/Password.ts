import { pbkdf2, randomBytes } from 'crypto';

export default class Password {
  static ITERATIONS = 100;
  static KEY_LENGTH = 64;
  static DIGEST = 'sha512';

  constructor(readonly value: string, readonly salt: string) {}

  static create(password: string, salt?: string): Promise<Password> {
    const generatedSalt = salt || randomBytes(20).toString('hex');

    return new Promise((resolve) => {
      pbkdf2(
        password,
        generatedSalt,
        Password.ITERATIONS,
        Password.KEY_LENGTH,
        Password.DIGEST,
        (err, value) => {
          resolve(new Password(value.toString('hex'), generatedSalt));
        }
      );
    });
  }

  async validate(password: string): Promise<boolean> {
    return new Promise((resolve) => {
      pbkdf2(
        password,
        this.salt,
        Password.ITERATIONS,
        Password.KEY_LENGTH,
        Password.DIGEST,
        (err, value) => {
          resolve(this.value === value.toString('hex'));
        }
      );
    });
  }
}
