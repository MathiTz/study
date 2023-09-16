package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"strconv"

	_ "github.com/go-sql-driver/mysql"
	"github.com/joho/godotenv"
)

type Users struct {
	ID        int    `json:"id"`
	FirstName string `json:"first_name"`
	LastName  string `json:"last_name"`
	Email     string `json:"email"`
}

func getUsers(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	id := r.URL.Query().Get("id")

	if id != "" {
		getUser(w, r, db, id)
		return
	}

	var users []Users
	rows, err := db.Query("SELECT id, first_name, last_name, email FROM users")
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close()

	for rows.Next() {
		var user Users
		err := rows.Scan(&user.ID, &user.FirstName, &user.LastName, &user.Email)
		if err != nil {
			log.Fatal(err)
		}
		users = append(users, user)
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(users)
}

func getUser(w http.ResponseWriter, r *http.Request, db *sql.DB, id string) {
	var user Users
	err := db.QueryRow("SELECT id, first_name, last_name, email FROM users WHERE id = ?", id).Scan(&user.ID, &user.FirstName, &user.LastName, &user.Email)
	if err != nil {
		log.Fatal(err)
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(user)
}

func createUser(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	var user Users
	json.NewDecoder(r.Body).Decode(&user)

	result, err := db.Exec("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)", user.FirstName, user.LastName, user.Email)
	if err != nil {
		log.Fatal(err)
	}

	lastInsertID, err := result.LastInsertId()
	if err != nil {
		log.Fatal(err)
	}

	user.ID = int(lastInsertID)

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(user)
}

func updateUser(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	id := r.URL.Query().Get("id")

	var user Users
	json.NewDecoder(r.Body).Decode(&user)

	_, err := db.Exec("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?", user.FirstName, user.LastName, user.Email, id)
	if err != nil {
		log.Fatal(err)
	}

	user.ID, _ = strconv.Atoi(id)

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(user)
}

func getUserByID(id string, db *sql.DB) (Users, error) {
	var user Users
	err := db.QueryRow("SELECT id, first_name, last_name, email FROM users WHERE id = ?", id).Scan(&user.ID, &user.FirstName, &user.LastName, &user.Email)
	if err != nil {
		return user, err
	}

	return user, nil
}

func deleteUserByID(id string, db *sql.DB) error {
	_, err := db.Exec("DELETE FROM users WHERE id = ?", id)
	if err != nil {
		return err
	}

	return nil
}

func deleteUser(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	// Get the user ID from the request URL path
	id := r.URL.Query().Get("id")

	// Check if the user with the given ID exists
	_, err := getUserByID(id, db)
	if err != nil {
		// If the user doesn't exist, return a 404 Not Found error
		w.WriteHeader(http.StatusNotFound)
		fmt.Fprintf(w, "User with ID %s not found", id)
		return
	}

	// Delete the user with the given ID
	err = deleteUserByID(id, db)
	if err != nil {
		// If there was an error deleting the user, return a 500 Internal Server Error
		w.WriteHeader(http.StatusInternalServerError)
		fmt.Fprint(w, "Error deleting user")
		return
	}

	// If the user was deleted successfully, return a 204 No Content response
	w.WriteHeader(http.StatusNoContent)
}

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}

	dbUsername := os.Getenv("DB_USERNAME")
	dbPassword := os.Getenv("DB_PASSWORD")
	dbHost := os.Getenv("DB_HOST")
	dbPort := os.Getenv("DB_PORT")
	dbName := os.Getenv("DB_NAME")

	dataSourceName := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s", dbUsername, dbPassword, dbHost, dbPort, dbName)

	db, err := sql.Open("mysql", dataSourceName)
	if err != nil {
		log.Fatal("Error connecting to database")
	}
	defer db.Close()

	http.HandleFunc("/users", func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case "GET":
			getUsers(w, r, db)
		case "POST":
			createUser(w, r, db)
		case "PUT":
			updateUser(w, r, db)
		case "DELETE":
			deleteUser(w, r, db)
		}
	})

	log.Println("Executando...")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
