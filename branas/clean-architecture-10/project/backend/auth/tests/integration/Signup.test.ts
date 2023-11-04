import User from '../../src/domain/entity/User';

test('Deve criar uma conta para o usuário', async function () {
  const users: any = {};
  const userRepository: UserRepository = {
    async save(user: User): Promise<void> {
      users[user.email.getValue()] = user;
    },
    async get(email: string): Promise<User> {
      return users[email];
    },
  };
  const signup = new Signup(userRepository);
  const input = {
    email: 'joao@gmail.com',
    password: 'abc123',
  };
  await signup.execute(input);

  const login = new Login(userRepository);
  const output = await login.execute(input);
  expect(output.token).toBe(
    'eyJhbGciOiJIUzI1NiIsInCI6IkpXVCJ9.eyJlbWFpbCI6ImpvYW9AZ21haWwuY29tIiwiaWF0IjoxNjc3Njc1NjAwMDAwLCJleHBpcmVzSW4iOjMxMTA0MDAwfQ.44NzDplxRU16N9glVXYMDdhmz4qhsPI-fsS4yEo6XXQ'
  );
});
