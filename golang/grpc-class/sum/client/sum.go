package main

import (
	"context"
	sm "github.com/mathitz/study/golang/grpc-class/sum/proto"
	"log"
)

func doSum(c sm.SumServiceClient) {
	log.Println("doSum was invoked")
	res, err := c.Sum(context.Background(), &sm.SumRequest{NumberOne: 1, NumberTwo: 3})

	if err != nil {
		log.Fatalf("Could not sum: %v\n", err)
	}

	log.Printf("Sum: %v\n", res.Result)
}
