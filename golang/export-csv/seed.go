package main

import (
	"fmt"
	"time"

	"github.com/go-faker/faker/v4"
)

type Product struct {
	ID             int
	Name           string
	Price_In_Cents int
	Description    string
	Created_At     time.Time
}

func SeedTable() {
	db := DatabaseClient()

	_, err := db.Exec(`
		CREATE TABLE IF NOT EXISTS products (
			id SERIAL PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			description TEXT,
			price_in_cents INTEGER NOT NULL,
			created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
		)
	`)
	if err != nil {
		panic(err)
	}

	_, err = db.Exec(`
		TRUNCATE TABLE products
	`)
	if err != nil {
		panic(err)
	}

	for range 2 {
		products := make([]Product, 100)
		for j := range products {
			err := faker.FakeData(&products[j])
			if err != nil {
				panic(err)
			}

			fmt.Printf("Generated product: %s\n", products[j].Name)
		}

		// insert the products into the database
		for _, product := range products {
			stmt, err := db.Prepare(`
				INSERT INTO products (name, description, price_in_cents, created_at)
				VALUES ($1, $2, $3, $4)
			`)
			if err != nil {
				panic(err)
			}
			defer stmt.Close()

			_, err = stmt.Exec(product.Name, product.Description, product.Price_In_Cents, product.Created_At)
			if err != nil {
				panic(err)
			}

			fmt.Printf("Inserted product: %s\n", product.Name)
		}
	}

	defer db.Close()
}
