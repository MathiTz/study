package main

import (
	"log"
)

func main() {
	svc := NewCatFactService("https://catfact.ninja/fact")
	svc = NewLoggingService(svc)

	apiServer := NewApiServer(svc)
	log.Fatal(apiServer.Start(":8080"))
}
