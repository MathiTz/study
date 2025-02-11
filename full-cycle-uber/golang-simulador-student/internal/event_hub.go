package internal

import (
	"encoding/json"
	"fmt"

	"github.com/go-errors/errors"
	"go.mongodb.org/mongo-driver/mongo"
)

type EventHub struct {
	routeService  *RouteService
	mongoClient   *mongo.Client
	chDriverMoved chan *DriverMovedEvent
}

func (eh *EventHub) HandleEvent(msg []byte) error {
	var baseEvent struct {
		EventName string `json:"event"`
	}
	err := json.Unmarshal(msg, &baseEvent)
	if err != nil {
		return fmt.Errorf("error unmarshalling event: %w", err)
	}

	switch baseEvent.EventName {
	case "RouteCreated":
		var event RouteCreatedEvent
		err := json.Unmarshal(msg, &event)
		if err != nil {
			return fmt.Errorf("error unmarshalling event: %w", err)
		}

		return eh.handleRouteCreated(event)

	case "DeliveryStarted":
		var event DeliveryStartedEvent
		err := json.Unmarshal(msg, &event)
		if err != nil {
			return fmt.Errorf("error unmarshalling event: %w", err)
		}
	default:
		return errors.New("unknown event")
	}

	return nil
}

func (eh *EventHub) handleRouteCreated(event RouteCreatedEvent) error {
	_, err := RouteCreatedHandler(&event, eh.routeService)
	if err != nil {
		return err
	}

	// TODO - publicar no Kafka
	return nil
}

func (eh *EventHub) handleDeliveryStarted(event DeliveryStartedEvent) error {

}
