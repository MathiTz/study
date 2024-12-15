const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database(':memory:');

db.serialize(() => {
  db.exec(
    `
      CREATE TABLE todo(
        id INTEGER PRIMARY KEY,
        description TEXT,
        finished BOOLEAN DEFAULT FALSE
      )
    `
  );

  db.all(
    `
      INSERT INTO todo (id, description, finished) VALUES (?, ?, ?)
    `,
    [null, 'Lorem ipsum amet', false]
  );
});

module.exports = {
  db,
};
