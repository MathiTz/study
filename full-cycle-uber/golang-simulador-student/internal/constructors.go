package internal

import "go.mongodb.org/mongo-driver/mongo"

func NewRoute(id string, distance int, directions []Directions) *Route {
	return &Route{
		ID:         id,
		Distance:   distance,
		Directions: directions,
	}
}

func NewRouteService(mongo *mongo.Client, freightService *FreightService) *RouteService {
	return &RouteService{
		mongo:          mongo,
		freightService: freightService,
	}
}

func NewFreightService() *FreightService {
	return &FreightService{}
}

func NewRouteCreatedEvent(routeID string, distance int, directions []Directions) *RouteCreatedEvent {
	return &RouteCreatedEvent{
		EventName:  "routeCreated",
		RouteID:    routeID,
		Distance:   distance,
		Directions: directions,
	}
}

func NewFreightCalculatedEvent(routeID string, amount float64) *FreightCalculatedEvent {
	return &FreightCalculatedEvent{
		EventName: "freightCalculated",
		RouteID:   routeID,
		Amount:    amount,
	}
}

func NewDeliveryStartedEvent(routeID string) *DeliveryStartedEvent {
	return &DeliveryStartedEvent{
		EventName: "deliveryStarted",
		RouteID:   routeID,
	}
}

func NewDriverMovedEvent(routeID string, lat float64, lng float64) *DriverMovedEvent {
	return &DriverMovedEvent{
		EventName: "driverMoved",
		RouteID:   routeID,
		Lat:       lat,
		Lng:       lng,
	}
}
