package main

func main() {
	x := 1
	y := 2

	// Apenas postfix
	x++ // x += 1 ou x = x + 1
	println(x)

	y-- // y -= 1 ou y = y - 1
	println(y)

	// fmt.Println(x == y--) // Não pode
}
