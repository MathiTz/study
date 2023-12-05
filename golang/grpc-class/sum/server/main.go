package main

import (
	sm "github.com/mathitz/study/golang/grpc-class/sum/proto"
	"google.golang.org/grpc"
	"log"
	"net"
)

var addr string = "0.0.0.0:50051"

type Server struct {
	sm.SumServiceServer
}

func main() {
	list, err := net.Listen("tcp", addr)

	if err != nil {
		log.Fatalf("Failed to listen on: %v\n", err)
	}

	log.Printf("Listening on: %s\n", addr)

	s := grpc.NewServer()
	sm.RegisterSumServiceServer(s, &Server{})

	if err := s.Serve(list); err != nil {
		log.Fatalf("Failed to serve: %v\n", err)
	}
}
