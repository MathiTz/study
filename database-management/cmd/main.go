package main

import (
	"fmt"
	"github.com/mathitz/hopper/hopper"
	"log"
)

func main() {

	db, err := hopper.New()
	if err != nil {
		log.Fatal(err)
	}
	user := map[string]string{
		"name": "Matheus",
		"age":  "23",
	}

	id, err := db.Insert("users", user)
	if err != nil {
		log.Fatal(err)
	}

	//coll, err := db.CreateCollection("users")
	//if err != nil {
	//	log.Fatal(err)
	//}

	fmt.Printf("%v\n", id)
}
