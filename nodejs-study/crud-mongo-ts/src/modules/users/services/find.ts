import { Users } from '../../../@types/Users';
import UsersRepository from '../repository/implementations/UsersRepository';

class FindUser {
  async execute(id: string): Promise<Users> {
    try {
      return await UsersRepository.findById(id);
    } catch (error) {
      throw new Error(error);
    }
  }
}

export default new FindUser();
