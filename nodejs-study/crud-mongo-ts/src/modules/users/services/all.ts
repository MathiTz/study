import { Users } from '../../../@types/Users';
import UsersRepository from '../repository/implementations/UsersRepository';

class GetAllUsersService {
  async execute(): Promise<Users[]> {
    return UsersRepository.index();
  }
}

export default new GetAllUsersService();
