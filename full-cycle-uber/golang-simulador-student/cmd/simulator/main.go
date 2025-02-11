package main

import (
	"context"
	"encoding/json"
	"fmt"

	"github.com/mathitz/fullcycle/imersao20/simulator/internal"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

func main() {
	mongoStr := "mongodb://root:root@localhost:27017/routes?authSource=admin"
	mongoConnection, err := mongo.Connect(context.Background(), options.Client().ApplyURI(mongoStr))
	if err != nil {
		panic(err)
	}

	freightService := internal.NewFreightService()
	routeService := internal.NewRouteService(mongoConnection, freightService)

	routeCreatedEvent := internal.NewRouteCreatedEvent(
		"1",
		100,
		[]internal.Directions{
			{Lat: 0, Lng: 0},
			{Lat: 1, Lng: 1},
		},
	)

	result, err := internal.RouteCreatedHandler(routeCreatedEvent, routeService)
	if err != nil {
		panic(err)
	}

	json, err := json.Marshal(result)
	if err != nil {
		panic(err)
	}

	fmt.Println(string(json))
}
