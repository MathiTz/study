import UserRepository from '../repository/UserRepository';

export default class Signup {
  constructor(readonly userRepository: UserRepository) {}

  execute(input: Input): Promise<void> {}
}

type Input = {
  email: string;
  password: string;
};
