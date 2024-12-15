package main

import "fmt"

func main() {
	array1 := [3]int{1, 2, 3}
	var slice1 []int

	// array1 = append(array1, 4, 5, 6) // não é possível adicionar elementos a um array
	slice1 = append(slice1, 4, 5, 6) // slice aceita novos elementos
	fmt.Println(array1, slice1)

	slice2 := make([]int, 2)
	copy(slice2, slice1) // copia o conteúdo de slice1 para slice2
	fmt.Println(slice2)
}
