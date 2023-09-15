package main

import (
	"log"
	"net/http"

	client "github.com/mathitz/server"
)

func main() {
	http.HandleFunc("/usuarios/", client.UsuarioHandler)
	log.Println("Executando...")
	log.Fatal(http.ListenAndServe(":3333", nil))
}
