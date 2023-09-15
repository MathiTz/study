package main

import (
	"database/sql"
	"log"

	_ "github.com/go-sql-driver/mysql"
)

func main() {
	db, err := sql.Open("mysql", "root:root@tcp(localhost:8889)/cursogo")
	if err != nil {
		log.Fatal(err)
	}

	defer db.Close()

	tx, _ := db.Begin()
	stmt, _ := tx.Prepare("INSERT INTO usuarios (id, nome) VALUES (?, ?)")

	stmt.Exec(2000, "Bia")
	stmt.Exec(2001, "Carlos")

	_, err = stmt.Exec(1, "Tiago") // chave duplicada
	if err != nil {
		tx.Rollback()
		log.Fatal(err)
	}

	tx.Commit()
	defer stmt.Close()
}
