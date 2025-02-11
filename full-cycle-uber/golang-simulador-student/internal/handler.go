package internal

import "time"

type RouteCreatedEvent struct {
	EventName  string       `json:"event"`
	RouteID    string       `json:"id"`
	Distance   int          `json:"distance"`
	Directions []Directions `json:"directions"`
}

type FreightCalculatedEvent struct {
	EventName string  `json:"event"`
	RouteID   string  `json:"route_id"`
	Amount    float64 `json:"amount"`
}

type DeliveryStartedEvent struct {
	EventName string `json:"event"`
	RouteID   string `json:"route_id"`
}

type DriverMovedEvent struct {
	EventName string  `json:"event"`
	RouteID   string  `json:"route_id"`
	Lat       float64 `json:"lat"`
	Lng       float64 `json:"lng"`
}

func RouteCreatedHandler(event *RouteCreatedEvent, rs *RouteService) (*FreightCalculatedEvent, error) {
	route := NewRoute(event.RouteID, event.Distance, event.Directions)
	routeCreated, err := rs.CreateRoute(route)
	if err != nil {
		return nil, err
	}

	freightCalculatedEvent := NewFreightCalculatedEvent(routeCreated.ID, routeCreated.FreighPrice)
	return freightCalculatedEvent, nil
}

func DeliveryStartedHandler(event *DeliveryStartedEvent, rs *RouteService, ch chan *DriverMovedEvent) error {
	route, err := rs.GetRoute(event.RouteID)
	if err != nil {
		return err
	}

	driverMovedEvent := NewDriverMovedEvent(route.ID, 0, 0)

	for _, direction := range route.Directions {
		driverMovedEvent.RouteID = route.ID
		driverMovedEvent.Lat = direction.Lat
		driverMovedEvent.Lng = direction.Lng
		time.Sleep(time.Second)
		ch <- driverMovedEvent
	}

	return nil
}
