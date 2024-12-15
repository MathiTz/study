package main

import (
	"context"
	sm "github.com/mathitz/study/golang/grpc-class/sum/proto"
	"log"
)

func (s *Server) Sum(ctx context.Context, in *sm.SumRequest) (*sm.SumResponse, error) {
	log.Printf("Sum function was invoked: %v\n", in)

	return &sm.SumResponse{
		Result: in.NumberOne + in.NumberTwo,
	}, nil
}
