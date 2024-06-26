package entity

type OrderItemEntity struct {
	productName  string
	productPrice float64
	quantity     int
}

func NewOrderItemEntity(productName string, productPrice float64, quantity int) *OrderItemEntity {
	return &OrderItemEntity{
		productName:  productName,
		productPrice: productPrice,
		quantity:     quantity,
	}
}

// getters
func (o *OrderItemEntity) GetProductName() string {
	return o.productName
}

func (o *OrderItemEntity) GetProductPrice() float64 {
	return o.productPrice
}

func (o *OrderItemEntity) GetQuantity() int {
	return o.quantity
}
