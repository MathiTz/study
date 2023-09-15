package main

import (
	"fmt"
	"net/http"
	"time"
)

func horaCerta(w http.ResponseWriter, r *http.Request) {
	s := time.Now().Format("02/01/2006 03:04:05")
	fmt.Fprintf(w, "<h1>Hora certa: %s</h1>", s)
}

func main() {
	http.HandleFunc("/hora-certa", horaCerta)
	http.ListenAndServe(":8080", nil)
}
