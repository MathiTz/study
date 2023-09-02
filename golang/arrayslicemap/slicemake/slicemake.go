package main

import "fmt"

func main() {
	s := make([]int, 10) // cria um slice com 10 posições
	s[9] = 12
	fmt.Println(s)

	s = make([]int, 10, 20) // cria um slice com 10 posições e um array interno com 20 posições
	fmt.Println(s, len(s), cap(s))

	s = append(s, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0) // adiciona elementos ao slice
	fmt.Println(s, len(s), cap(s))

	s = append(s, 1) // adiciona um elemento ao slice
	fmt.Println(s, len(s), cap(s))
}
