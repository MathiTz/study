import UsersRepository from '../repository/implementations/UsersRepository';

class DeleteUserService {
  async execute(id: string): Promise<void> {
    return UsersRepository.delete(id);
  }
}

export default new DeleteUserService();
