package main

import (
	"log"
	"net"

	pb "github.com/mathitz/grpc-go-course"
)

var addr string = "0.0.0.0:50051"

type Server struct {
	pb.GreetServiceServer
}

func main() {
	lis, err := net.Listen("tcp", addr)

	if err != nil {
		log.Fatalf("Failed to listen on: %v\n", err)
	}

	log.Prinf("Listening on %s\n", addr)
}
