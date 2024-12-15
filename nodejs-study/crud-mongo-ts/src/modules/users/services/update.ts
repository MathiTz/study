import UsersRepository from '../repository/implementations/UsersRepository';

interface IUpdateUser {
  name: string;
  email: string;
  password?: string;
}

class UpdateUserService {
  async execute({ name, email, password }: IUpdateUser) {
    if (!email) throw new Error('Credentials must be provided');

    try {
      await UsersRepository.update({ name, email, password });
    } catch (error) {
      throw new Error(error);
    }
  }
}

export default new UpdateUserService();
