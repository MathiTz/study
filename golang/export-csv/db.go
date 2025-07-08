package main

import (
	"database/sql"

	_ "github.com/lib/pq"
)

func DatabaseClient() *sql.DB {
	dbName := "export"
	dbUser := "docker"
	dbPassword := "docker"
	connectionString := "host=localhost port=5432 user=" + dbUser + " password=" + dbPassword + " dbname=" + dbName + " sslmode=disable"

	db, err := sql.Open("postgres", connectionString)
	if err != nil {
		panic(err)
	}

	// Check if the connection is established
	err = db.Ping()
	if err != nil {
		panic(err)
	}

	return db
}
