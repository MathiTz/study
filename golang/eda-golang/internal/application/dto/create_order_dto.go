package dto

type CreateOrderDTO struct {
	Items []Item `json:items`
}

type Item struct {
	ProductId string `json:"product_id"`
	Qtd       int    `json:"qtd"`
}
