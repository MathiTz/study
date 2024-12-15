import Cpf from '../../src/domain/entity/Cpf';

const validCpfs = [
  '987.654.321-00',
  '714.602.380-01',
  '313.030.210-72',
  '144.796.170-60',
];

test.each(validCpfs)('Deve testar um cpf válido: %s', (value: string) => {
  // Act
  const cpf = new Cpf(value);

  // Assert
  expect(cpf.value).toBeDefined();
});

const invalidCpfs = [
  '111.111.111-11',
  '222.222.222-22',
  '333.333.333-33',
  '444.444.444-44',
  '555.555.555-55',
  '666.666.666-66',
  '777.777.777-77',
  '888.888.888-88',
  '999.999.999-99',
  '000.000.000-00',
  '987.654.321-04',
  '714.652.380-19',
  '313.030.210-55',
  '144.796.170-89',
];

test.each(invalidCpfs)('Deve testar um cpf inválido: %s', (value: string) => {
  expect(() => new Cpf(value)).toThrowError('Invalid cpf');
});
