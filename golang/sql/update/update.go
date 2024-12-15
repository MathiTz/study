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
	stmt, _ := db.Prepare("UPDATE usuarios SET nome = ? WHERE id = ?")
	defer stmt.Close()

	stmt.Exec("Uóxiton Istive", 1)
	stmt.Exec("Sheristone Ualeska", 2)

	stmt2, _ := db.Prepare("DELETE FROM usuarios WHERE id = ?")
	defer stmt2.Close()
	stmt2.Exec(4)

}
