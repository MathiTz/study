import TokenGenerator from '../../src/domain/entity/TokenGenerator';
import User from '../../src/domain/entity/User';

test('Deve gerar o token do usuário', async function () {
  const user = await User.create('joao@gmail.com', 'abc123');
  const expiresIn = 60 * 60 * 24 * 30 * 12; // 1 year
  const issueDate = new Date('2023-03-01T10:00:00');
  const tokenGenerator = new TokenGenerator('key');
  const token = tokenGenerator.generate(user, expiresIn, issueDate);

  expect(token).toBe(
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImpvYW9AZ21haWwuY29tIiwiaWF0IjoxNjc3Njc1NjAwMDAwLCJleHBpcmVzSW4iOjMxMTA0MDAwfQ.44NzDplxRU16N9glVXYMDdhmz4qhsPI-fsS4yEo6XXQ'
  );
});

test('Deve validar o token do usuário', async function () {
  const token =
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImpvYW9AZ21haWwuY29tIiwiaWF0IjoxNjc3Njc1NjAwMDAwLCJleHBpcmVzSW4iOjMxMTA0MDAwfQ.44NzDplxRU16N9glVXYMDdhmz4qhsPI-fsS4yEo6XXQ';
  const tokenGenerator = new TokenGenerator('key');
  const payload = tokenGenerator.verify(token);

  expect(payload).toBeDefined();
  expect(payload.email).toBe('joao@gmail.com');
});

test('Deve tentar validar o token inválido', async function () {
  const token =
    'eyJhbGciOiJIUzI1NiIsInCI6IkpXVCJ9.eyJlbWFpbCI6ImpvYW9AZ21haWwuY29tIiwiaWF0IjoxNjc3Njc1NjAwMDAwLCJleHBpcmVzSW4iOjMxMTA0MDAwfQ.44NzDplxRU16N9glVXYMDdhmz4qhsPI-fsS4yEo6XXQ';
  const tokenGenerator = new TokenGenerator('key');
  expect(() => tokenGenerator.verify(token)).toThrow(
    new Error('invalid token')
  );
});
