import bcrypt from 'bcryptjs';
import { sign } from 'jsonwebtoken';
import { inject, injectable } from 'tsyringe';
import { AppError } from '../../../../errors/AppError';

import { IUsersRepository } from '../../repositories/IUsersRepository';

interface IRequest {
  email: string;
  password: string;
}

interface IResponse {
  user: {
    name: string;
    email: string;
  };
  token: string;
}

@injectable()
class AuthenticateUserUseCase {
  constructor(
    @inject('UsersRepository')
    private usersRepository: IUsersRepository
  ) {}

  async execute({ email, password }: IRequest): Promise<IResponse> {
    const user = await this.usersRepository.findByEmail(email);

    if (!user) throw new AppError('Email or password incorrect!');

    const passwordMatch = await bcrypt.compare(password, user.password);

    if (!passwordMatch) throw new AppError('Email or password incorrect!');

    const token = sign({}, '7e1019ceb67350ff8c7f75700d54bec6f8ae06ad', {
      subject: user.id,
      expiresIn: '1d',
    });

    const tokenReturn: IResponse = {
      token,
      user: { name: user.name, email },
    };

    return tokenReturn;
  }
}

export { AuthenticateUserUseCase };
