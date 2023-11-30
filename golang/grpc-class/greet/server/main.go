package main

import (
	"log"
	"net"

	pb "github.com/mathitz/grpc-class"
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

	log.Printf("Listening on %s\n", addr)

	s := grpc.NewServer()

	if err := s.Serve(list); err != nil {
		log.Fatalf("Failed to serve: %v\n", err)
	}
}
