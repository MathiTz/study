import UsersRepository from '../repository/implementations/UsersRepository';

interface ICreateUser {
  name: string;
  email: string;
  password: string;
}

class CreateUserService {
  async execute({ name, email, password }: ICreateUser): Promise<void> {
    if (!password || !email || !name)
      throw new Error('Credentials must be provided');

    try {
      await UsersRepository.create({ name, email, password });
    } catch (error) {
      throw new Error(error);
    }
  }
}

export default new CreateUserService();
