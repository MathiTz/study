export default class Cpf {
  value: string;

  constructor(cpf: string) {
    if (!this.validate(cpf)) throw new Error("Invalid cpf");

    this.value = cpf;
  }

  calculateDigit(cpf: string, factor: number) {
    let total = 0;
    for (const digit of cpf) {
      if (factor > 1) total += parseInt(digit) * factor--;
    }

    const rest = total % 11;
    return rest < 2 ? 0 : 11 - rest;
  }

  isInvalidLength(cpf: string) {
    return cpf.length < 11 || cpf.length > 14;
  }

  allDigitsTheSame(cpf: string) {
    const [firstDigit] = cpf;
    return [...cpf].every((digit) => digit === firstDigit);
  }

  extractDigits(cpf: string) {
    return cpf.slice(9);
  }

  validate(rawCpf: string | null | undefined) {
    if (!rawCpf) return false;
    const cleanCpf = rawCpf.replace(/\D/g, "");
    if (this.isInvalidLength(cleanCpf)) return false;
    if (this.allDigitsTheSame(cleanCpf)) return false;
    const dg1 = this.calculateDigit(cleanCpf, 10);
    const dg2 = this.calculateDigit(cleanCpf, 11);
    let actualDigit = this.extractDigits(cleanCpf);
    const validatedDigital = `${dg1}${dg2}`;
    return actualDigit === validatedDigital;
  }
}
