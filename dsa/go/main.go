package main

import "fmt"

func main() {
	boardExist := Board(
		[][]byte{
			{'A', 'B', 'C', 'E'},
			{'S', 'F', 'C', 'S'},
			{'A', 'D', 'E', 'E'},
		}, "ABCB")

	fmt.Println(boardExist)
}
