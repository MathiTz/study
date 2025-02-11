package internal

import (
	"context"
	"math"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

type Directions struct {
	Lat float64 `bson:"lat" json:"lat"`
	Lng float64 `bson:"lng" json:"lng"`
}

type Route struct {
	ID          string       `bson:"_id" json:"id"`
	Distance    int          `bson:"distance" json:"distance"`
	Directions  []Directions `bson:"directions" json:"directions"`
	FreighPrice float64      `bson:"freigh_price" json:"freigh_price"`
}

type FreightService struct{}

func (fs *FreightService) Calculate(distance int) float64 {
	return math.Floor((float64(distance)*0.15+0.3)*100) / 100
}

type RouteService struct {
	mongo          *mongo.Client
	freightService *FreightService
}

func (rs *RouteService) CreateRoute(route *Route) (*Route, error) {
	route.FreighPrice = rs.freightService.Calculate(route.Distance)

	update := bson.M{
		"$set": bson.M{
			"distance":     route.Distance,
			"directions":   route.Directions,
			"freigh_price": route.FreighPrice,
		},
	}

	filter := bson.M{"_id": route.ID}
	opts := options.Update().SetUpsert(true)

	_, err := rs.mongo.Database("routes").Collection("routes").UpdateOne(context.TODO(), filter, update, opts)
	if err != nil {
		return &Route{}, err
	}

	return route, err
}

func (rs *RouteService) GetRoute(id string) (Route, error) {
	var route Route

	filter := bson.M{"_id": id}

	err := rs.mongo.Database("routes").Collection("routes").FindOne(context.TODO(), filter).Decode(&route)
	if err != nil {
		return Route{}, err
	}

	return route, nil
}
