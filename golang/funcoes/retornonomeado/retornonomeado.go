package main

import "fmt"

func trocar(p1, p2 int) (segundo, primeiro int) {
	segundo = p2
	primeiro = p1
	return // retorno limpo
}

func main() {
	x, y := trocar(2, 3)
	fmt.Println(x, y) // 3 2

	segundo, primeiro := trocar(7, 1)
	fmt.Println(segundo, primeiro) // 1 7
}
