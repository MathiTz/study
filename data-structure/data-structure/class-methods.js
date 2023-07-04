class Student {
  constructor(firstName, lastName, year) {
    this.firstName = firstName;
    this.lastName = lastName;
    this.grade = year;
    this.tardies = 0;
    this.scores = [10, 2, 5, 10, 9];
  }

  getFullName() {
    return `${this.firstName} ${this.lastName}`;
  }

  markLate() {
    this.tardies += 1;
    if (this.tardies >= 3) {
      return "YOU ARE EXPELLED!!!";
    }
    return `${this.getFullName()} has been late ${this.tardies}`;
  }

  addScore(score) {
    this.scores.push(score);
    return this.scores;
  }

  calculateAverage() {
    return (
      this.scores.reduce(function (a, b) {
        return a + b;
      }) / this.scores.length
    );
  }

  static EnrollStudents() {
    return "ENROLLING STUDENTS";
  }
}

const firstStudent = new Student("Colt", "Steele");

firstStudent.getFullName();
// console.log(firstStudent.EnrollStudents());
