package main

import (
	"encoding/csv"
	"os"
	"strconv"
)

func main() {
	// Seed the database
	SeedTable()

	// Query the database using cursor
	db := DatabaseClient()
	rows, err := db.Query(`
		SELECT id, name
		FROM products
	`)

	if err != nil {
		panic(err)
	}

	// Create a slice to hold the values
	var products []Product

	// Iterate over the rows
	for rows.Next() {
		var id int
		var name string

		err := rows.Scan(&id, &name)
		if err != nil {
			panic(err)
		}

		// Append the values to the slice
		products = append(products, Product{
			ID:   id,
			Name: name,
		})

		// Print the values
		println("ID:", id, "Name:", name)
	}

	// When done, create a CSV file
	// and write the products to it

	csvFile, err := os.Create("products.csv")
	if err != nil {
		panic(err)
	}

	writer := csv.NewWriter(csvFile)

	// Write the header
	header := []string{"ID", "Name"}
	if err := writer.Write(header); err != nil {
		panic(err)
	}

	// Write the products to the CSV file
	for _, product := range products {
		record := []string{strconv.Itoa(product.ID), product.Name}
		if err := writer.Write(record); err != nil {
			panic(err)
		}
	}

	// Flush the writer and close the file
	if err := writer.Error(); err != nil {
		panic(err)
	}

	defer writer.Flush()
	defer csvFile.Close()
	defer db.Close()
	defer rows.Close()
}
