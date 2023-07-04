import UsersRepository from '../repository/implementations/UsersRepository';

class MakeUserAdminService {
  async execute(id: string): Promise<void> {
    try {
      return await UsersRepository.makeAdmin(id);
    } catch (error) {
      throw new Error(error);
    }
  }
}

export default new MakeUserAdminService();
