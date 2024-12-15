package main

// Iniciando com letra maiúscula é PÚBLICO (visível dentro e fora do pacote)!
// Iniciando com letra minúscula é PRIVADO (visível apenas dentro do pacote)!
// Por exemplo...
// Ponto - gerará algo público
// ponto ou _Ponto - gerará algo privado

import (
	"math"
)

// Ponto representa uma coordenada no plano cartesiano
type Ponto struct {
	x float64
	y float64
}

func catetos(a, b Ponto) (cx, cy float64) {
	cx = math.Abs(a.x - b.x)
	cy = math.Abs(a.y - b.y)
	return
}

// Distancia é responsável por calcular a distância linear entre dois pontos
func Distancia(a, b Ponto) float64 {
	cx, cy := catetos(a, b)
	return math.Sqrt(math.Pow(cx, 2) + math.Pow(cy, 2))
}
